<?php

namespace Symfobooster\Devkit\Tester;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @mixin ClientTrait
 * @mixin WebTestCase
 */
trait SpecialTrait
{
    protected function checkNotAllowedMethods(string $except, string $uri, array $data = []): void
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE'];
        foreach ($methods as $method) {
            if ($method === $except) {
                continue;
            }
            $this->sendRequest($method, $uri, $data);
            $this->checkMethodNotAllowed();
        }
    }

    protected function checkPagination(
        string $endpoint,
        string $field,
        array $firstPage,
        array $secondPage,
        array $filters = []
    ): void {
        $response = $this->sendGet(
            $endpoint,
            array_merge([
                'page' => 0,
                'pageSize' => 1,
            ], $filters)
        );
        $this->checkSuccess();
        $this->assertCount(1, $response['items']);
        $this->assertEquals($firstPage, array_column($response['items'], $field));

        $response = $this->sendGet(
            $endpoint,
            array_merge([
                'page' => 1,
                'pageSize' => 1,
            ], $filters)
        );
        $this->checkSuccess();
        $this->assertCount(1, $response['items']);
        $this->assertEquals($secondPage, array_column($response['items'], $field));
    }

    protected function checkListResponse(int $count, int $total, array $structure): void
    {
        $this->checkStructure([
            'items' => 'array',
            'total' => 'integer',
        ], $this->response);
        $this->assertEquals($total, $this->response['total']);
        $this->assertCount($count, $this->response['items']);
        $this->checkStructureInList($structure, $this->response['items']);
    }
}