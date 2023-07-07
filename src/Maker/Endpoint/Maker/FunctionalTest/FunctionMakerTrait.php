<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest;

use Nette\PhpGenerator\Method;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

trait FunctionMakerTrait
{
    private function printEndpointCall(
        Manifest $manifest,
        Method $method,
        ?\Closure $requestIsVariable = null,
        bool $responseIsVariable = true
    ): void {
        $request = '$this->getRequest()';
        if (null !== $requestIsVariable) {
            $request = '$request';
            $method->addBody('$request = $this->getRequest();');
            $requestIsVariable();
            $method->addBody('');
        }
        $response = $responseIsVariable ? '$response = ' : '';
        $methodName = 'send' . ucfirst(strtolower($manifest->method));

        $method->addBody($response . '$this->' . $methodName . '(?, ' . $request . ');', [$manifest->getUrl()]);
    }
}
