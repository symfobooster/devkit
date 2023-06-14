<?php

namespace Symfobooster\Devkit;

use Symfobooster\Devkit\DependencyInjection\DevkitExtension;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class DevkitBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new DevkitExtension();
    }
}
