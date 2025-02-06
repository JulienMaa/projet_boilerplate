<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Creation of an admin user
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setFirstname('Admin');
        $admin->setLastname('User');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(password_hash('supersecureadminpass', PASSWORD_BCRYPT));
        $manager->persist($admin);

        // Creation of a manager user
        $managerUser = new User();
        $managerUser->setEmail('manager@example.com');
        $managerUser->setFirstname('Manager');
        $managerUser->setLastname('User');
        $managerUser->setRoles(['ROLE_MANAGER']);
        $managerUser->setPassword(password_hash('supersecuremanagerpass', PASSWORD_BCRYPT));
        $manager->persist($managerUser);

        // Creation of a basic user
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setFirstname('Basic');
        $user->setLastname('User');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(password_hash('supersecureuserpass', PASSWORD_BCRYPT));
        $manager->persist($user);

        $roles = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MANAGER'];

        for ($i = 0; $i < 10; $i++) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $email = $lastName . "." . $firstName . "@example.com";

            $userFaker = new User();
            $userFaker->setEmail($email);
            $userFaker->setFirstname($firstName);
            $userFaker->setLastname($lastName);

            $randomRole = $faker->randomElement($roles);
            $userFaker->setRoles([$randomRole]);

            $userFaker->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $manager->persist($userFaker);
        }

        $manager->flush();
    }
}
