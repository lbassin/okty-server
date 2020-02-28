<?php

namespace App\Behat\Context;

use App\Kernel;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use InvalidArgumentException;
use Symfony\Component\HttpClient\HttpClient;
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
}
