<?php

namespace Symfobooster\Devkit\Command;

use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'symfobooster:devkit:endpoint',
    description: 'Make new endpoint',
    hidden: false,
)]
class MakeEndpointCommand extends Command
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Manifest file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // \Symfobooster\Devkit\Maker\Endpoint\EndpointMaker

//         Класс формирования манифеста из файла. там гидрация и валидация
//        маниест передаём в генератор
//        дополнительные параметры

        $manifestFileName = empty($input->getOption('file')) ? 'manifest.yml' : $input->getOption('file');
        $manifestFilePath = $this->projectDir . '/' . $manifestFileName;
        if (!file_exists($manifestFilePath)) {
            $io->error('Manifest file not found in ' . $manifestFilePath);

            return Command::FAILURE;
        }
        $extractor = new PropertyInfoExtractor([], [new ReflectionExtractor()]);
        $serializer = new Serializer([new ObjectNormalizer(null, null, null, $extractor)], [new YamlEncoder()]);
        $manifest = $serializer->deserialize(file_get_contents($manifestFilePath), Manifest::class, 'yml');
        var_dump($manifest);

//====
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
