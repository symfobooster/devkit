<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\List;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\FunctionMakerInterface;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\FunctionMakerTrait;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Field;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

class NotValid implements FunctionMakerInterface
{
    use FunctionMakerTrait;

    public function isNeedToRun(Manifest $manifest): bool
    {
        return $manifest->type === Manifest::TYPE_LIST;
    }

    public function run(Manifest $manifest, PhpNamespace $namespace, ClassType $class): void
    {
        $this->addTestNotValidMethod($manifest, $class);
        $this->addDataProvider($manifest, $class);
    }

    private function addTestNotValidMethod(Manifest $manifest, ClassType $class): void
    {
        $method = $class->addMethod('testNotValid')
            ->setReturnType('void')
            ->setComment('@dataProvider getNotValidFields');
        $method->addParameter('field')->setType('string');
        $method->addParameter('value')->setType('mixed');

        $this->printEndpointCall($manifest, $method, function () use ($method) {
            $method->addBody('$request[$field] = $value;');
        }, false);
        $method->addBody('$this->checkNotValid([$field]);');
    }

    private function addDataProvider(Manifest $manifest, ClassType $class)
    {
        $method = $class->addMethod('getNotValidFields')
            ->setReturnType('array');

        $values = [];
        if (!empty($manifest->input) && !empty($manifest->input->fields)) {
            foreach ($manifest->input->fields as $field) {
                $values = array_merge($values, $this->getValuesByField($field));
            }
        }
        $dumper = new Dumper();
        $method->setBody('return ' . $dumper->dump($values) . ';');
    }

    private function getValuesByField(Field $field): array
    {
        if (empty($field->type)) {
            return [[$field->name, '']];
        }

        switch ($field->type) {
            case 'string':
                return $this->getValuesForString($field);
                break;
            case 'int':
                return $this->getValuesForInteger($field);
                break;
        }

        return [[$field->name, '']];
    }

    private function getValuesForString(Field $field): array
    {
        return [
            [$field->name, ''],
            [$field->name, new Literal('str_repeat(\'x\', 101)')],
        ];
    }

    private function getValuesForInteger(Field $field): array
    {
        return [
            [$field->name, -1],
            [$field->name, 101],
        ];
    }
}
