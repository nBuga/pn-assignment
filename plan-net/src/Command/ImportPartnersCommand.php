<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\ImportPartner;
use App\Model\PartnerDTO;
use App\Service\ImportFileHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:import-partners',
    description: 'Import the partners fixtures',
)]
class ImportPartnersCommand extends Command
{
    public const FILE_NAME = 'partners';
    public function __construct(
        private readonly ImportFileHandler $importFileHandler,
        private readonly MessageBusInterface $messageBus,
    ) {
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
            $partnerDTO = new PartnerDTO(
                $fileLine['name'],
                $fileLine['url'],
                $fileLine['code']
            );

            $message = new ImportPartner($partnerDTO, $locale);
            $this->messageBus->dispatch($message);
        }

        $io->success('File has been imported successfully.');

        return Command::SUCCESS;
    }
}
