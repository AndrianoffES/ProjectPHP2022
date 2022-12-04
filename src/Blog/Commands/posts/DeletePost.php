<?php

namespace project\App\Blog\Commands\posts;

use project\App\Blog\Exceptions\PostNotFoundException;
use project\App\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use project\App\Blog\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;


class DeletePost extends Command
{
    public function __construct(
// Внедряем репозиторий статей
        private PostRepositoryInterface $postsRepository, )
    {
        parent::__construct();
    }
    // Конфигурируем команду
    protected function configure(): void {
        $this
            ->setName('posts:delete')
            ->setDescription('Deletes a post')
            ->addArgument(
                'uuid', InputArgument::REQUIRED, 'UUID of a post to delete'
            )
            ->addOption('check-existence', 'c',
                InputOption::VALUE_NONE, 'check if post actually exist');

    }
    protected function execute( InputInterface $input, OutputInterface $output,
    ): int {
        $question = new ConfirmationQuestion( // Вопрос для подтверждения
        'Delete post [Y/n]? ',
// По умолчанию не удалять
            false
        );
        // Ожидаем подтверждения
        if (!$this->getHelper('question') ->ask($input, $output, $question)
        ){
            return Command::SUCCESS; }
        // Получаем UUID статьи
        $uuid = new UUID($input->getArgument('uuid'));
        if ($input->getOption('check-existence')) { try {
            // Пытаемся получить статью
            $this->postsRepository->get($uuid); } catch (PostNotFoundException $e) {
            // Выходим, если статья не найдена
            $output->writeln($e->getMessage());
            return Command::FAILURE; }
        }
        // Удаляем статью из репозитория
        $this->postsRepository->delete($uuid);
        $output->writeln("Post $uuid deleted");
        return Command::SUCCESS; }
}