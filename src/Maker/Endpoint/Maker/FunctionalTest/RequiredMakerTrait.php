<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTestMaker;

/**
 * @mixin FunctionalTestMaker
 */
trait RequiredMakerTrait
{
    private function addRequiredTest(PhpNamespace $namespace, ClassType $class): void
    {
        if (empty($this->manifest->input) || empty($this->manifest->input->fields)) {
            return;
        }
        $fields = [];
        foreach ($this->manifest->input->fields as $field) {
            if (false === $field->required) {
                continue;
            }
            $fields[] = $field->name;
        }
        if (empty($fields)) {
            return;
        }
        $test = $class->addMethod('testRequired')
            ->setReturnType('void')
            ->setComment('@dataProvider getRequiredFields');
        $test->addParameter('field')->setType('string');
        $this->printEndpointCall($test, function () use ($test) {
            $test->addBody('unset($request[$field]);');
        }, false);
        $test->addBody('$this->checkNotValid([$field]);');

        $provider = $class->addMethod('getRequiredFields');
        $provider->setReturnType('array');
        $provider->setBody('return ' . $this->dumper->dump($fields) . ';');
    }
}
