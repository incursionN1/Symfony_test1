<?php

namespace App\Command;

use App\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ImportCsvCommandStatus',
    description: 'Добавить данные в таблицу Status из файла test_status.csv',
)]
class ImportCsvCommandStatus extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

//        $filename = $input->getArgument('filename');
//        $tableName = $input->getOption('table');
//
//        // Проверка файла
//        if (!file_exists($filename)) {
//            $output->writeln('<error>File not found!</error>');
//            return Command::FAILURE;
//        }
        $count =0;
        try {
            // Открываем файл
            if (($handle = fopen('test_status.csv', 'r')) !== false) {
                $header = fgetcsv($handle); // Читаем заголовки
                while (($row = fgetcsv($handle)) !== false) {
                    $data = array_combine($header, $row);

                    $status = new Status();
                    $status->setName($data['name']);
                    $this->entityManager->persist($status);

                    $count++;
                    if ($count > 10) {
                        $this->entityManager->flush();
                        $this->entityManager->clear();
                        $io->note('Добавлено'. $count . 'Записей' );
                    }
                }

                fclose($handle);
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        } catch (\Exception $e) {
            $io->error(sprintf('Error: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
        return Command::SUCCESS;
    }
}
