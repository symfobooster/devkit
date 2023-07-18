<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\Delete;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\FunctionMakerInterface;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\FunctionMakerTrait;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

class NotFound implements FunctionMakerInterface
{
    use FunctionMakerTrait;

    public function isNeedToRun(Manifest $manifest): bool
    {
        return $manifest->type === Manifest::TYPE_DELETE;
    }

    public function run(Manifest $manifest, PhpNamespace $namespace, ClassType $class): void
    {
        $method = $class->addMethod('testNotFound')
            ->setReturnType('void');

        $this->printEndpointCall($manifest, $method, null, false);
        $method->addBody('$this->checkNotFound();');
        $method->setBody(
            str_replace(
                '$this->getRequest()',
                "['id' => '7b151408-0b71-4025-a392-b88d16cf4e3f']",
                $method->getBody()
            )
        );
    }
}
