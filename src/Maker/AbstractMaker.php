<?php

namespace Symfobooster\Devkit\Maker;

use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;
use Symfobooster\Devkit\Maker\Endpoint\ManifestLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * @property Manifest $manifest
 */
abstract class AbstractMaker implements MakerInterface
{
    protected Storage $storage;
    protected FileStorage $fileStorage;
    protected ManifestLoader $manifestLoader;

    public function __construct(
        ManifestLoader $manifestLoader,
        Storage $storage,
        FileStorage $fileStorage
    ) {
        $this->storage = $storage;
        $this->fileStorage = $fileStorage;
        $this->manifestLoader = $manifestLoader;
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

    public function __get(string $name)
    {
        if ($name === 'manifest') {
            if (empty($this->manifest)) {
                $this->manifest = $this->manifestLoader->getManifest();
            }

            return $this->manifest;
        }

        return null;
    }
}
