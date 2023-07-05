<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest;

use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTestMaker;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Field;

/**
 * @mixin FunctionalTestMaker
 */
trait DataExampleTrait
{
    private array $examplesByName = [
        'name' => 'Alan',
        'surname' => 'Turing',
        'email' => 'email@example.com',
        'price' => 1000,
        'address' => '10911 Chalon Rd, Los Angeles, CA 90077, USA',
        'image' => 'https://emaple.com/image.png',
        'avatar' => 'https://emaple.com/avatar.png',
        'picture' => 'https://emaple.com/picture.png',
    ];

    private function getDataExample(Field $field): mixed
    {
        $examples = [
            'int' => 108,
            'float' => 1.08,
            'string' => 'FooBar',
            'bool' => true,
            'array' => [],
        ];

        $byName = array_key_exists($field->name, $this->examplesByName) ? $this->examplesByName[$field->name] : null;

        return null === $byName && !empty($field->type) ? $examples[$field->type] : 'Example';
    }
}
