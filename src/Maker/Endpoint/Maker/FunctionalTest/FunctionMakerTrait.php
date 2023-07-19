<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest;

use Nette\PhpGenerator\Method;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

trait FunctionMakerTrait
{
    private function printEndpointCall(
        Manifest $manifest,
        Method $method,
        ?\Closure $requestAsVariable = null,
        bool $contentAsVariable = true
    ): void {
        $request = '$this->getRequest()';
        if (null !== $requestAsVariable) {
            $request = '$request';
            $method->addBody('$request = $this->getRequest();');
            $requestAsVariable();
            $method->addBody('');
        }
        $response = $contentAsVariable ? '$content = ' : '';
        $methodName = 'send' . ucfirst(strtolower($manifest->method));

        $method->addBody($response . '$this->' . $methodName . '(?, ' . $request . ');', [$manifest->getUrl()]);
    }
}
