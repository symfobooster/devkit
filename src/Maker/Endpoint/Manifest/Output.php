<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Manifest;

use Symfobooster\Base\Input\InputInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class Output implements InputInterface
{
    private const KINDS = ['success', 'created'];
    private const EXTEND_CLASSES = [
        'success' => Success::class,
        'no-content' => Created::class
    ];

    public string $kind = 'success';
    /** @var Field[] $fields */
    public array $fields = [];

    public static function getValidators(): Constraint
    {
        return new Assert\Collection([
            'fields' => [ // fields is reserved word in Collection
                'fields' => [
                    new Assert\Optional([
                        new Assert\Type('array'),
                        new Assert\Count(null, 1, 100),
                        new Assert\All([
                            Field::getValidators(),
                        ])
                    ]),
                ],
            ],
        ]);
    }
}
