<?php

namespace UserBundle\Command\Trait;

use InvalidArgumentException;
use Sovic\Common\Validator\EmailValidator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use UserBundle\User\User;
use UserBundle\User\UserFactoryInterface;

trait UserCommandTrait
{
    private function loadUserFromInput(
        InputInterface       $input,
        OutputInterface      $output,
        UserFactoryInterface $userFactory
    ): User {
        $helper = $this->getHelper('question');
        $id = $input->getArgument('id');
        /** @var User $user */
        $user = null;
        if (empty($id)) {
            $question = new Question('User id/e-mail: ');
            $id = $helper->ask($input, $output, $question);
        }
        if (empty($id)) {
            throw new InvalidArgumentException('Invalid id');
        }
        if (EmailValidator::validate($id) === true) {
            $user = $userFactory->loadByEmail($id);
        }
        if (null === $user && (int) $id > 0) {
            $user = $userFactory->loadById($id);
        }
        if (null === $user) {
            throw new InvalidArgumentException('User [' . $id . '] not found');
        }

        return $user;
    }
}
