<?php

namespace Zabachok\Symfobooster\Maker\Endpoint\Maker;

use Zabachok\Symfobooster\Maker\AbstractMaker;

use function Symfony\Component\String\u;

class EndpointConfigMaker extends AbstractMaker
{
    private string $domainSnake;
    private string $endpointSnake;

    public function make(): void
    {
        $this->domainSnake = u($this->manifest->domain)->snake();
        $this->endpointSnake = u($this->manifest->endpoint)->snake();
        $this->writeEndpoint();
        $this->writeCollection();
        $this->writeEndpoints();
    }

    private function writeEndpoint(): void
    {
        $prefix = $this->domainSnake . '.' . $this->endpointSnake;

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

        $fileName = '/endpoints/' . $this->domainSnake . '/' . $this->endpointSnake . '.yml';
        $this->writeConfigFile($fileName, $config);
    }

    private function writeCollection(): void
    {
        $path = '/endpoints/' . $this->manifest->domain . '.yml';
        $config = $this->readConfigFile($path) ?? [];
        if (!array_key_exists('imports', $config)) {
            $config['imports'] = [];
        }
        $resource = ['resource' => $this->domainSnake . '/' . $this->endpointSnake . '.yml'];
        if (array_search($resource, $config['imports']) === false) {
            $config['imports'][] = $resource;
        }

        $this->writeConfigFile($path, $config);
    }

    private function writeEndpoints(): void
    {
        $path = '/endpoints/endpoints.yml';
        $config = $this->readConfigFile($path) ?? [];
        if (!array_key_exists('imports', $config)) {
            $config['imports'] = [];
        }
        $resource = ['resource' => $this->manifest->domain . '.yml'];
        if (array_search($resource, $config['imports']) === false) {
            $config['imports'][] = $resource;
        }

        $this->writeConfigFile($path, $config);
    }
}
