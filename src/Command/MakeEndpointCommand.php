<?php

namespace Symfobooster\Devkit\Command;

use Symfobooster\Devkit\Maker\Endpoint\Maker\EndpointConfigMaker;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTestMaker;
use Symfobooster\Devkit\Maker\Endpoint\Maker\InputMaker;
use Symfobooster\Devkit\Maker\Endpoint\Maker\OutputMaker;
use Symfobooster\Devkit\Maker\Endpoint\Maker\RouterMaker;
use Symfobooster\Devkit\Maker\Endpoint\Maker\ServiceMaker;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;
use Symfobooster\Devkit\Maker\Storage;
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
        // TODO here is need to add validation
        $storage = new Storage();

        foreach ($this->getMakers() as $maker) {
            $maker = new $maker($input, $io, $manifest, $storage, $this->projectDir);
            try {
                $maker->make();
            } catch (RuntimeCommandException $exception) {
                echo $exception->getMessage();
            }
        }

        $this->writeSuccessMessage($io);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    private function getMakers(): array
    {
        return [
//            InputMaker::class,
            OutputMaker::class,
            ServiceMaker::class,
            EndpointConfigMaker::class,
//            RouterMaker::class,
//            FunctionalTestMaker::class,
        ];
    }
}
