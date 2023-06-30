<?php

namespace Symfobooster\Devkit\Maker;

class FileStorage
{
    /** @var File[] */
    private array $files = [];

    public function addFile(string $path, string $content): self
    {
        if ($this->hasFile($path)) {
            throw new \Exception('File `' . $path . '` is already exists in list');
        }

        $this->files[] = new File($path, $content);

        return $this;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function hasFile(string $path): bool
    {
        foreach ($this->files as $file) {
            if ($file->fullName === $path) {
                return true;
            }
        }

        return false;
    }
}
