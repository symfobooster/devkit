<?php

namespace Symfobooster\Devkit\Maker;

use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractMaker implements MakerInterface
{
    protected Manifest $manifest;
    protected Storage $storage;
    protected FileStorage $fileStorage;

    public function __construct(
        Manifest $manifest,
        Storage $storage,
        FileStorage $fileStorage
    ) {
        $this->manifest = $manifest;
        $this->storage = $storage;
        $this->fileStorage = $fileStorage;
    }

    protected function readConfigFile(string $path): ?array
    {
        return $this->readYamlFile($this->storage->get('projectDir') . '/config' . $path);
    }

    protected function readYamlFile(string $path): ?array
    {
        $path = $this->storage->get('projectDir') . $path;
        if (file_exists($path)) {
            $yaml = file_get_contents($path);

            return Yaml::parse($yaml);
        }

        return null;
    }

    protected function getVariableByClass(string $class): string
    {
        return lcfirst($this->getNameByClass($class));
    }

    protected function getNameByClass(string $class): string
    {
        $pieces = explode('\\', $class);
        $className = end($pieces);

        return str_replace('Interface', '', $className);
    }
}
