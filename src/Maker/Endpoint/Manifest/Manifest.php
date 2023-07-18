<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Manifest;

use Symfobooster\Base\Input\InputInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class Manifest implements InputInterface
{
    public const
        TYPE_DEFAULT = 'default',
        TYPE_LIST = 'list',
        TYPE_VIEW = 'view',
        TYPE_CREATE = 'create',
        TYPE_UPDATE = 'update',
        TYPE_DELETE = 'delete';

    public string $type = 'default';
    public string $controller;
    public string $action;
    public string $method = 'GET';
    public Service $service;
    public Input $input;
    public Output $output;
    public Special $special;

    public static function getValidators(): Constraint
    {
        return new Assert\Collection([
            'type' => [
                new Assert\Optional([
                    new Assert\Type('string'),
                    new Assert\Choice(self::getTypes()),
                ]),
            ],
            'controller' => [
                new Assert\Required(),
                new Assert\Regex('|^[\w\-\_]+$|'),
            ],
            'action' => [
                new Assert\Required(),
                new Assert\Regex('|^[\w\-\_]+$|'),
            ],
            'method' => [
                new Assert\Optional([
                    new Assert\Type('string'),
                    new Assert\Choice(self::getMethods()),
                ]),
            ],
            'input' => [
                new Assert\Optional([
                    Input::getValidators(),
                ]),
            ],
            'service' => [
                new Assert\Optional([
                    Service::getValidators(),
                ]),
            ],
            'output' => [
                new Assert\Optional([
                    Output::getValidators(),
                ]),
            ],
        ]);
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_DEFAULT,
            self::TYPE_LIST,
            self::TYPE_VIEW,
            self::TYPE_CREATE,
            self::TYPE_UPDATE,
            self::TYPE_DELETE,
        ];
    }

    public static function getMethods(): array
    {
        return ['GET', 'POST', 'PUT', 'DELETE'];
    }

    public function getUrl(): string
    {
        return '/' . $this->controller . '/' . $this->action;
    }
}
