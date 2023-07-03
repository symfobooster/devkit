<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker;

use Symfobooster\Devkit\Maker\AbstractMaker;

use function Symfony\Component\String\u;

class EndpointConfigMaker extends AbstractMaker
{
    private string $controllerSnake;
    private string $actionSnake;

    public function make(): void
    {
        $this->controllerSnake = u($this->manifest->controller)->snake();
        $this->actionSnake = u($this->manifest->action)->snake();
        $this->writeEndpoint();
        $this->writeCollection();
        $this->writeEndpoints();
    }

    private function writeEndpoint(): void
    {
        $prefix = $this->controllerSnake . '.' . $this->actionSnake;

        $config = [
            'services' => [
                $prefix . '.controller' => [
                    'public' => true,
                    'parent' => 'symfobooster.controller',
                    'arguments' => [
                        '$inputLoader' => '@' . $prefix . '.input',
                        '$service' => '@' . $prefix . '.service'
//                        '@' . $prefix . '.service',
                    ],
                ],
                $prefix . '.service' => [
                    'class' => $this->storage->get('serviceClass'),
                ],
                $prefix . '.input' => [
                    'parent' => 'symfobooster.input.loader',
                    'arguments' => [
                        '$input' => '@' . $this->storage->get('inputClass'),
                    ]
                ],
            ],
        ];

        $fileName = '/endpoints/' . $this->controllerSnake . '/' . $this->actionSnake . '.yml';
        $this->fileStorage->addFile('/config' . $fileName, $config);
    }

    private function writeCollection(): void
    {
        $path = '/endpoints/' . $this->manifest->controller . '.yml';
        $config = $this->readConfigFile($path) ?? [];
        if (!array_key_exists('imports', $config)) {
            $config['imports'] = [];
        }
        $resource = ['resource' => $this->controllerSnake . '/' . $this->actionSnake . '.yml'];
        if (array_search($resource, $config['imports']) === false) {
            $config['imports'][] = $resource;
        }

        $this->fileStorage->addFile('/config' . $path, $config);
    }

    private function writeEndpoints(): void
    {
        $path = '/endpoints/endpoints.yml';
        $config = $this->readConfigFile($path) ?? [];
        if (!array_key_exists('imports', $config)) {
            $config['imports'] = [];
        }
        $resource = ['resource' => $this->manifest->controller . '.yml'];
        if (array_search($resource, $config['imports']) === false) {
            $config['imports'][] = $resource;
        }

        $this->fileStorage->addFile('/config' . $path, $config);
    }
}
