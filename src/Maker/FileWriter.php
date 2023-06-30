<?php

namespace Symfobooster\Devkit\Maker;

use Symfony\Component\Yaml\Yaml;

class FileWriter
{
    private FileStorage $fileStorage;
    private string $projectDir;

    public function __construct(FileStorage $fileStorage, string $projectDir)
    {
        $this->fileStorage = $fileStorage;
        $this->projectDir = $projectDir;
    }

    public function writeFiles(): void
    {
        foreach ($this->fileStorage->getFiles() as $file) {
            switch ($file->type) {
                case 'yml':
                case 'yaml':
                    $this->writeYamlFile($file);
                    break;
                case 'php':
                    $this->writeClassFile($file);
                    break;
                default:
                    throw new \Exception('Undefined type "' . $file->type . '" for writing to ' . $file->fullName);
            }
        }
    }

    protected function writeYamlFile(File $file): void
    {
        if (!is_array($file->content)) {
            throw new \Exception('Content for yaml file must be an array but ' . gettype($file->content) . ' given');
        }
        $this->writeFile($file, Yaml::dump($file->content, 20, 2, Yaml::DUMP_OBJECT));
    }

    protected function writeClassFile(File $file): void
    {
        if (!is_string($file->content)) {
            throw new \Exception('Content for yaml file must be a string but ' . gettype($file->content) . ' given');
        }
        $this->writeFile($file);
    }

    private function writeFile(File $file, ?string $content = null): void
    {
        if (!is_dir($this->projectDir . $file->path)) {
            mkdir($this->projectDir . $file->path, 0755, true);
        }

        file_put_contents($this->projectDir . $file->fullName, $content ?? $file->content);
    }
}
