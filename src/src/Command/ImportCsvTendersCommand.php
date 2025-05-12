<?php

namespace App\Command;

use App\Entity\Status;
use App\Entity\Tenders;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ImportCsvTenders',
    description: 'Добавить данные в таблицу Status из файла test_task_data.csv',
)]
class ImportCsvTendersCommand extends Command
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
        $total =0;
        $id = 1;
        $statusArray =[];
        try {
            // Открываем файл
            if (($handle = fopen('test_task_data.csv', 'r')) !== false) {
                $header = fgetcsv($handle); // Читаем заголовки
                while (($row = fgetcsv($handle)) !== false) {
                    $data = array_combine($header, $row);

                    $tenders= new Tenders();
                    if (!empty($data['Статус'])) {
                        if (empty($statusArray[$data['Статус']]))
                        {
                            $statusModel = new Status();
                            $statusModel->setName($data['Статус']);
                            $this->entityManager->persist($statusModel);
                            $statusArray[$data['Статус']] = $statusModel;
                        }
                        $tenders->setStatus($statusArray[$data['Статус']]);
                    }
                    $tenders->setExternalCode($data['Внешний код']);
                    $tenders->setNumber($data['Номер']);
                    $tenders->setName($data['Название']);
                    $dateTime = DateTime::createFromFormat('d.m.Y H:i:s', $data['Дата изм.']);
                    if ($dateTime === false) {

                        $io->error('Неверный формат даты!');
                        continue;
                    }
                    $tenders->setDateUpdate($dateTime);
                    $this->entityManager->persist($tenders);

                    $count++;
                }
                fclose($handle);
                $this->entityManager->flush();
                $this->entityManager->clear();

                $io->note('add: '. $count . ' - Записей' );
            }
        } catch (\Exception $e) {
            $io->error(sprintf('Error: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        $io->success('Миграция завершена');
        return Command::SUCCESS;
    }
}
