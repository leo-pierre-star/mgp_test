<?php

namespace App\Command;

use App\Repository\LoginEventRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:users:count-connected',
    description: 'Count the number of login events for the current day',
)]
class CountConnectedUsersCommand extends Command
{
    public function __construct(private readonly LoginEventRepository $loginEvents)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $today = new \DateTimeImmutable('today');
        $count = $this->loginEvents->countForDate($today);

        $io->success(sprintf('Connexions aujourd\'hui (%s): %d', $today->format('Y-m-d'), $count));
        return Command::SUCCESS;
    }
}
