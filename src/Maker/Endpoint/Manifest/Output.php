<?php

namespace Zabachok\Symfobooster\Maker\Endpoint\Manifest;

use Zabachok\Symfobooster\Hydrator;
use Zabachok\Symfobooster\Output\Created;
use Zabachok\Symfobooster\Output\Success;

class Output
{
    private const KINDS = ['success', 'created'];
    private const EXTEND_CLASSES = [
        'success' => Success::class,
        'no-content' => Created::class
    ];

    public string $kind = 'success';
    /** @var Field[] */
    public array $fields = [];

    public function setFields(array $fields): void
    {
        $hydrator = new Hydrator();
        foreach ($fields as $key => $manifest) {
            /** @var Field $field */
            $field = $hydrator->hydrate(Field::class, $manifest ?? ['name' => $key]);
            $field->name = $key;
            if ($field->muted) {
                $this->hasMuted = true;
            }
            if ($field->renamed) {
                $this->hadRenamed = true;
            }
            $this->fields[] = $field;
        }
    }

    public function getExtendClass(): string
    {
        return self::EXTEND_CLASSES[$this->kind];
    }
}