<?php

namespace Symfobooster\Devkit\Tester;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @mixin WebTestCase
 */
trait BrowserTrait
{
    use StatusTrait;
    use StructureTrait;
    use SpecialTrait;

    private KernelBrowser $browser;
    private mixed $response;
    private ?array $content;
    private array $cookies = [];
    private array $headers = [];
    private array $files = [];

    protected function withCookie(string $key, string $value): self
    {
        $this->cookies[$key] = $value;

        return $this;
    }

    protected function withoutCookie(string $key): self
    {
        if (array_key_exists($key, $this->cookies)) {
            unset($this->cookies[$key]);
        }

        return $this;
    }

    protected function withHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    protected function withoutHeader(string $key): self
    {
        if (array_key_exists($key, $this->headers)) {
            unset($this->headers[$key]);
        }

        return $this;
    }

    protected function withFile(string $key, UploadedFile $value): self
    {
        $this->files[$key] = $value;

        return $this;
    }

    protected function withoutFile(string $key): self
    {
        if (array_key_exists($key, $this->files)) {
            unset($this->files[$key]);
        }

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
        $browser->setServerParameter('HTTP_USER_AGENT', 'Symfobooster test process');
        $browser->request($method, $url, [], $this->files, $headers, json_encode($data));

        $response = $browser->getResponse();
        $this->content = json_decode($response->getContent(), true);
        $this->cookies = [];
        $this->headers = [];
        $this->files = [];

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

    protected function setValueByKeyString(array &$array, string $keyString, mixed $value): void
    {
        $link = &$array;
        $fields = explode('.', $keyString);
        foreach ($fields as $index => $key) {
            if ((count($fields) - 1) === $index) {
                $link[$key] = $value;
            } else {
                $link = &$link[$key];
            }
        }
    }
}
