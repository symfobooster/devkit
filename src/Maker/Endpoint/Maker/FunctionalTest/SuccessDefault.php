<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

class SuccessDefault implements FunctionMakerInterface
{
    use FunctionMakerTrait;

    public function isNeedToRun(Manifest $manifest): bool
    {
        return $manifest->type === Manifest::TYPE_SIMPLE;
    }

    public function run(Manifest $manifest, PhpNamespace $namespace, ClassType $class): void
    {
        $method = $class->addMethod('testSuccess')
            ->setReturnType('void');

        $this->printEndpointCall($manifest, $method, null, false);
        $method->addBody('$this->checkSuccess();');
    }
}
