<?php

namespace App\Behat\Context;

use App\Kernel;
use App\ValueObject\Json;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
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

    /**
     * @Given /^I have the payload$/
     */
    public function iHaveThePayload(PyStringNode $payload)
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
    public function theResponseShouldBeReceived()
    {
        if (empty($this->response)) {
            throw new \Exception('There is no reponse');
        }
    }

    /**
     * @Then /^display the response$/
     */
    public function displayTheResponse()
    {
        dump($this->response->getContent());
    }

    /**
     * @Then /^the response should contain (\d+) (\w+)$/
     */
    public function theResponseShouldContain(int $count, string $key)
    {
        $content = $this->decodeResponse();

        if (!isset($content[$key])) {
            throw new \Exception(sprintf('The key %s in not set in the response', $key));
        }

        $countResponse = count($content[$key]);
        if ($countResponse !== $count) {
            throw new \Exception(sprintf('%s contains %d values, %d expected', $key, $countResponse, $count));
        }
    }

    /**
     * @Then /^the version should be greater than (\d+(.\d+)?)$/
     */
    public function theVersionShouldBeGreaterThan($expectedVersion)
    {
        $content = $this->decodeResponse();

        if (!isset($content['version'])) {
            throw new \Exception('No version set');
        }

        if ($content['version'] < $expectedVersion) {
            throw new \Exception(
                sprintf('Version is %s, expected greater than %s', $content['version'], $expectedVersion)
            );
        }
    }
}
