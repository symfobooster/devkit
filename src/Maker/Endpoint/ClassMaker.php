<?php

namespace Symfobooster\Devkit\Maker\Endpoint;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;

class ClassMaker
{
    private PhpFile $file;
    private PhpNamespace $namespace;
    private ClassType $class;

    public function __construct(string $classId, ?string $extends = null)
    {
        $namespace = substr($classId, 0, strrpos($classId, '\\'));
        $className = substr($classId, (strrpos($classId, '\\') + 1));

        $this->file = new PhpFile;
        $this->file->setStrictTypes();

        $this->namespace = $this->file->addNamespace(
            $namespace
        );

        $this->class = $this->namespace->addClass($className);

        if (!is_null($extends)) {
            $this->namespace->addUse($extends);
            $this->class->setExtends($extends);
        }
    }

    public function getFile(): PhpFile
    {
        return $this->file;
    }

    public function getNamespace(): PhpNamespace
    {
        return $this->namespace;
    }

    public function getClass(): ClassType
    {
        return $this->class;
    }

    public function getContent(): string
    {
        return (string)$this->file;
    }

    public function getPath(): string
    {
        $namespace = str_replace('\\', '/', $this->namespace->getName());
        $namespace = str_replace('App/', '/', $namespace);

        return $namespace . '/' . $this->class->getName() . '.php';
    }

    public function addUse(string $class): self
    {
        $this->namespace->addUse($class);

        return $this;
    }
}