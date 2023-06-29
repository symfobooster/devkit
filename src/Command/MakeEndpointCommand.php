<?php

namespace Symfobooster\Devkit\Command;

use Symfobooster\Base\Input\Exception\InvalidInputException;
use Symfobooster\Devkit\Maker\Endpoint\Maker\EndpointConfigMaker;
use Symfobooster\Devkit\Maker\Endpoint\Maker\OutputMaker;
use Symfobooster\Devkit\Maker\Endpoint\Maker\ServiceMaker;
use Symfobooster\Devkit\Maker\Endpoint\ManifestLoader;
use Symfobooster\Devkit\Maker\Storage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'symfobooster:devkit:endpoint',
    description: 'Make new endpoint',
    hidden: false,
)]
class MakeEndpointCommand extends Command
{
    private string $projectDir;
    private ManifestLoader $manifestLoader;

    public function __construct(string $projectDir, ManifestLoader $manifestLoader)
    {
        $this->projectDir = $projectDir;
        $this->manifestLoader = $manifestLoader;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Manifest file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $manifest = $this->manifestLoader->loadFromFile($input->getOption('file') ?? 'manifest.yml');
        } catch (InvalidInputException $exception) {
            $io->error('You have not valid fields in your manifest file');
            $io->note($exception);

            return Command::FAILURE;
        }

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
