<?php

namespace EDC\BaseUserBundle\Command;

use EDC\BaseUserBundle\Service\UserManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUserPasswordCommand extends Command
{
    protected static $defaultName = 'edc_base_user:update-user-password';

    /** @var UserManagerInterface */
    private $userManager;

    /**
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        parent::__construct(self::$defaultName);
        $this->userManager = $userManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Update User Password')
            ->setDefinition(
                new InputDefinition(
                    array(
                        new InputArgument(
                            'identifier',
                            InputArgument::REQUIRED,
                            'identifier (username or psasword) of the user'
                        ),
                        new InputArgument(
                            'password',
                            InputArgument::REQUIRED,
                            'plain password of the user'
                        )
                    )
                )
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $input->getArgument('identifier');
        $password = $input->getArgument('password');

        $user = $this->userManager->findUser($identifier);

        if (!$user) {
            return 404;
        }

        $this->userManager->updatePassword($user, $password);

        $this->userManager->updateUser($user);

        $output->write('Username ' . $identifier . ' has been updated with ' . $password . ' password');

        return 0;
    }
}
