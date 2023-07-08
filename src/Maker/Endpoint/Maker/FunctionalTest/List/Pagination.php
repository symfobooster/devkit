<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\List;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\FunctionMakerInterface;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\FunctionMakerTrait;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

class Pagination implements FunctionMakerInterface
{
    use FunctionMakerTrait;

    public function isNeedToRun(Manifest $manifest): bool
    {
        return $manifest->type === Manifest::TYPE_LIST;
    }

    public function run(Manifest $manifest, PhpNamespace $namespace, ClassType $class): void
    {
        $method = $class->addMethod('testPagination')
            ->setReturnType('void');

        $method->addBody('$this->checkPagination(?, ?, ?, ?);', [$manifest->getUrl(), 'name', ['test1'], ['test2']]);
    }
}
