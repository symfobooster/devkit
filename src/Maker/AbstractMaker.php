<?php

namespace Symfobooster\Devkit\Maker;

use Nette\PhpGenerator\PhpFile;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Yaml\Yaml;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

abstract class AbstractMaker implements MakerInterface
{
    protected InputInterface $input;
    protected ConsoleStyle $io;
    protected Generator $generator;
    protected Manifest $manifest;
    protected Storage $storage;

    public function __construct(
        InputInterface $input,
        ConsoleStyle $io,
        Generator $generator,
        Manifest $manifest,
        Storage $storage
    ) {
        $this->input = $input;
        $this->io = $io;
        $this->generator = $generator;
        $this->manifest = $manifest;
        $this->storage = $storage;
    }

    protected function readYamlFile(string $path): ?array
    {
        if (file_exists($path)) {
            $yaml = file_get_contents($path);
            return Yaml::parse($yaml);
        }

        return null;
    }

    protected function writeYamlFile(string $path, array $data): void
    {
        $directory = dirname($path);
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($path, Yaml::dump($data, 20, 2, Yaml::DUMP_OBJECT));
    }

    protected function readConfigFile(string $path): ?array
    {
        return $this->readYamlFile($this->generator->getRootDirectory() . '/config' . $path);
    }

    protected function writeConfigFile(string $path, array $config): void
    {
        $this->writeYamlFile($this->generator->getRootDirectory() . '/config' . $path, $config);
    }

    protected function writeClassFile(string $path, string $content): void
    {
        $realPath = $this->generator->getRootDirectory() . '/src' . $path;

        $directory = dirname($realPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($realPath, $content);
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
