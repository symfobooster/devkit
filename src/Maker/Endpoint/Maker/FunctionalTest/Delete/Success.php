<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\Delete;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\FunctionMakerInterface;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\FunctionMakerTrait;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;
use Symfobooster\Devkit\Tester\DataBaseTrait;

class Success implements FunctionMakerInterface
{
    use FunctionMakerTrait;

    public function isNeedToRun(Manifest $manifest): bool
    {
        return $manifest->type === Manifest::TYPE_DELETE;
    }

    public function run(Manifest $manifest, PhpNamespace $namespace, ClassType $class): void
    {
        $namespace->addUse(DataBaseTrait::class);
        $class->addTrait(DataBaseTrait::class);
        $method = $class->addMethod('testSuccess')
            ->setReturnType('void');

        $this->printEndpointCall($manifest, $method, function () {
        }, false);
        $method->addBody('$this->checkNoContent();');

        $method->addBody('$this->dontSeeRecord(ChangeEntity::class, [\'id\' => $request[\'id\']);');
        // TODO set entity from manifest
    }
}
