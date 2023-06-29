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
        $manifestFilePath = $this->projectDir . '/' . $fileName;
        if (!file_exists($manifestFilePath)) {
            throw new Exception('Manifest file not found in ' . $manifestFilePath);
        }
        $this->validate($manifestFilePath);

        $reflectionExtractor = new ReflectionExtractor();
        $phpDocExtractor = new PhpDocExtractor();
        $propertyTypeExtractor = new PropertyInfoExtractor([$reflectionExtractor], [$phpDocExtractor, $reflectionExtractor], [$phpDocExtractor], [$reflectionExtractor], [$reflectionExtractor]);

        $normalizer = new ObjectNormalizer(null, null, null, $propertyTypeExtractor);
        $arrayNormalizer = new ArrayDenormalizer();
        $serializer = new Serializer([$arrayNormalizer, $normalizer]);

//        $extractor = new PropertyInfoExtractor([], [new ReflectionExtractor()]);
//        $serializer = new Serializer([new ObjectNormalizer(null, null, null, $propertyTypeExtractor)], [new YamlEncoder()]);

        return $serializer->denormalize(Yaml::parseFile($manifestFilePath), Manifest::class);

        return $serializer->deserialize(file_get_contents($manifestFilePath), Manifest::class, 'yml');
    }

    private function validate(string $manifestFilePath): void
    {
        $data = Yaml::parseFile($manifestFilePath);
        $violations = $this->validator->validate($data, Manifest::getValidators());

        if (count($violations) > 0) {
            throw new InvalidInputException($violations);
        }
    }
}
