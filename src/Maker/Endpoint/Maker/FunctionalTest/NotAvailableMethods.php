<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

class NotAvailableMethods implements FunctionMakerInterface
{
    public function isNeedToRun(Manifest $manifest): bool
    {
        return true;
    }

    public function run(Manifest $manifest, PhpNamespace $namespace, ClassType $class): void
    {
        $class->addMethod('testNotAvailableMethods')
            ->setReturnType('void')
            ->setBody('$this->checkNotAvailableMethods(?, ?, $this->getRequest());', [$manifest->method, $manifest->getUrl()]);
    }
}
