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
    use StatusTrait;
    use StructureTrait;

    private KernelBrowser $browser;
    private $response;
    private ?array $content;
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

    private function sendRequest(string $method, string $url, array $data): ?array
    {
        $browser = $this->createBrowser();

        foreach ($this->cookies as $key => $value) {
            $browser->getCookieJar()->set(new Cookie($key, $value, (string)strtotime('+1 day')));
        }
        // TODO authontification

        $headers = array_merge([
            'CONTENT_TYPE' => 'application/json',
        ], $this->headers);
//        $browser->setServerParameters('HTTP_USER_AGENT', 'Symfobooster test process');
        $browser->request($method, $url, [], [], $headers, json_encode($data));

        $response = $browser->getResponse();
        $this->content = json_decode($response->getContent(), true);
        $this->cookies = [];
        $this->headers = [];

        return $this->content;
    }

    protected function sendGet(string $url, array $data = []): ?array
    {
        $query = $data ? '?' . http_build_query($data) : '';

        return $this->sendRequest('GET', $url . $query, []);
    }

    protected function sendPut(string $url, array $data): ?array
    {
        return $this->sendRequest('PUT', $url, $data);
    }

    protected function sendPost(string $url, array $data): ?array
    {
        return $this->sendRequest('POST', $url, $data);
    }

    protected function sendDelete(string $url, array $data): ?array
    {
        $query = $data ? '?' . http_build_query($data) : '';

        return $this->sendRequest('DELETE', $url . $query, []);
    }

    private function createBrowser(): KernelBrowser
    {
        if (!isset($this->browser)) {
            $this->browser = static::createClient();
        }

        return $this->browser;
    }
}
