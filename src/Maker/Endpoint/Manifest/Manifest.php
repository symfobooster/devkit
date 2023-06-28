<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Manifest;

class Manifest
{
    public const
        TYPE_SIMPLE = 'default',
        TYPE_LIST = 'list',
        TYPE_VIEW = 'view',
        TYPE_CREATE = 'create',
        TYPE_UPDATE = 'update',
        TYPE_DELETE = 'delete';

    public string $type = 'default';
    public string $domain;
    public string $endpoint;
    public string $method = 'GET';
    public Service $service;
    public Input $input;
    public Output $output;
    public Special $special;

    public function __construct()
    {
        $this->output = new Output();
    }
}
