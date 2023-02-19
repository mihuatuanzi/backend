<?php

namespace App\Command;

use App\Config\UserType;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Repository\UserStateRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:system:init',
    description: '初始化应用数据',
    hidden: true
)]
class SystemInitCommand extends Command
{
    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly UserStateRepository         $userStateRepository,
        private readonly AuthenticationRepository    $authenticationRepository,
        private readonly UserPasswordHasherInterface $passwordHashTool
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('初始化应用数据，不要重复执行');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        {  // Set Super User
            if (!$email = $io->askQuestion(new Question('请输入 Super User email'))) {
                $io->error('缺少 Super User email');
                return Command::FAILURE;
            }
            if (!$password = $io->askQuestion(new Question('请输入 Super User 密码'))) {
                $io->error('请输入至少 6 位密码');
                return Command::FAILURE;
            }
            $this->createUsers($email, $password, '米花团子', ['ROLE_MANAGER', 'ROLE_ADMIN', 'ROLE_SUPER_USER']);
        }

        {  // Add a Admin User
            if (!$email = $io->askQuestion(new Question('请输入 Admin User email'))) {
                $io->error('缺少 Admin User email');
                return Command::FAILURE;
            }
            if (!$password = $io->askQuestion(new Question('请输入 Admin User 密码'))) {
                $io->error('请输入至少 6 位密码');
                return Command::FAILURE;
            }
            $this->createUsers($email, $password, '棉花团子', ['ROLE_MANAGER', 'ROLE_ADMIN']);
        }

        $io->success('System initialization succeeded.');

        return Command::SUCCESS;
    }

    private function createUsers(string $email, string $password, string $nickname, array $roles)
    {
        $auth = $this->authenticationRepository->findOrCreateByEmail($email, UserType::Person);
        $user = $auth->getUser();
        $user->setPassword($this->passwordHashTool->hashPassword($user, $password));
        $user->setRoles($roles);
        $user->setNickname($nickname);
        $this->userRepository->save($user, true);
        $this->userStateRepository->save($user->getUserState());
        $this->authenticationRepository->save($auth, true);
    }
}
