<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Manifest;

use Symfobooster\Base\Input\InputInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class Service implements InputInterface
{
    public bool $transactional;
    public string $repository;
    public string $entity;

    public static function getValidators(): Constraint
    {
        return new Assert\Collection([
            'transactional' => [
                new Assert\Optional([
                    new Assert\Type('boolean'),
                ]),
            ],
            'repository' => [
                new Assert\Optional([
                    new Assert\Type('string'),
                ]),
            ],
            'entity' => [
                new Assert\Optional([
                    new Assert\Type('string'),
                ]),
            ],
        ]);
    }
}
