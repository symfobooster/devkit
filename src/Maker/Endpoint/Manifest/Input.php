<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Manifest;

use Symfobooster\Base\Input\InputInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class Input implements InputInterface
{
    /** @var Field[] $fields */
    private array $fields = [];
//    public bool $hasMuted = false;
//    public bool $hadRenamed = false;

//    public function setFields(array $fields): void
//    {
//        $hydrator = new Hydrator();
//        foreach ($fields as $key => $manifest) {
//            /** @var Field $field */
//            $field = $hydrator->hydrate(Field::class, $manifest ?? ['name' => $key]);
//            $field->name = $key;
//            if ($field->muted) {
//                $this->hasMuted = true;
//            }
//            if ($field->renamed) {
//                $this->hadRenamed = true;
//            }
//            $this->fields[] = $field;
//        }
//    }

    public static function getValidators(): Constraint
    {
        return new Assert\Collection([
            'fields' => [ // fields is reserved word in Collection
                'fields' => [
                    new Assert\Optional([
                        new Assert\Type('array'),
                        new Assert\All([
                            Field::getValidators(),
                        ])
                    ]),
                ],
            ],
        ]);
    }

    /**
     * @param Field[] $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function addField(Field $field)
    {
        $this->fields[] = $field;
    }
}
