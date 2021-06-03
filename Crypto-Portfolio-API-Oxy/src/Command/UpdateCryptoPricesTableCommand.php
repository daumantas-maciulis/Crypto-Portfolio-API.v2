<?php


namespace App\Command;


use App\Controller\CryptoCurrencyController;
use App\Service\UpdateCryptoPricesInUsdService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCryptoPricesTableCommand extends Command
{
    protected static $defaultName = 'update-crypto-prices-table';
    protected CryptoCurrencyController $cryptoCurrencyController;

    public function __construct(CryptoCurrencyController $cryptoCurrencyController, string $name = null)
    {
        $this->cryptoCurrencyController = $cryptoCurrencyController;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Updates Crypto prices table');
        $this->setHelp('This command automatically updates Crypto currency price in USD table in database from which program takes prices in USD');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting to update prices in USD');
        $this->cryptoCurrencyController->saveCryptoPricesAction();

        $output->writeln('Prices was updated successfully');

        return Command::SUCCESS;
    }
}