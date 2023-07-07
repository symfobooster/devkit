<?php

namespace Symfobooster\Devkit\Command;

use Symfobooster\Base\Input\Exception\InvalidInputException;
use Symfobooster\Devkit\Maker\Endpoint\Maker\ConfigMaker;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTestMaker;
use Symfobooster\Devkit\Maker\Endpoint\Maker\InputMaker;
use Symfobooster\Devkit\Maker\Endpoint\Maker\OutputMaker;
use Symfobooster\Devkit\Maker\Endpoint\Maker\RouterMaker;
use Symfobooster\Devkit\Maker\Endpoint\Maker\ServiceMaker;
use Symfobooster\Devkit\Maker\Endpoint\ManifestLoader;
use Symfobooster\Devkit\Maker\FileStorage;
use Symfobooster\Devkit\Maker\FileWriter;
use Symfobooster\Devkit\Maker\MakerInterface;
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
    /** @var MakerInterface[]  */
    private array $makers;
    private FileStorage $fileStorage;

    public function __construct(string $projectDir, ManifestLoader $manifestLoader, array $makers, FileStorage $fileStorage)
    {
        $this->projectDir = $projectDir;
        $this->manifestLoader = $manifestLoader;
        $this->makers = $makers;
        $this->fileStorage = $fileStorage;
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
            $this->manifestLoader->loadFromFile($input->getOption('file') ?? 'manifest.yml');
        } catch (InvalidInputException $exception) {
            $io->error('You have not valid fields in your manifest file');
            $io->note($exception);

            return Command::FAILURE;
        }

        foreach ($this->makers as $maker) {
            try {
                $maker->make();
            } catch (RuntimeCommandException $exception) {
                echo $exception->getMessage();
            }
        }
        $fileWriter = new FileWriter($this->fileStorage, $this->projectDir);
        $fileWriter->writeFiles();

        $io->success('Your files have been successfully created');
        return Command::SUCCESS;
    }
}
