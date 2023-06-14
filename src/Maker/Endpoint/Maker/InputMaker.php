<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker;

use Symfobooster\Devkit\Maker\AbstractMaker;

class InputMaker extends AbstractMaker
{
    public function make(): void
    {
        $serviceDetails = $this->generator->createClassNameDetails(
            $this->manifest->endpoint,
            'Domain\\' . ucfirst($this->manifest->domain) . '\\Input\\',
            'Input'
        );
        $this->storage->set('inputClass', $serviceDetails->getFullName());
    }
}
