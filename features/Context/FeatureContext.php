<?php

namespace App\Behat\Context;

use App\Kernel;
use App\ValueObject\Json;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class FeatureContext implements Context
{
    /** @var Kernel */
    private $kernel;
    /** @var HttpClientInterface */
    private $client;
    /** @var ResponseInterface */
    private $response;
    /** @var string */
    private $payload = '';

    public function __construct(Kernel $kernel, string $baseUri)
    {
        $this->kernel = $kernel;
        $this->client = HttpClient::create(['base_uri' => $baseUri]);
    }

    private function decodeResponse(): array
    {
        $response = new Json($this->response->getContent());
        $content = base64_decode($response->getAsArray()['content']);

        return Yaml::parse($content);
    }

    private function getContainerInResponse(string $name): array
    {
        $content = $this->decodeResponse();

        if (!isset($content['containers'][$name])) {
            throw new \RuntimeException(sprintf('Container %s not found', $name));
        }

        return $content['containers'][$name];
    }

    /**
     * @Given /^I have the payload$/
     */
    public function iHaveThePayload(PyStringNode $payload): void
    {
        $this->payload = $payload->getRaw();
    }

    /**
     * @When /^I send a ([^"]*) request to "([^"]*)"$/
     */
    public function iSendARequestTo($method, $uri): void
    {
        $expectedMethods = ['GET', 'POST'];
        if (!in_array(strtoupper($method), $expectedMethods)) {
            throw new InvalidArgumentException(
                sprintf('%s is not a valid method, %s expected', $method, implode(',', $expectedMethods))
            );
        }

        $this->response = $this->client->request($method, $uri, ['body' => $this->payload]);
    }

    /**
     * @Then /^the response should be received$/
     */
    public function theResponseShouldBeReceived(): void
    {
        if (empty($this->response)) {
            throw new Exception('There is no reponse');
        }
    }

    /**
     * @Then /^display the response$/
     */
    public function displayTheResponse(): void
    {
        dump($this->response->getContent(false));
    }

    /**
     * @Then /^the response should contain (\d+) (\w+)$/
     */
    public function theResponseShouldContain(int $count, string $key): void
    {
        $content = $this->decodeResponse();

        if (!isset($content[$key])) {
            throw new Exception(sprintf('The key %s in not set in the response', $key));
        }

        $countResponse = count($content[$key]);
        if ($countResponse !== $count) {
            throw new Exception(sprintf('%s contains %d values, %d expected', $key, $countResponse, $count));
        }
    }

    /**
     * @Then /^the version should be greater than (\d+(.\d+)?)$/
     */
    public function theVersionShouldBeGreaterThan(string $expectedVersion): void
    {
        $content = $this->decodeResponse();

        if (!isset($content['version'])) {
            throw new Exception('No version set');
        }

        if ($content['version'] < $expectedVersion) {
            throw new Exception(
                sprintf('Version is %s, expected greater than %s', $content['version'], $expectedVersion)
            );
        }
    }

    /**
     * @Then /^the HTTP code in the response should be (\d+)$/
     */
    public function theHTTPCodeInTheResponseShouldBe(int $code): void
    {
        if ($code !== $this->response->getStatusCode()) {
            throw new Exception(
                sprintf('Response code is %d, %d expected', $this->response->getStatusCode(), $code)
            );
        }
    }

    /**
     * @Given the error message should be :message
     */
    public function theErrorMessageShouldBe(string $message): void
    {
        $content = json_decode($this->response->getContent(false), true);

        if ($message != $content['message']) {
            throw new Exception('Response message does not match');
        }
    }

    /**
     * @Given /^the container (.*) should have the tag (.*)$/
     */
    public function theContainerShouldHaveTheTag(string $containerName, string $expectedTag): void
    {
        $container = $this->getContainerInResponse($containerName);

        [, $tag] = explode(':', $container['image']);

        if ($tag != $expectedTag) {
            throw new Exception(sprintf('Tag %s excepted, got %s', $expectedTag, $tag));
        }
    }

    /**
     * @Given /^the container (.*) should have (.*) as build path$/
     */
    public function theContainerShouldHaveAsBuildPath(string $name, string $path): void
    {
        $container = $this->getContainerInResponse($name);

        if ($container['build'] != $path) {
            throw new Exception(sprintf('Build %s excepted, got %s', $path, $container['build']));
        }

        if (!empty($container['image'])) {
            throw new Exception('A container from build file should not have an image specified');
        }
    }

    /**
     * @Given /^the container (.*) should have (\d+) ports?$/
     */
    public function theContainerShouldHavePorts(string $name, int $portCount): void
    {
        $container = $this->getContainerInResponse($name);

        if ($portCount === 0 && isset($container['ports'])) {
            throw new Exception(sprintf(
                'Container %s is not supposed to have ports entry, %s found',
                $name,
                count($container['ports'])
            ));
        }

        if (count($container['ports'] ?? []) !== $portCount) {
            throw new Exception(sprintf(
                'Container %s should have %s ports, %s found',
                $name,
                count($container['ports']),
                $portCount
            ));
        }
    }

    /**
     * @Given /^the container (.*) should have his port (\d+) mapped to the (\d+) of the host$/
     */
    public function theContainerHavePortMappedToHost(string $name, int $expectedContainer, int $expectedHost): void
    {
        $container = $this->getContainerInResponse($name);

        foreach ($container['ports'] as $port) {
            $details = explode(':', $port);

            $responseIp = '';
            [$responseHost, $responseContainer] = $details;
            if (count($details) === 3) {
                [$responseIp, $responseHost, $responseContainer] = $details;
            }

            if ($responseContainer != $expectedContainer) {
                continue;
            }

            if ($responseHost != $expectedHost) {
                throw new Exception(sprintf(
                    'Port %d is supposed to be map to %d, got %d',
                    $expectedContainer,
                    $expectedHost,
                    $responseHost
                ));
            }

            return;
        }

        throw new Exception(sprintf('No mapping for port %s found', $expectedContainer));
    }

    /**
     * @Given /^the container (.*) should allows local traffic only on the host port (\d+)$/
     */
    public function theContainerShouldAllowsLocalTrafficOnlyOnTheHostPort(string $name, int $expectedHostPort): void
    {
        $container = $this->getContainerInResponse($name);

        foreach ($container['ports'] as $port) {
            $details = explode(':', $port);

            $responseIp = '';
            [$responseHost, $responseContainer] = $details;
            if (count($details) === 3) {
                [$responseIp, $responseHost, $responseContainer] = $details;
            }

            if ($expectedHostPort != $responseHost) {
                continue;
            }

            if ($responseIp === '127.0.0.1') {
                return;
            }
        }

        throw new Exception(sprintf(
            'Container %s should allows only local traffic on the host port %d',
            $name,
            $expectedHostPort
        ));
    }

    /**
     * @Given /^the container (.*) should allows all traffic on the host port (\d+)$/
     */
    public function theContainerShouldAllowsAllTrafficOnTheHostPort(string $name, int $expectedHostPort): void
    {
        $container = $this->getContainerInResponse($name);

        foreach ($container['ports'] as $port) {
            $details = explode(':', $port);

            $responseIp = '';
            [$responseHost, $responseContainer] = $details;
            if (count($details) === 3) {
                [$responseIp, $responseHost, $responseContainer] = $details;
            }

            if ($expectedHostPort != $responseHost) {
                continue;
            }

            if (count($details) === 2 && $responseIp === '') {
                return;
            }
        }

        throw new Exception(sprintf(
            'Container %s should allows all traffic on the host port %d',
            $name,
            $expectedHostPort
        ));
    }

    /**
     * @Given /^the container (.+) should have (\d+) env$/
     */
    public function theContainerShouldHaveEnv(string $name, int $expectedEnvCount): void
    {
        $container = $this->getContainerInResponse($name);

        $envCount = count($container['environments'] ?? []);
        if ($envCount !== $expectedEnvCount) {
            throw new Exception(sprintf(
                'Container %s was excepted to have %d env, got %s',
                $name,
                $expectedEnvCount,
                $envCount
            ));
        }

    }

    /**
     * @Given /^the container (.+) should have the value set to "([^"]*)" for the env "([^"]*)"$/
     */
    public function theContainerShouldHaveTheValueSetToForTheEnv(string $name, string $value, string $key): void
    {
        $container = $this->getContainerInResponse($name);

        if (!isset($container['environments'][$key])) {
            throw new Exception(sprintf(
                'Container %s does not have env with key %s',
                $name,
                $key
            ));
        }

        $containerValue = $container['environments'][$key];
        if ($containerValue !== $value) {
            throw new Exception(sprintf(
                'Container %s is expected to have %s for %s, got %s',
                $name,
                $value,
                $key,
                $containerValue
            ));
        }
    }

    /**
     * @Given /^the container (.+) should have (\d+) volumes$/
     */
    public function theContainerShouldHaveVolumes(string $name, int $expectedVolumeCount): void
    {
        $container = $this->getContainerInResponse($name);

        $volumeCount = count($container['volumes'] ?? []);
        if ($volumeCount !== $expectedVolumeCount) {
            throw new Exception(sprintf(
                'Container %s should have %d volumes, found %d',
                $name,
                $expectedVolumeCount,
                $volumeCount
            ));
        }
    }

    /**
     * @Given /^the container (.+) should have the folder "(.+)" bound to the folder "(.+)" on the host$/
     */
    public function theContainerFolderShouldBeBoundToHost(string $name, string $containerPath, string $hostPath): void
    {
        $container = $this->getContainerInResponse($name);

        foreach ($container['volumes'] as $volume) {
            if (is_array($volume)) {
                continue;
            }

            [$hostInResponse, $containerInResponse] = explode(':', $volume);

            if ($hostInResponse !== $hostPath) {
                continue;
            }

            if ($containerInResponse === $containerPath) {
                return;
            }
        }

        throw new Exception(sprintf(
            'Container %s should have his folder %s bound to the host folder %s',
            $name,
            $containerPath,
            $hostPath ?? ''
        ));
    }

    /**
     * @Given /^the container (.+) should have the folder "(.+)" bound to the volume "(.+)"$/
     */
    public function theContainerFolderBoundToVolume(string $name, string $containerPath, string $volumeName): void
    {
        $container = $this->getContainerInResponse($name);

        foreach ($container['volumes'] as $volume) {
            if (!is_array($volume)) {
                continue;
            }

            if ($volume['source'] !== $volumeName) {
                continue;
            }

            if ($volume['target'] === $containerPath) {
                return;
            }
        }

        throw new Exception(sprintf(
            'Container %s should have his folder %s bound to the volume %s',
            $name,
            $containerPath,
            $volumeName
        ));
    }
}
