<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker;

use Nette\PhpGenerator\PhpFile;
use Symfobooster\Devkit\Maker\AbstractMaker;
use Symfobooster\Devkit\Maker\Endpoint\ClassMaker;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Field;
use Symfobooster\Devkit\Output\OutputInterface;

class OutputMaker extends AbstractMaker
{
    public function make(): void
    {
        $generator = new ClassMaker(
            'App\\Domain\\' . ucfirst($this->manifest->controller) . '\\' . ucfirst($this->manifest->action) . '\\Output',
            $this->manifest->output->getExtendClass()
        );

        $class = $generator->getClass();
        $class->setExtends($this->manifest->output->getExtendClass());


        $class->addMethod('getData')
            ->setReturnType('array|object|string|null')
            ->addBody('return [];');

        /** @var Field $field */
        foreach ($this->manifest->output->fields as $field) {
            $class->addProperty($field->name)
                ->setType($field->type)
                ->setPrivate();
        }

        $this->storage->set('outputClass', $class->getName());

        $this->writeClassFile($generator->getPath(), $generator->getContent());
    }
}
