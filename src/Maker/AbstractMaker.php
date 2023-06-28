<?php

namespace Symfobooster\Devkit\Maker;

use Nette\PhpGenerator\PhpFile;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractMaker implements MakerInterface
{
    protected InputInterface $input;
    protected SymfonyStyle $io;
    protected Manifest $manifest;
    protected Storage $storage;
    private string $projectDir;

    public function __construct(
        InputInterface $input,
        SymfonyStyle $io,
        Manifest $manifest,
        Storage $storage,
        string $projectDir
    ) {
        $this->input = $input;
        $this->io = $io;
        $this->manifest = $manifest;
        $this->storage = $storage;
        $this->projectDir = $projectDir;
    }

    protected function readConfigFile(string $path): ?array
    {
        return $this->readYamlFile($this->projectDir . '/config' . $path);
    }

    protected function readYamlFile(string $path): ?array
    {
        if (file_exists($path)) {
            $yaml = file_get_contents($path);
            return Yaml::parse($yaml);
        }

        return null;
    }

    protected function writeConfigFile(string $path, array $config): void
    {
        $this->writeYamlFile($this->projectDir . '/config' . $path, $config);
    }

    protected function writeYamlFile(string $path, array $data): void
    {
        $directory = dirname($path);
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($path, Yaml::dump($data, 20, 2, Yaml::DUMP_OBJECT));
    }

    protected function writeClassFile(string $path, string $content): void
    {
        $realPath = $this->projectDir . '/src' . $path;

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
