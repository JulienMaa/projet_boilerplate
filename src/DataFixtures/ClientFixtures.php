<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $client = new Client();
            $client->setFirstname($faker->firstName);
            $client->setLastname($faker->lastName);
            $client->setEmail($faker->unique()->safeEmail);
            $client->setPhoneNumber($faker->phoneNumber);
            $client->setAddress($faker->address);
            $client->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($client);
        }

        $manager->flush();
    }
}
