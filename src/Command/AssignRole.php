<?php

namespace UserBundle\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use UserBundle\Command\Trait\UserCommandTrait;
use UserBundle\User\UserFactoryInterface;
use UserBundle\UserRole\UserRoleManager;

final class AssignRole extends Command
{
    use UserCommandTrait;

    public function __construct(
        private readonly UserFactoryInterface $userFactory
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:assign-role')
            ->setDescription('Adds role for user (interactively).')
            ->addArgument('id', InputArgument::OPTIONAL, 'User id/e-mail')
            ->addArgument('role', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $user = $this->loadUserFromInput($input, $output, $this->userFactory);
        } catch (Exception $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $helper = $this->getHelper('question');
        $role = $input->getArgument('role');
        if (null === $role) {
            $question = new ChoiceQuestion(
                'Please select role you want to assign to [' . $user->entity->getEmail() . ']: ',
                ['ROLE_ADMIN'],
                0
            );
            $question->setErrorMessage('Invalid role: %s.');
            $question->setMaxAttempts(3);
            $role = $helper->ask($input, $output, $question);
        }

//        $roles = $user->entity->getRoles();
//        $roles[] = $role;
//        $user->entity->setRoles(array_unique($roles));
//        $user->getEntityManager()->persist($user->entity);
//        $user->getEntityManager()->flush();

        $urm = new UserRoleManager($user);
        if ($urm->addRole($role) === false) {
            $output->writeln('Role ' . $role . ' has not been assigned to user ' . $user->entity->getEmail());

            return Command::FAILURE;
        }

        $output->writeln('User ' . $user->entity->getEmail() . ' has been granted ' . $role . ' role.');

        return Command::SUCCESS;
    }
}
