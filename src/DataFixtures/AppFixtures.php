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
use App\Entity\ValidateOrder;

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
        $themeNames = ['Theme A', 'Theme B', 'Theme C', 'Theme D', 'Theme E'];
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

            // Truncate the description to maximum 255 characters
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
            $randomGames = $faker->randomElements($games, mt_rand(1, 5)); // Adjust the range as needed
            foreach ($randomGames as $game) {
                $user->addUserGetGame($game);

                // Create a user game key
              
                $userGameKey = new UserGameKey();
                $userGameKey->setUser($user);
                $userGameKey->setGame($game);
                $userGameKey->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisYear()));
                $userGameKey->setGameKey(sha1($game->getName() . $game->getId())); // Generate unique key based on game name and ID

                $manager->persist($userGameKey); // Persist UserGameKey
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

        $usersWithOrders = [];
        $total = 0;
        foreach ($users as $user) {
            // Check if user already has an order
            if (in_array($user, $usersWithOrders)) {
                continue; // Skip this user if they already have an order
            }

            // Create the order
            $order = new Order();

            $order->setUser($user);
            $order->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisYear()));

            // Calculate total for the order
            $total = 0;
            $randomGames = $faker->randomElements($games, mt_rand(1, 5));
            foreach ($randomGames as $game) {
                $order->addGame($game);
                $total += $game->getPrice();
            }
            $order->setTotal($total);

            $manager->persist($order);

            // Mark this user as having an order
            $usersWithOrders[] = $user;

            // Create ValidateOrder if this order is validated
            if ($order->getStatus() === 'validated') {
                $validateOrder = new ValidateOrder();
                $validateOrder->setQuantity(count($order->getGames()));
                $validateOrder->setTotalPrice($order->getTotal());
                $validateOrder->addOrder($order);

                foreach ($order->getGames() as $game) {
                    $validateOrder->addGame($game);
                }

                $manager->persist($validateOrder);
            }
        
        }
        
        $manager->flush();
    }
}
