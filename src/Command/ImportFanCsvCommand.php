<?php

namespace App\Command;

use App\Service\CsvService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Fan;

class ImportFanCsvCommand extends Command
{
    protected static $defaultName = 'import:fan-csv';

    private CsvService $csvService;
    private EntityManagerInterface $entityManager;

    public function __construct(CsvService $csvService, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->csvService = $csvService;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = 'fans.csv';
        $data = $this->csvService->parse($filename);

        foreach ($data as $index => $row) {
            $fan = new Fan();
            if ($index === 0) {
                continue;
            }
            $fan->setName($row['name']);
            $fan->setEmail($row['email']);
            $fan->setMemberNumber((int) $row['fan_number']);
            $fan->setBirthday(new \DateTime($row['birthday']));

            $this->entityManager->persist($fan);
        }

        $this->entityManager->flush();
        $output->writeln('CSV data imported successfully.');

        return Command::SUCCESS;
    }
}
