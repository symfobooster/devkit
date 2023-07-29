<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\PhpNamespace;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

class Required implements FunctionMakerInterface
{
    use FunctionMakerTrait;

    public function isNeedToRun(Manifest $manifest): bool
    {
        if (empty($manifest->input) || empty($manifest->input->fields)) {
            return false;
        }
        foreach ($manifest->input->fields as $field) {
            if (true === $field->required) {
                return true;
            }
        }

        return false;
    }

    public function run(Manifest $manifest, PhpNamespace $namespace, ClassType $class): void
    {
        $test = $class->addMethod('testRequired')
            ->setReturnType('void')
            ->addAttribute('DataProvider', ['getRequiredFields']);
        $namespace->addUse(DataProvider::class);
        $test->addParameter('field')->setType('string');
        $this->printEndpointCall($manifest, $test, function () use ($test) {
            $test->addBody('unset($request[$field]);');
        }, false);
        $test->addBody('$this->checkNotValid([$field]);');

        $fields = [];
        foreach ($manifest->input->fields as $field) {
            if($field->required === false) {
                continue;
            }
            $fields[] = [$field->name];
        }
        $dumper = new Dumper();
        $provider = $class->addMethod('getRequiredFields')
            ->setStatic()
            ->setReturnType('array');
        $provider->setBody('return ' . $dumper->dump($fields) . ';');
    }
}
