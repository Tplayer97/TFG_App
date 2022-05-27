<?php

namespace App\DataFixtures;

use App\Factory\CommentFactory;
use App\Factory\PostFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        UserFactory::createOne(['username' => 'AdminJose']);
        UserFactory::createMany(10);
        PostFactory::createMany(5);
       // CommentFactory::createMany(3);
        $manager->flush();
    }
}
