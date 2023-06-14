<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Manifest;

use Symfobooster\Devkit\Hydrator;

class Input
{
    /** @var Field[] */
    public array $fields = [];
    public bool $hasMuted = false;
    public bool $hadRenamed = false;

    public function setFields(array $fields): void
    {
        $hydrator = new Hydrator();
        foreach ($fields as $key => $manifest) {
            /** @var Field $field */
            $field = $hydrator->hydrate(Field::class, $manifest  ?? ['name' => $key]);
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
}
