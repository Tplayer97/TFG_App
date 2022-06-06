<?php

namespace App\DataFixtures;

use App\Factory\CommentFactory;
use App\Factory\PostFactory;
use App\Factory\UserFactory;
use App\Repository\PostRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        UserFactory::createOne(['username' => 'AdminJose', 'roles' => ['ROLE_GESTOR']]);
        UserFactory::createMany(5, ['roles' => ['ROLE_GESTOR']]);
        UserFactory::createMany(5, ['roles' => ['ROLE_ADMIN']]);
        PostFactory::createMany(10,function(){
            return[
                'created_by' => UserFactory::random()->getUserIdentifier()
            ];
        });
        CommentFactory::createMany(50, function(){
            return[
                'created_by' =>     UserFactory::random()->getUserIdentifier(),
                'Post' => PostFactory::random()
            ];
        });
       // CommentFactory::createMany(3);
        $manager->flush();
    }
}
