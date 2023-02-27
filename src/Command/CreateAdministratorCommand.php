<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-administrator',
    description: 'Create an administrator',
)]
class CreateAdministratorCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct('app:create-administrator');
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('full_name', InputArgument::OPTIONAL, 'Full Name')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input, $output);

        $fullName = $input->getArgument('full_name');
        if (!$fullName) {
            $question = new Question("What is the new administrator's name : ");
            $fullName = $helper->ask($input, $output, $question);
        }

        $email = $input->getArgument('email');
        if (!$email) {
            $question = new Question("What is {$fullName}'s email : ");
            $email = $helper->ask($input, $output, $question);
        }

        $plainPassword = $input->getArgument('password');
        $default = 'password';
        $questionString = sprintf('What is ' . $fullName . ' password? [%s]: ', $default);
        if (!$plainPassword) {
            $question = new Question($questionString, $default);
            $plainPassword = $helper->ask($input, $output, $question);
        }


        $user = (new User)->setFullName($fullName)
            ->setEmail($email)
            ->setPlainPassword($plainPassword)
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('The new adminstrator was created !');

        return Command::SUCCESS;
    }
}
