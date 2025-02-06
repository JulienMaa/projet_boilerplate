<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:import-products-csv',
    description: 'Imports products from a CSV file into the database'
)]
class ImportProductsCsvCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private Filesystem $filesystem;

    public function __construct(EntityManagerInterface $entityManager, Filesystem $filesystem)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('csvFile', InputArgument::OPTIONAL, 'The path to the CSV file', 'public/csv/products.csv')
            ->addOption('overwrite', null, InputOption::VALUE_NONE, 'If set, will overwrite existing products');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $csvFile = $input->getArgument('csvFile');
        $overwrite = $input->getOption('overwrite');

        if (!$this->filesystem->exists($csvFile)) {
            $io->error("The CSV file does not exist at path: $csvFile");
            return Command::FAILURE;
        }

        if (($handle = fopen($csvFile, 'r')) === false) {
            $io->error("Could not open the CSV file.");
            return Command::FAILURE;
        }

        fgetcsv($handle, 0, ';');

        $productsImported = 0;

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            if (count($data) !== 3) {
                continue;
            }

            // Extract columns: name, description, price
            list($name, $description, $price) = $data;

            // Check if the name exceeds the allowed length and truncate it
            if (strlen($name) > 100) {
                $io->warning("Product name '$name' is too long and will be truncated.");
                $name = substr($name, 0, 100);
            }

            $product = new Product();
            $product->setName($name);
            $product->setDescription($description);
            $product->setPrice((float) $price);

            $this->entityManager->persist($product);
            $productsImported++;
        }

        $this->entityManager->flush();

        fclose($handle);

        $io->success("$productsImported product(s) imported successfully!");

        return Command::SUCCESS;
    }
}
