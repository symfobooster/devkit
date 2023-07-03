<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\AbstractMaker;
use Symfobooster\Devkit\Maker\Endpoint\ClassMaker;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Field;
use Symfobooster\Devkit\Tester\ClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FunctionalTestMaker extends AbstractMaker
{
    private Dumper $dumper;

    public function make(): void
    {
        $this->dumper = new Dumper();
        $generator = new ClassMaker(
            'Tests\\Functional\\' . ucfirst($this->manifest->controller) . '\\' . ucfirst(
                $this->manifest->action
            ) . 'Test',
            WebTestCase::class
        );
        $class = $generator->getClass();
        $namespace = $generator->getNamespace();
        $namespace->addUse(WebTestCase::class);

        $namespace->addUse(ClientTrait::class);
        $class->addTrait(ClientTrait::class);

        $this->addRequiredTest($namespace, $class);

        $this->fileStorage->addFile('/' . lcfirst($generator->getPath()), $generator->getContent());
    }

    public function getDataExample(Field $field): mixed
    {
        $examples = [
            'int' => 108,
            'string' => "'Foobar'",
            'bool' => 'true',
            'array' => '[]',
        ];

        return array_key_exists($field->type, $examples) ? $examples[$field->type] : 'Example';
    }

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
            ->setReturnType('void');
        $test->setComment('@dataProvider getRequiredFields');
        $test->addParameter('field')->setType('string');

        $provider = $class->addMethod('getRequiredFields');
        $provider->setReturnType('array');
        $provider->setBody('return ' . $this->dumper->dump($fields) . ';');
    }
}
