<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\AbstractMaker;
use Symfobooster\Devkit\Maker\Endpoint\ClassMaker;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\DataExampleTrait;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\NotAvailableMethodsMakerTrait;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\RequiredMakerTrait;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\SuccessMakerTrait;
use Symfobooster\Devkit\Tester\ClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FunctionalTestMaker extends AbstractMaker
{
    use RequiredMakerTrait, DataExampleTrait, NotAvailableMethodsMakerTrait, SuccessMakerTrait;

    private Dumper $dumper;
    private string $url;

    public function make(): void
    {
        $this->dumper = new Dumper();
        $this->url = '/' . strtolower($this->manifest->controller) . '/' . strtolower($this->manifest->action);
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

        $this->addSuccessTest($namespace, $class);
        $this->addRequiredTest($namespace, $class);
        $this->addNotAvailableMethodsTest($namespace, $class);
        $this->addGetRequestMethod($namespace, $class);

        $this->fileStorage->addFile('/' . lcfirst($generator->getPath()), $generator->getContent());
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

        $method->addBody($response . '$this->' . $methodName . '(?, ' . $request . ');', [$this->url]);
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
}
