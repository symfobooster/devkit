<?php

namespace Zabachok\Symfobooster\Maker\Endpoint\Maker;

use Nette\PhpGenerator\PhpFile;
use Zabachok\Symfobooster\Maker\AbstractMaker;
use Zabachok\Symfobooster\Maker\Endpoint\ClassMaker;
use Zabachok\Symfobooster\Maker\Endpoint\Manifest\Field;
use Zabachok\Symfobooster\Output\OutputInterface;

class OutputMaker extends AbstractMaker
{
    public function make(): void
    {
        $generator = new ClassMaker(
            'App\\Domain\\' . ucfirst($this->manifest->domain) . '\\' . ucfirst($this->manifest->endpoint) . '\\Output',
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