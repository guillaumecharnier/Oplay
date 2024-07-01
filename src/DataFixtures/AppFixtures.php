<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Game;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Order;
use App\Entity\UserGameKey;

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

        // Create some themes
        $themes = [];
        $themeNames = ['Theme A', 'Theme B', 'Theme C'];
        foreach ($themeNames as $name) {
            $theme = new Theme();
            $theme->setName($name);
            $manager->persist($theme);
            $themes[] = $theme;
        }

        // Create games
        $games = [];
        for ($i = 0; $i < 50; $i++) {
            $game = new Game();
            $game->setName($faker->sentence(3));
            $game->setReleaseDate(\DateTimeImmutable::createFromMutable($faker->dateTimeThisDecade())); // Convertir en DateTimeImmutable
            $game->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisYear())); // Convertir en DateTimeImmutable
            $game->setPicture($faker->imageUrl());
            $game->setPrice($faker->randomFloat(2, 10, 100));

            // Tronquer la description à 255 caractères maximum
            $description = $faker->paragraph;
            if (strlen($description) > 255) {
                $description = substr($description, 0, 252) . '...';
            }
            $game->setDescription($description);
            $game->setEditor($faker->company);

            // Assign random categories to game
            $randomCategoryKeys = array_rand($categories, min(3, count($categories)));
            if (!is_array($randomCategoryKeys)) {
                $randomCategoryKeys = [$randomCategoryKeys];
            }
            foreach ($randomCategoryKeys as $key) {
                $game->addHasCategory($categories[$key]);
            }

            // Assign random tags to game
            $randomTagKeys = array_rand($tags, min(5, count($tags)));
            if (!is_array($randomTagKeys)) {
                $randomTagKeys = [$randomTagKeys];
            }
            foreach ($randomTagKeys as $key) {
                $game->addHasTag($tags[$key]);
            }

            $manager->persist($game);
            $games[] = $game;
        }

         // Create users
         $users = [];
         for ($i = 0; $i < 20; $i++) {
             $user = new User();
             $user->setFirstname($faker->firstName);
             $user->setLastname($faker->lastName);
             $user->setNickname($faker->userName);
             $user->setPicture($faker->imageUrl());
             $user->setEmail($faker->email);
             $user->setPassword($faker->password);
             $user->setChooseTheme($themes[array_rand($themes)]);
             $manager->persist($user);
             $users[] = $user;
 
             // Assign random games to user
             $randomGames = $faker->randomElements($games, mt_rand(1, 5));
             foreach ($randomGames as $game) {
                 $user->addUserGetGame($game);
             }

             // Assign random category to user
             $randomCategory = $faker->randomElements($categories, mt_rand(1, 5));
             foreach ($randomCategory as $category) {
                 $user->addSelectedCategory($category);
             }

             // Assign random tag to user
             $randomTag = $faker->randomElements($tags, mt_rand(1, 5));
             foreach ($randomTag as $tag) {
                 $user->addPreferedTag($tag);
             }
         }

        // Create orders
        for ($i = 0; $i < 30; $i++) {
            $order = new Order();
            $order->setStatus($faker->randomElement(['pending', 'completed']));
            $order->setTotal($faker->randomFloat(2, 50, 500));
            $order->setUser($faker->randomElement($users));

            // Set createdAt to current DateTimeImmutable
            $order->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisYear()));
           

             // Add random games to the order with a random key for the user
             $randomGames = $faker->randomElements($games, mt_rand(1, 5));
             foreach ($randomGames as $game) {
                 $order->addGame($game);
 
                 // Create UserGameKey
                 $userGameKey = new UserGameKey();
                 $userGameKey->setUser($order->getUser());
                 $userGameKey->setGame($game);
                 $userGameKey->setGameKey(bin2hex(random_bytes(16))); // Generate a random key
                 $userGameKey->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisYear()));
                 $manager->persist($userGameKey);
             }

            $manager->persist($order);
        }

        $manager->flush();
    }
}
