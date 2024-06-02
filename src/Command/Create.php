<?php

namespace UserBundle\Command;

use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UserBundle\User\UserFactoryInterface;

#[AsCommand(name: 'user:create', description: 'Create user')]
class Create extends Command
{
    public function __construct(
        private readonly UserFactoryInterface $userFactory,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'User e-mail');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        if (empty($email)) {
            throw new InvalidArgumentException('Invalid e-mail');
        }

        $user = $this->userFactory->loadByEmail($email);
        if ($user) {
            $output->writeln('User already exists');

            return Command::FAILURE;
        }

        $password = substr(str_shuffle(MD5(microtime())), 0, 16);

        $userFactory = $this->userFactory;
        $user = $userFactory->createNew($email, $password);
        $user->flush();
        $user->activate();

        echo 'User created, credentials: ' . $email . ':' . $password . PHP_EOL;

        return Command::SUCCESS;
    }
}
