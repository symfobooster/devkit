<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Field;
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
            ->setComment('@dataProvider getRequiredFields');
        $test->addParameter('field')->setType('string');
        $this->printEndpointCall($manifest, $test, function () use ($test) {
            $test->addBody('unset($request[$field]);');
        }, false);
        $test->addBody('$this->checkNotValid([$field]);');

        $fields = array_column(
            array_filter($manifest->input->fields, function (Field $field) {
                return true === $field->required;
            }),
            'name'
        );
        $dumper = new Dumper();
        $provider = $class->addMethod('getRequiredFields');
        $provider->setReturnType('array');
        $provider->setBody('return ' . $dumper->dump($fields) . ';');
    }
}
