<?php

namespace App\Command;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(
    name: 'app:add-client',
    description: 'Add a new client via the command line.',
)]
class AddClientCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $output->writeln('<info>Creating a new client</info>');
        
        // not empty, no special characters
        $firstnameQuestion = new Question('Client First Name: ');
        $firstnameQuestion->setValidator(function ($value) {
            if (empty($value) || !preg_match('/^[a-zA-ZÀ-ÿ\- ]+$/', $value)) {
                throw new \RuntimeException('The first name must contain only letters and cannot be empty.');
            }
            return ucfirst(strtolower(trim($value)));
        });

        // not empty, no special characters
        $lastnameQuestion = new Question('Client Last Name: ');
        $lastnameQuestion->setValidator(function ($value) {
            if (empty($value) || !preg_match('/^[a-zA-ZÀ-ÿ\- ]+$/', $value)) {
                throw new \RuntimeException('The last name must contain only letters and cannot be empty.');
            }
            return strtoupper(trim($value));
        });

        // use of filter_var to validate email format
        $emailQuestion = new Question('Client Email: ');
        $emailQuestion->setValidator(function ($value) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('Invalid email format.');
            }
            return strtolower(trim($value));
        });

        // only allows digits, spaces, +, and ()
        $phoneQuestion = new Question('Client Phone Number: ');
        $phoneQuestion->setValidator(function ($value) {
            if (!preg_match('/^\+?[0-9\s\-\(\)]+$/', $value)) {
                throw new \RuntimeException('Invalid phone number.');
            }
            return trim($value);
        });

        $addressQuestion = new Question('Client Address: ');
        $addressQuestion->setValidator(function ($value) {
            if (empty($value)) {
                throw new \RuntimeException('The address cannot be empty.');
            }
            return trim($value);
        });

        $firstname = $helper->ask($input, $output, $firstnameQuestion);
        $lastname = $helper->ask($input, $output, $lastnameQuestion);
        $email = $helper->ask($input, $output, $emailQuestion);
        $phoneNumber = $helper->ask($input, $output, $phoneQuestion);
        $address = $helper->ask($input, $output, $addressQuestion);

        // check if mail exists in database
        $existingClient = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => $email]);
        if ($existingClient) {
            $output->writeln('<error>A client with this email already exists.</error>');
            return Command::FAILURE;
        }

        // print in terminal the informations given for the user to confirm
        $output->writeln("<info>Summary:</info>");
        $output->writeln(" - First Name: <comment>{$firstname}</comment>");
        $output->writeln(" - Last Name: <comment>{$lastname}</comment>");
        $output->writeln(" - Email: <comment>{$email}</comment>");
        $output->writeln(" - Phone: <comment>{$phoneNumber}</comment>");
        $output->writeln(" - Address: <comment>{$address}</comment>");

        $confirmationQuestion = new ConfirmationQuestion('Do you confirm adding this client? (yes/no) ', false);
        if (!$helper->ask($input, $output, $confirmationQuestion)) {
            $output->writeln('<comment>Operation cancelled.</comment>');
            return Command::SUCCESS;
        }

        $client = new Client();
        $client->setFirstname($firstname);
        $client->setLastname($lastname);
        $client->setEmail($email);
        $client->setPhoneNumber($phoneNumber);
        $client->setAddress($address);
        $client->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $output->writeln('<info>Client successfully added!</info>');
        return Command::SUCCESS;
    }
}
