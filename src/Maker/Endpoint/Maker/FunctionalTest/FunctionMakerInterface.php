<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

interface FunctionMakerInterface
{
    public function isNeedToRun(Manifest $manifest): bool;
    public function run(Manifest $manifest, PhpNamespace $namespace, ClassType $class): void;
}
