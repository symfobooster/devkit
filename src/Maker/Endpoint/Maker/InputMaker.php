<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Base\Input\Attributes\Muted;
use Symfobooster\Base\Input\Attributes\Renamed;
use Symfobooster\Base\Input\Attributes\Source;
use Symfobooster\Base\Input\InputInterface;
use Symfobooster\Devkit\Maker\AbstractMaker;
use Symfobooster\Devkit\Maker\Endpoint\ClassMaker;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Field;
use Symfony\Component\Validator\Constraint;

class InputMaker extends AbstractMaker
{
    public function make(): void
    {
        $generator = new ClassMaker(
            'App\\' . ucfirst($this->manifest->controller) . '\\' . ucfirst($this->manifest->action) . '\\Input'
        );
        $class = $generator->getClass();
        $namespace = $generator->getNamespace();

        $this->prepareClass($class, $namespace);
        $this->addMethodGetValidators($class);
        $this->addProperties($class, $namespace);

        $this->storage->set('inputClass', $class->getName());
        $this->fileStorage->addFile($generator->getPath(), $generator->getContent());
    }

    private function prepareClass(ClassType $class, PhpNamespace $namespace): void
    {
        $class->addImplement(InputInterface::class);
        $namespace->addUse(InputInterface::class);
        $namespace->addUse(Constraint::class);
        $namespace->addUse('Symfony\Component\Validator\Constraints', 'Assert');
    }

    private function addMethodGetValidators(ClassType $class): void
    {
        $method = $class->addMethod('getValidators')
            ->setStatic()
            ->setReturnType(Constraint::class);

        $method->addBody('return new Assert\Collection([');
        if (!empty($this->manifest->input)) {
            foreach ($this->manifest->input->fields as $field) {
                $method->addBody($this->printFieldValidators($field));
            }
        }
        $method->addBody(']);');
    }

    private function addProperties(ClassType $class, PhpNamespace $namespace): void
    {
        if (empty($this->manifest->input)) {
            return;
        }

        foreach ($this->manifest->input->fields as $field) {
            $property = $class->addProperty($field->name);
            if (!empty($field->type)) {
                $property->setType($field->type);
            }
            if (!empty($field->source) && !$this->isDefaultSource($field->source)) {
                $namespace->addUse(Source::class);
                $property->addAttribute(Source::class, [$field->source]);
            }
            if (!empty($field->renamed)) {
                $namespace->addUse(Renamed::class);
                $property->addAttribute(Renamed::class, [$field->renamed]);
            }
            if (!empty($field->muted) && $field->muted === true) {
                $namespace->addUse(Muted::class);
                $property->addAttribute(Muted::class);
            }
        }
    }

    private function isDefaultSource(string $source): bool
    {
        if ($source === 'query' && $this->manifest->method === 'GET') {
            return true;
        }
        if ($source === 'body' && $this->manifest->method === 'POST') {
            return true;
        }

        return false;
    }

    private function printFieldValidators(Field $field): string
    {
        $n = "\n";
        $t1 = "\t";
        $t2 = "\t\t";

        $result = $t1 . "'" . $field->name . "' => [" . $n;

        if ($field->required) {
            $result .= $t2 . 'new Assert\Required(),' . $n;
            $i = $t2;
        } else {
            $result .= $t2 . 'new Assert\Optional([' . $n;
            $i = "\t\t\t";
        }
        if (!empty($field->type)) {
            $result .= $i . "new Assert\Type('" . $field->type . "')," . $n;
            if ($field->type === 'string') {
                $result .= $i . "new Assert\Length(null, 1, 1000)," . $n;
            }
            if ($field->type === 'array') {
                $result .= $i . "new Assert\Count(null, 1, 100)," . $n;
            }
        }
        if ($this->manifest->method !== 'GET') {
            $result .= $i . 'new Assert\NotNull(),' . $n;
        }

        if (!$field->required) {
            $result .= $t2 . ']),' . $n;
        }

        $result .= $t1 . '],';

        return $result;
    }
}
