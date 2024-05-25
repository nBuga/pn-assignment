<?php

namespace App\Command;

use App\Message\ImportPartner;
use App\Message\ImportPrize;
use App\Model\PartnerDTO;
use App\Model\PrizeDTO;
use App\Service\ImportFileHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:import-prizes',
    description: 'Import the prizes fixtures',
)]
class ImportPrizeCommand extends Command
{
    public CONST FILE_NAME = 'prizes';
    public function __construct(
        private readonly ImportFileHandler $importFileHandler,
        private readonly MessageBusInterface $messageBus,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('locale', InputArgument::REQUIRED, 'Chosen locale');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $locale = $input->getArgument('locale');


        $fileLines = $this->importFileHandler->getFileContentLines(self::FILE_NAME, $locale);
        foreach ($fileLines as $fileLine) {
            $prizeDTO = new PrizeDTO(
                $fileLine['partner_code'],
                $fileLine['name'],
                $fileLine['description'],
                $fileLine['code']
            );

            $message = new ImportPrize($prizeDTO, $locale);
            $this->messageBus->dispatch($message);
        }

        $io->success('File has been imported successfully.');

        return Command::SUCCESS;
    }
}
