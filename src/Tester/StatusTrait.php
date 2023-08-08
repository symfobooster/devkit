<?php

namespace Symfobooster\Devkit\Tester;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @mixin BrowserTrait
 * @mixin WebTestCase
 */
trait StatusTrait
{
    protected function checkSuccess(): void
    {
        $this->checkStatus(200);
        $this->assertIsArray($this->content);
    }

    protected function checkLogic(string $message): void
    {
        $this->checkStatus(400); // TODO check this code
        $this->assertEquals($message, $this->content['message']);
    }

    protected function checkNotValid(array $fields): void
    {
        $this->checkStatus(400);
        $this->assertCount(count($fields), $this->content['fields']);
        foreach ($fields as $field) {
            $this->assertArrayHasKey($field, $this->content['fields']);
        }
    }

    protected function checkNotFound(): void
    {
        $this->checkStatus(404);
    }

    protected function checkNoContent(): void
    {
        $this->checkStatus(204);
    }

    private function checkStatus(int $status): void
    {
        $statusCode = $this->browser->getResponse()->getStatusCode();
        if ($status !== $statusCode) {
            print_r($this->content);
        }
        $this->assertEquals($status, $this->browser->getResponse()->getStatusCode());
    }

    protected function checkMethodNotAllowed(): void
    {
        $this->checkStatus(405);
    }

    protected function checkUnauthorized(): void
    {
        $this->checkStatus(401);
    }

    protected function checkConflict(): void
    {
        $this->checkStatus(409);
    }

    protected function checkAccessDenied(): void
    {
        $this->checkStatus(403);
    }
}
