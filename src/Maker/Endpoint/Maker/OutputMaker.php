<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker;

use Nette\PhpGenerator\ClassType;
use Symfobooster\Base\Output\Attributes\SuccessMarker;
use Symfobooster\Devkit\Maker\AbstractMaker;
use Symfobooster\Devkit\Maker\Endpoint\ClassMaker;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Field;
use Symfobooster\Devkit\Output\OutputInterface;

class OutputMaker extends AbstractMaker
{
    public function make(): void
    {
        $generator = new ClassMaker(
            'App\\' . ucfirst($this->manifest->controller) . '\\' . ucfirst(
                $this->manifest->action
            ) . '\\Output',
        );
        $generator->getNamespace()->addUse(SuccessMarker::class);

        $class = $generator->getClass();
        $class->addAttribute(SuccessMarker::class);
        $this->addProperties($class);

        $this->storage->set('outputClass', $generator->getNamespace()->getName() . '\\' . $class->getName());
        $this->fileStorage->addFile('/src' . $generator->getPath(), $generator->getContent());
    }

    private function addProperties(ClassType $class): void
    {
        if (empty($this->manifest->output)) {
            return;
        }
        /** @var Field $field */
        foreach ($this->manifest->output->fields as $field) {
            $class->addProperty($field->name)
                ->setType($field->type);
        }
    }
}
