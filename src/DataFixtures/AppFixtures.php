<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Game;
use App\Entity\Category;
use App\Entity\Tag;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Create some categories
        $categories = [];
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->word);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Create some tags
        $tags = [];
        for ($i = 0; $i < 20; $i++) {
            $tag = new Tag();
            $tag->setName($faker->word);
            $manager->persist($tag);
            $tags[] = $tag;
        }

        for ($i = 0; $i < 50; $i++) {
            $game = new Game();
            $game->setName($faker->sentence(3));
            $game->setReleaseDate(\DateTimeImmutable::createFromMutable($faker->dateTimeThisCentury)); // Convertir en DateTimeImmutable
            $game->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisYear)); // Convertir en DateTimeImmutable
            $game->setPicture($faker->imageUrl());
            $game->setPrice($faker->randomFloat(2, 10, 100));
        
            // Truncate description to 255 characters
            $description = $faker->paragraph;
            if (strlen($description) > 255) {
                $description = substr($description, 0, 252) . '...';
            }
            $game->setDescription($description);
            $game->setEditor($faker->company);
        
            // Assign random categories to game
            $randomCategoryKeys = array_rand($categories, mt_rand(1, 3));
            if (is_int($randomCategoryKeys)) {
                $randomCategoryKeys = [$randomCategoryKeys];
            }
            foreach ($randomCategoryKeys as $key) {
                $game->addHasCategory($categories[$key]);
            }
        
            // Assign random tags to game
            $randomTagKeys = array_rand($tags, mt_rand(1, 5));
            if (is_int($randomTagKeys)) {
                $randomTagKeys = [$randomTagKeys];
            }
            foreach ($randomTagKeys as $key) {
                $game->addHasTag($tags[$key]);
            }
        
            $manager->persist($game);
        }
        

        $manager->flush();
    }
}
