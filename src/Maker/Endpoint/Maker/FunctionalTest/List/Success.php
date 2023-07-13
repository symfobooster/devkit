<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\List;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\FunctionMakerInterface;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\FunctionMakerTrait;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

class Success implements FunctionMakerInterface
{
    use FunctionMakerTrait;

    public function isNeedToRun(Manifest $manifest): bool
    {
        return $manifest->type === Manifest::TYPE_LIST;
    }

    public function run(Manifest $manifest, PhpNamespace $namespace, ClassType $class): void
    {
        $method = $class->addMethod('testSuccess')
            ->setReturnType('void');

        $this->printEndpointCall($manifest, $method);
        $method->addBody('$this->checkSuccess();');

        $this->addCheckStructure($manifest, $method);

        $method->addBody('$this->assertEquals(?, array_column($response[\'items\'], \'name\'));', [['name1', 'name2']]);
    }

    private function addCheckStructure(Manifest $manifest, Method $method): void
    {
        $fields = [];
        if (!empty($manifest->output) && !empty($manifest->output->fields)) {
            foreach ($manifest->output->fields as $field) {
                $fields[$field->name] = $field->type;
            }
        }
        $method->addBody('');
        $method->addBody('$this->checkListStructure(2, 2, ?);', [$fields]);
    }
}
