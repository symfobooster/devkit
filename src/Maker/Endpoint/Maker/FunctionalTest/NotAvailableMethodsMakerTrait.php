<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTestMaker;

/**
 * @mixin FunctionalTestMaker
 */
trait NotAvailableMethodsMakerTrait
{
    private function addNotAvailableMethodsTest(PhpNamespace $namespace, ClassType $class): void
    {
        $class->addMethod('testNotAvailableMethods')
            ->setReturnType('void')
            ->setBody('$this->checkNotAvailableMethods(?, ?);', [$this->manifest->method, $this->url]);
    }
}
