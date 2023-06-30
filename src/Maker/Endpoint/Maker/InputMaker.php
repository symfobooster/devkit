<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker;

use Symfobooster\Base\Input\InputInterface;
use Symfobooster\Devkit\Maker\AbstractMaker;
use Symfobooster\Devkit\Maker\Endpoint\ClassMaker;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

class InputMaker extends AbstractMaker
{
    public function make(): void
    {
        $generator = new ClassMaker(
            'App\\' . ucfirst($this->manifest->controller) . '\\' . ucfirst($this->manifest->action) . '\\Input'
        );
        $class = $generator->getClass();
        $class->addImplement(InputInterface::class);
        $generator->getNamespace()->addUse(InputInterface::class);
        $generator->getNamespace()->addUse(Constraint::class);
        $generator->getNamespace()->addUse(Constraints::class, 'Assert');
        $class->addMethod('getValidators')
            ->setStatic()
            ->setReturnType(Constraint::class);

        $this->storage->set('inputClass', $class->getName());
        $this->fileStorage->addFile($generator->getPath(), $generator->getContent());
    }
}
