<?php

namespace Symfobooster\Devkit\Tester;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

/**
 * @mixin WebTestCase
 */
trait ClientTrait
{
    private KernelBrowser $client;
    private ?array $response;
    private array $cookies = [];
    private array $headers = [];

    protected function withCookie(string $key, string $value): self
    {
        $this->cookies[$key] = $value;

        return $this;
    }

    protected function withHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    protected function sendGet(string $url, array $data = []): ?array
    {
        $query = $data ? '?' . http_build_query($data) : '';

        return $this->send('GET', $url . $query, []);
    }

    private function send(string $method, string $url, array $data): ?array
    {
        $client = $this->getWebClient();
        foreach ($this->cookies as $key => $value) {
            $client->getCookieJar()->set(new Cookie($key, $value, (string)strtotime('+1 day')));
        }
        $client->request(
            $method,
            $url,
            [],
            [],
            array_merge(['CONTENT_TYPE' => 'application/json'], $this->headers),
            json_encode($data)
        );
        $response = $this->client->getResponse();
        $this->response = json_decode($response->getContent(), true);
        $this->cookies = [];
        $this->headers = [];

        return $this->response;
    }

    protected function getWebClient(): KernelBrowser
    {
        if (!isset($this->client)) {
            $this->client = static::createClient();
        }

        return $this->client;
    }

    protected function sendPut(string $url, array $data): ?array
    {
        return $this->send('PUT', $url, $data);
    }

    protected function sendPost(string $url, array $data): ?array
    {
        return $this->send('POST', $url, $data);
    }

    protected function checkSuccess(): void
    {
        $statusCode = $this->client->getResponse()->getStatusCode();
        if (200 !== $statusCode) {
            print_r($this->response);
        }
        $this->assertEquals(200, $statusCode);
    }

    protected function checkLogic(string $message): void
    {
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($message, $this->response['error']);
    }

    protected function checkNotValid(array $fields): void
    {
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        foreach ($fields as $field) {
            $this->assertArrayHasKey($field, $this->response['fields']);
        }
    }

    protected function checkNotFound(): void
    {
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
