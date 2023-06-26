<?php

namespace Symfobooster\Devkit\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'symfobooster:devkit:manifest',
    description: 'Create or update manifest',
    hidden: false,
)]
class MakeManifestCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Manifest file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
