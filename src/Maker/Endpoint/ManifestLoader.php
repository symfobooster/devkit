<?php

namespace Symfobooster\Devkit\Maker\Endpoint;

use Exception;
use Symfobooster\Base\Input\Exception\InvalidInputException;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Yaml;

class ManifestLoader
{
    private string $projectDir;
    private ValidatorInterface $validator;

    public function __construct(string $projectDir, ValidatorInterface $validator)
    {
        $this->projectDir = $projectDir;
        $this->validator = $validator;
    }

    public function loadFromFile(string $fileName): Manifest
    {
        $manifestFilePath = dirname($this->projectDir) . '/' . $fileName;
        if (!file_exists($manifestFilePath)) {
            throw new Exception('Manifest file not found in ' . $manifestFilePath);
        }
        $data = $this->getData($manifestFilePath);
        $this->validate($data);

        $reflectionExtractor = new ReflectionExtractor();
        $phpDocExtractor = new PhpDocExtractor();
        $propertyTypeExtractor = new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$phpDocExtractor, $reflectionExtractor],
            [$phpDocExtractor],
            [$reflectionExtractor],
            [$reflectionExtractor]
        );

        $normalizer = new ObjectNormalizer(null, null, null, $propertyTypeExtractor);
        $arrayNormalizer = new ArrayDenormalizer();
        $serializer = new Serializer([$arrayNormalizer, $normalizer]);

        return $serializer->denormalize($data, Manifest::class);
    }

    private function validate(array $data): void
    {
        $violations = $this->validator->validate($data, Manifest::getValidators());

        if (count($violations) > 0) {
            throw new InvalidInputException($violations);
        }
    }

    private function getData(string $manifestFilePath): array
    {
        $data = Yaml::parseFile($manifestFilePath);
        $data = $this->transformFields('input', $data);
        $data = $this->transformFields('output', $data);

        return $data;
    }

    private function transformFields(string $block, array $data): array
    {

        if (empty($data[$block]) || empty($data[$block]['fields'])) {
            return $data;
        }

        $fields = [];
        foreach ($data[$block]['fields'] as $name => $value) {
            if (empty($value)) {
                $fields[] = ['name' => $name];
                continue;
            }
            if (!is_array($value)) {
                $fields[] = $value;
                continue;
            }
            $fields[] = array_merge($value, ['name' => $name]);
        }

        $data[$block]['fields'] = $fields;

        return $data;
    }
}
