<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\List;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\DataExampleTrait;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\FunctionMakerInterface;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\FunctionMakerTrait;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

class Filtration implements FunctionMakerInterface
{
    use FunctionMakerTrait;
    use DataExampleTrait;

    public function isNeedToRun(Manifest $manifest): bool
    {
        return $manifest->type === Manifest::TYPE_LIST;
    }

    public function run(Manifest $manifest, PhpNamespace $namespace, ClassType $class): void
    {
        $this->addTestFiltrationMethod($manifest, $class);
        $this->addDataProvider($manifest, $class);
    }

    private function addTestFiltrationMethod(Manifest $manifest, ClassType $class): void
    {
        $method = $class->addMethod('testFiltration')
            ->setReturnType('void')
            ->setComment('@dataProvider getFiltrationFields');
        $method->addParameter('request')->setType('array');
        $method->addParameter('values')->setType('array');

        $this->printEndpointCall($manifest, $method);
        $method->addBody('$this->checkSuccess();');
        $method->addBody('$this->assertEquals($values, array_column($response[\'items\'], \'alias\'));');
        $method->setBody(str_replace('$this->getRequest()', '$request', $method->getBody()));
    }

    private function addDataProvider(Manifest $manifest, ClassType $class)
    {
        $method = $class->addMethod('getFiltrationFields')
            ->setReturnType('array');

        $values = [];
        if (!empty($manifest->input) && !empty($manifest->input->fields)) {
            foreach ($manifest->input->fields as $field) {
                $values = array_merge(
                    $values,
                    [[[$field->name => $this->getDataExample($field)], ['example1', 'example2']]]
                );
            }
        }
        $dumper = new Dumper();
        $method->setBody('return ' . $dumper->dump($values) . ';');
    }
}
