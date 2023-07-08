<?php

namespace Symfobooster\Devkit\Tester;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @mixin ClientTrait
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
        $this->assertEquals($message, $this->response['message']);
    }

    protected function checkNotValid(array $fields): void
    {
        $this->checkStatus(400);
        $this->assertCount(count($fields), $this->response['fields']);
        foreach ($fields as $field) {
            $this->assertArrayHasKey($field, $this->response['fields']);
        }
    }

    protected function checkNotFound(): void
    {
        $this->checkStatus(404);
    }

    private function checkStatus(int $status): void
    {
        $statusCode = $this->browser->getResponse()->getStatusCode();
        if ($status !== $statusCode) {
            print_r($this->response);
        }
        $this->assertEquals($status, $this->browser->getResponse()->getStatusCode());
    }
}
