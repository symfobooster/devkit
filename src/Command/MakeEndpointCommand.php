<?php

namespace Symfobooster\Devkit\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'symfobooster:devkit:endpoint',
    description: 'Make new endpoint',
    hidden: false,
)]
class MakeEndpointCommand extends Command
{
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // \Symfobooster\Devkit\Maker\Endpoint\EndpointMaker

//         Класс формирования манифеста из файла. там гидрация и валидация
//        маниест передаём в генератор
//        дополнительные параметры

        $io = new SymfonyStyle($input, $output);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
