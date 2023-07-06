<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use PhpParser\Builder\Method;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTestMaker;

/**
 * @mixin FunctionalTestMaker
 */
trait SuccessMakerTrait
{
    private function addSuccessTest(PhpNamespace $namespace, ClassType $class): void
    {
        $method = $class->addMethod('testSuccess')
            ->setReturnType('void');

        $this->printEndpointCall($method);
        $method->addBody('$this->checkSuccess();');
        $method->addBody('');


    }

    private function addCheckStructure(Method $method): void
    {

    }
}
