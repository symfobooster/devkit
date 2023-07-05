<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\Method;
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
        $this->addGetRequestMethod($namespace, $class);

        $this->fileStorage->addFile('/' . lcfirst($generator->getPath()), $generator->getContent());
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

    private function printEndpointCall(
        Method $method,
        ?\Closure $requestIsVariable = null,
        bool $responseIsVariable = true
    ): void {
        $request = '$this->getRequest()';
        if (null !== $requestIsVariable) {
            $request = '$request';
            $method->addBody('$request = $this->getRequest();');
            $requestIsVariable();
            $method->addBody('');
        }
        $response = $responseIsVariable ? '$response = ' : '';
        $methodName = 'send' . ucfirst(strtolower($this->manifest->method));
        $url = '/' . strtolower($this->manifest->controller) . '/' . strtolower($this->manifest->action);

        $method->addBody($response . '$this->' . $methodName . '(\'' . $url . '\', ' . $request . ');');
    }

    private function addGetRequestMethod(PhpNamespace $namespace, ClassType $class): void
    {
        $method = $class->addMethod('getRequest')
            ->setPrivate()
            ->setReturnType('array');

        $result = [];
        if (!empty($this->manifest->input) || !empty($this->manifest->input->fields)) {
            foreach ($this->manifest->input->fields as $field) {
                $result[$field->name] = $this->getDataExample($field);
            }
        }

        $method->setBody('return ' . $this->dumper->dump($result) . ';');
    }

    private function getDataExample(Field $field): mixed
    {
        $examples = [
            'int' => 108,
            'float' => 1.08,
            'string' => 'FooBar',
            'bool' => true,
            'array' => [],
        ];

        return array_key_exists($field->type ?? 'no', $examples) ? $examples[$field->type] : 'Example';
    }
}
