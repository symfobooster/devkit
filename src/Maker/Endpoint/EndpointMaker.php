<?php

namespace Zabachok\SymfoboosterDevkit\Maker\Endpoint;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Yaml;
use Zabachok\SymfoboosterDevkit\Hydrator;
use Zabachok\SymfoboosterDevkit\Maker\Endpoint\Maker\FunctionalTestMaker;
use Zabachok\SymfoboosterDevkit\Maker\Endpoint\Maker\InputMaker;
use Zabachok\SymfoboosterDevkit\Maker\Endpoint\Maker\OutputMaker;
use Zabachok\SymfoboosterDevkit\Maker\Endpoint\Maker\RouterMaker;
use Zabachok\SymfoboosterDevkit\Maker\Endpoint\Maker\EndpointConfigMaker;
use Zabachok\SymfoboosterDevkit\Maker\Endpoint\Maker\ServiceMaker;
use Zabachok\SymfoboosterDevkit\Maker\Endpoint\Manifest\Manifest;
use Zabachok\SymfoboosterDevkit\Maker\Storage;

class EndpointMaker extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:endpoint';
    }

    public static function getCommandDescription(): string
    {
        return 'Make new api endpoint from manifest file';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command->addOption('manifest', 'm', InputOption::VALUE_REQUIRED, 'Manifest file');
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $manifestFileName = empty($input->getOption('manifest')) ? 'manifest.yml' : $input->getOption('manifest');

        $manifestFilePath = $generator->getRootDirectory() . '/' . $manifestFileName;
        if (!file_exists($manifestFilePath)) {
            throw new RuntimeCommandException('Manifest file not found');
        }
        $rawManifest = Yaml::parse(file_get_contents($manifestFilePath));
        $hydrator = new Hydrator();
        $manifest = $hydrator->hydrate(Manifest::class, $rawManifest);
        $storage = new Storage();

        foreach ($this->getMakers() as $maker) {
            $maker = new $maker($input, $io, $generator, $manifest, $storage);
            try {
                $maker->make();
            } catch (RuntimeCommandException $exception) {
                echo $exception->getMessage();
            }
        }

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
    }

    private function getMakers(): array
    {
        return [
            InputMaker::class,
            OutputMaker::class,
            ServiceMaker::class,
            EndpointConfigMaker::class,
            RouterMaker::class,
            FunctionalTestMaker::class,
        ];
    }
}
