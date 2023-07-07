<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

class Success implements FunctionMakerInterface
{
    use FunctionMakerTrait;

    public function isNeedToRun(Manifest $manifest): bool
    {
        return true;
    }

    public function run(Manifest $manifest, PhpNamespace $namespace, ClassType $class): void
    {
        $method = $class->addMethod('testSuccess')
            ->setReturnType('void');

        $this->printEndpointCall($manifest, $method);
        $method->addBody('$this->checkSuccess();');
        $method->addBody('');
    }
}
