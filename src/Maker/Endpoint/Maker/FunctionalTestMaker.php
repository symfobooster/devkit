<?php

namespace Zabachok\SymfoboosterDevkit\Maker\Endpoint\Maker;

use Zabachok\SymfoboosterDevkit\Maker\AbstractMaker;
use Zabachok\SymfoboosterDevkit\Maker\Endpoint\Manifest\Field;

class FunctionalTestMaker extends AbstractMaker
{

    public function make(): void
    {
        $serviceDetails = $this->generator->createClassNameDetails(
            $this->manifest->endpoint,
            'Tests\\Functional\\' . ucfirst($this->manifest->domain) . '\\',
            'Test'
        );

        $this->generator->generateClass(
            $serviceDetails->getFullName(),
            __DIR__ . '/templates/functional-test.tpl.php',
            [
                'maker' => $this,
                'manifest' => $this->manifest,
                'fields' => $this->manifest->input->fields,
                'input' => $this->manifest->input,
            ]
        );
    }

    public function getDataExample(Field $field): mixed
    {
        $examples = [
            'int' => 108,
            'string' => "'Foobar'",
            'bool' => 'true',
            'array' => '[]',
        ];

        return array_key_exists($field->type, $examples) ? $examples[$field->type] : 'Example';
    }
}
