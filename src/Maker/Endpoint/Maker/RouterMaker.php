<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker;

use Symfobooster\Devkit\Maker\AbstractMaker;

class RouterMaker extends AbstractMaker
{
    public function make(): void
    {
        $domainRouter = $this->readYamlFile('/config/routes/' . $this->manifest->controller . '.yml') ?? [];
        $key = $this->manifest->controller . '_' . $this->manifest->action;
        if (!array_key_exists($key, $domainRouter)) {
            $domainRouter[$key] = [
                'path' => '/' . $this->manifest->action,
                'controller' => $this->manifest->controller . '.' . $this->manifest->action . '.controller::action',
                'methods' => [strtoupper($this->manifest->method)],
            ];

            $this->fileStorage->addFile('/config/routes/' . $this->manifest->controller . '.yml', $domainRouter);
        }

        $router = $this->readYamlFile('/config/routes.yaml') ?? [];
        if (!array_key_exists($this->manifest->controller, $router)) {
            $router[$this->manifest->controller] = [
                'resource' => './routes/' . $this->manifest->controller . '.yml',
                'prefix' => '/' . $this->manifest->controller . '/',
            ];

            $this->fileStorage->addFile('/config/routes.yaml', $router);
        }
    }
}
