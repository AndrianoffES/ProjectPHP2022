<?php

namespace project\App\Blog\Commands\FakeData;

use project\App\Blog\Comment;
use project\App\Blog\Post;
use project\App\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use project\App\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use project\App\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use project\App\Users\Name;
use project\App\Users\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use project\App\Blog\UUID;

class PopulateDB extends Command
{
    public function __construct(
        private \Faker\Generator $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository,
    ){ parent::__construct();
    }
    protected function configure(): void {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption('usersNumb', 'i', InputOption::VALUE_REQUIRED, 'count of users')
            ->addOption('commentsNumb', 'c', InputOption::VALUE_REQUIRED, 'count of comments')
            ->addOption('postsNumb', 'p', InputOption::VALUE_REQUIRED, 'count of posts');
    }
    protected function execute( InputInterface $input, OutputInterface $output,
    ): int {
        // Создаём  пользователей
        $users = [];
        for ($i = 0; $i < $input->getOption('usersNumb'); $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getLogin());
        }
        // От имени каждого пользователя создаём статьи
        foreach ($users as $user) {
        for ($i = 0; $i < $input->getOption('postsNumb'); $i++) {
            $post = $this->createFakePost($user);
            $output->writeln('Post created: ' . $post->getTitle());
            }
        }
        // делаем коменты
        foreach ($users as $user) {
            for ($i = 0; $i < $input->getOption('commentsNumb'); $i++) {
                $comment = $this->createFakeComment($user, $this->createFakePost($user));
                $output->writeln('Comment created: ');
            }
        }

    return Command::SUCCESS; }
    private function createFakeUser(): User {
        $user = User::createFrom(
            new Name(
            $this->faker->firstName,
            $this->faker->lastName
            ),
            $this->faker->userName,
            $this->faker->password,
        );
       // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);
        return $user;
}
    private function createFakePost(User $author): Post {
        $post = new Post(
            UUID::random(),
            $author,
            // Генерируем предложение не длиннее шести слов
            $this->faker->sentence(6, true),
            // Генерируем текст
            $this->faker->realText
        );
        // Сохраняем статью в репозиторий
        $this->postsRepository->save($post);
        return $post;
    }

    private function createFakeComment(User $author, Post $post):Comment{
        $comment = new Comment(
            UUID::random(),
            $post,
            $author,
            $this->faker->realText
        );
        $this->commentsRepository->save($comment);
        return $comment;

    }
}