<?php

namespace Zabachok\SymfoboosterDevkit\Maker\Endpoint\Maker;

use Zabachok\SymfoboosterDevkit\Maker\AbstractMaker;

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
