<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Manifest;

use Symfobooster\Base\Input\InputInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class Field implements InputInterface
{
//    public function __construct(string $name) {
//        $this->name = $name;
//    }
    public string $name;
    public string $type;
    public string $source;
    public bool $muted = false;
    public bool $required = true;
    public ?string $renamed = null;

    public static function getValidators(): Constraint
    {
        return new Assert\Collection([
            'name' => [
                new Assert\Optional([
                    new Assert\Type('string'),
                    new Assert\Regex('|^[\w_]+$|'),
                ]),
            ],
            'type' => [
                new Assert\Optional([
                    new Assert\Type('string'),
                    new Assert\Choice(self::getPhpTypes()),
                ]),
            ],
            'source' => [
                new Assert\Optional([
                    new Assert\Type('string'),
                    new Assert\Choice(self::getSources()),
                ]),
            ],
            'muted' => [
                new Assert\Optional([
                    new Assert\Type('boolean'),
                ]),
            ],
            'required' => [
                new Assert\Optional([
                    new Assert\Type('boolean'),
                ]),
            ],
            'renamed' => [
                new Assert\Optional([
                    new Assert\Type('string'),
                    new Assert\Regex('|^[\w_]+$|'),
                ]),
            ],
        ]);
    }

    public static function getPhpTypes(): array
    {
        return [
            'string',
            'integer',
            'array',
            'boolean',
            'float',
        ];
    }

    // TODO move it to another place
    public static function getSources(): array
    {
        return [
            'query',
            'body',
            'cookie',
            'path',
            'header',
        ];
    }
}
