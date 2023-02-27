<?php

namespace Zabachok\Symfobooster\Maker\Endpoint\Manifest;

class Field
{
    public string $name;
    public string $type = 'string';
    public string $source = 'query';
    public bool $muted = false;
    public bool $required = true;
    public ?string $renamed = null;
}
