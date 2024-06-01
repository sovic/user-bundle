<?php

namespace UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use UserBundle\User\User;
use UserBundle\User\UserFactoryInterface;
use UserBundle\UserRole\UserRoleManager;
use UserBundle\Validation\EmailValidator;

final class AssignRole extends Command
{
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
            ->addArgument('id', InputArgument::OPTIONAL)
            ->addArgument('role', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $id = $input->getArgument('id');
        /** @var User $user */
        $user = null;
        if (empty($id)) {
            $question = new Question('User id/e-mail: ');
            $id = $helper->ask($input, $output, $question);
        }
        if (empty($id)) {
            $output->writeln('Invalid id');

            return Command::FAILURE;
        }
        if (EmailValidator::validate($id) === true) {
            $user = $this->userFactory->loadByEmail($id);
        }
        if (null === $user) {
            $user = $this->userFactory->loadById($id);
        }
        if (null === $user) {
            $output->writeln('User [' . $id . '] not found');

            return Command::FAILURE;
        }

        $role = $input->getArgument('role');
        if (null === $role) {
            $question = new ChoiceQuestion(
                'Please select role you want to assign to [' . $user->entity->getEmail() . ']: ',
                ['admin'], // TODO
                0
            );
            $question->setErrorMessage('Invalid role: %s.');
            $question->setMaxAttempts(3);
            $role = $helper->ask($input, $output, $question);
        }

        $urm = new UserRoleManager($user);
        if ($urm->addRole($role) === false) {
            $output->writeln('Role ' . $role . ' has not been assigned to user ' . $user->entity->getEmail());

            return Command::FAILURE;
        }

        $output->writeln('User ' . $user->entity->getEmail() . ' has been granted ' . $role . ' role.');

        return Command::SUCCESS;
    }
}
