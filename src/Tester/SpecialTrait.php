<?php

namespace Symfobooster\Devkit\Tester;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @mixin BrowserTrait
 * @mixin WebTestCase
 */
trait SpecialTrait
{
    protected function checkNotAvailableMethods(string $except, string $uri, array $data = []): void
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
        ], $this->content);
        $this->assertEquals($total, $this->content['total']);
        $this->assertCount($count, $this->content['items']);
        $this->checkStructureInList($structure, $this->content['items']);
    }
}
