<?php

namespace Symfobooster\Devkit\Maker;

class File
{
    public readonly string $name;
    public readonly string $path;
    public readonly string $type;

    public function __construct(
        public readonly string $fullName,
        public readonly mixed $content,
    ) {
        $info = pathinfo($this->fullName);
        $this->path = $info['dirname'];
        $this->name = $info['filename'];
        $this->type = $info['extension'];
    }
}
