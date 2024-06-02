<?php

namespace UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use UserBundle\Command\Trait\UserCommandTrait;
use UserBundle\User\UserFactoryInterface;

#[AsCommand(name: 'user:set-password', description: 'Set user password')]
class SetPassword extends Command
{
    use UserCommandTrait;

    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserFactoryInterface        $userFactory,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'User id/e-mail');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $user = $this->loadUserFromInput($input, $output, $this->userFactory);
        } catch (Exception $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $entity = $user->getEntity();
        $password = $input->getArgument('password');
        $hashedPassword = $this->passwordHasher->hashPassword($entity, $password);
        $entity->setPassword($hashedPassword);
        $user->flush();

        $output->writeln('Password for user ' . $entity->getEmail() . ' has been set');

        return Command::SUCCESS;
    }
}
