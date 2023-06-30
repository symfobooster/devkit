<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker;

use Symfobooster\Devkit\Maker\AbstractMaker;

class RouterMaker extends AbstractMaker
{
    public function make(): void
    {
        $domainRouter = $this->readYamlFile($this->getDomainRouterPath()) ?? [];
        $key = $this->manifest->controller . '_' . $this->manifest->action;
        if (!array_key_exists($key, $domainRouter)) {
            $domainRouter[$key] = [
                'path' => '/' . $this->manifest->action,
                'controller' => $this->manifest->controller . '.' . $this->manifest->action . '.controller::action',
                'methods' => [strtoupper($this->manifest->method)],
            ];
            $this->writeYamlFile($this->getDomainRouterPath(), $domainRouter);
        }

        $router = $this->readYamlFile($this->getRouterPath()) ?? [];
        if (!array_key_exists($this->manifest->controller, $router)) {
            $router[$this->manifest->controller] = [
                'resource' => './routes/' . $this->manifest->controller . '.yml',
                'prefix' => '/' . $this->manifest->controller . '/',
            ];

            $this->writeYamlFile($this->getRouterPath(), $router);
        }
    }

    private function getDomainRouterPath(): string
    {
        return $this->generator->getRootDirectory() . '/config/routes/' . $this->manifest->controller . '.yml';
    }

    private function getRouterPath(): string
    {
        return $this->generator->getRootDirectory() . '/config/routes.yaml';
    }
}
