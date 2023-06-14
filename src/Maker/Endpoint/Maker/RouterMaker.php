<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker;

use Symfobooster\Devkit\Maker\AbstractMaker;

class RouterMaker extends AbstractMaker
{
    public function make(): void
    {
        $domainRouter = $this->readYamlFile($this->getDomainRouterPath()) ?? [];
        $key = $this->manifest->domain . '_' . $this->manifest->endpoint;
        if(!array_key_exists($key, $domainRouter)) {
            $domainRouter[$key] = [
                'path' => '/' . $this->manifest->endpoint,
                'controller' => $this->manifest->domain . '.' . $this->manifest->endpoint . '.controller::action',
                'methods' => [strtoupper($this->manifest->method)],
            ];
            $this->writeYamlFile($this->getDomainRouterPath(), $domainRouter);
        }

        $router = $this->readYamlFile($this->getRouterPath()) ?? [];
        if(!array_key_exists($this->manifest->domain, $router)) {
            $router[$this->manifest->domain] = [
                'resource' => './routes/' . $this->manifest->domain . '.yml',
                'prefix' => '/' . $this->manifest->domain . '/',
            ];

            $this->writeYamlFile($this->getRouterPath(), $router);
        }
    }

    private function getDomainRouterPath(): string
    {
        return $this->generator->getRootDirectory() . '/config/routes/' . $this->manifest->domain . '.yml';
    }

    private function getRouterPath(): string
    {
        return $this->generator->getRootDirectory() . '/config/routes.yaml';
    }
}
