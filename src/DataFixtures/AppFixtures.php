<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Game;
use App\Entity\Category;
use App\Entity\GameOrder;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Order;
use App\Entity\UserGameKey;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    private function getGames(ObjectManager $manager)
    {
        $gameRepository = $manager->getRepository(Game::class);
        return $gameRepository->findAll();
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Définition des données de catégorie
        $categoryData = [
            [
                'name' => 'Action',
                'picture' => 'https://picsum.photos/200',
            ],
            [
                'name' => 'Adventure',
                'picture' => 'https://picsum.photos/200',
            ],
            [
                'name' => 'RPG',
                'picture' => 'https://picsum.photos/200',
            ],
            [
                'name' => 'Strategy',
                'picture' => 'https://picsum.photos/200',
            ],
            [
                'name' => 'Simulation',
                'picture' => 'https://picsum.photos/200',
            ],
            [
                'name' => 'Sports',
                'picture' => 'https://picsum.photos/200',
            ],
        ];

        // Création des catégories
        $categoryEntityList = [];
        foreach ($categoryData as $categoryInfo) {
            $category = new Category();
            $category->setName($categoryInfo['name']);
            $category->setPicture($categoryInfo['picture']);
            $manager->persist($category); // Persiste l'entité de catégorie
            $categoryEntityList[] = $category; // Ajoute la catégorie à la liste
        }

        // Définition des données de tag
        $tagData = [
            [
                'name' => 'Shooter',
                'picture' => 'https://picsum.photos/200'
            ],
            [
                'name' => 'Fighting',
                'picture' => 'https://picsum.photos/200'
                
            ],
            [
                'name' => 'Stealth',
                'picture' => 'https://picsum.photos/200'
            ],
            [
                'name' => 'Open World',
                'picture' => 'https://picsum.photos/200'
            ],
            [
                'name' => 'Survival',
                'picture' => 'https://picsum.photos/200'
            ],
            [
                'name' => 'Exploration',
                'picture' => 'https://picsum.photos/200'
            ],
            [
                'name' => 'Fantasy',
                'picture' => 'https://picsum.photos/200'
            ],
            [
                'name' => 'Sci-Fi',
                'picture' => 'https://picsum.photos/200'
            ],
            [
                'name' => 'Turn-Based',
                'picture' => 'https://picsum.photos/200'
            ],
            [
                'name' => 'Real-Time',
                'picture' => 'https://picsum.photos/200'
            ],
            [
                'name' => 'Simulation',
                'picture' => 'https://picsum.photos/200'
            ],
            [
                'name' => 'Football',
                'picture' => 'https://picsum.photos/200'
            ],
        ];

        // Création des tags
        $tagEntityList = [];
        foreach ($tagData as $tagInfo) {
            $tag = new Tag();
            $tag->setName($tagInfo['name']);
            $tag->setPicture($tagInfo['name']);
            $manager->persist($tag);
            $tagEntityList[] = $tag;
        }

        // Création des thèmes
        $themes = [];
        $themeNames = ['Action', 'Adventure', 'RPG', 'Strategy', 'Simulation', 'Sports'];
        foreach ($themeNames as $name) {
            $theme = new Theme();
            $theme->setName($name);
            $manager->persist($theme);
            $themes[] = $theme;
        }

        // Définition des données de jeu
        $gamesData = [
            [
                'name' => 'The Witcher 3: Wild Hunt',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2015-05-19'),
                'description' => 'The Witcher 3: Wild Hunt is an action role-playing game developed and published by CD Projekt.',
                'price' => 49.99,
                'editor' => 'CD Projekt'
            ],
            [
                'name' => 'Red Dead Redemption 2',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2018-10-26'),
                'description' => 'Red Dead Redemption 2 is a Western-themed action-adventure game developed and published by Rockstar Games.',
                'price' => 59.99,
                'editor' => 'Rockstar Games'
            ],
            [
                'name' => 'Cyberpunk 2077',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-12-10'),
                'description' => 'Cyberpunk 2077 is an open-world action-adventure game developed and published by CD Projekt.',
                'price' => 59.99,
                'editor' => 'CD Projekt'
            ],
            [
                'name' => 'The Legend of Zelda: Breath of the Wild',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2017-03-03'),
                'description' => 'The Legend of Zelda: Breath of the Wild is an action-adventure game developed and published by Nintendo.',
                'price' => 59.99,
                'editor' => 'Nintendo'
            ],
            [
                'name' => 'GTA V',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2013-09-17'),
                'description' => 'Grand Theft Auto V is an action-adventure game developed by Rockstar North and published by Rockstar Games.',
                'price' => 39.99,
                'editor' => 'Rockstar Games'
            ],
            [
                'name' => 'Assassin\'s Creed Valhalla',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-11-10'),
                'description' => 'Assassin\'s Creed Valhalla is an action role-playing game developed by Ubisoft Montreal and published by Ubisoft.',
                'price' => 49.99,
                'editor' => 'Ubisoft'
            ],
            [
                'name' => 'Call of Duty: Warzone',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-03-10'),
                'description' => 'Call of Duty: Warzone is a free-to-play battle royale game developed by Infinity Ward and Raven Software, and published by Activision.',
                'price' => 0,
                'editor' => 'Activision'
            ],
            [
                'name' => 'Minecraft',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2011-11-18'),
                'description' => 'Minecraft is a sandbox video game developed by Mojang Studios.',
                'price' => 19.99,
                'editor' => 'Mojang Studios'
            ],
            [
                'name' => 'Fortnite',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2017-07-25'),
                'description' => 'Fortnite is an online video game developed by Epic Games.',
                'price' => 0,
                'editor' => 'Epic Games'
            ],
            [
                'name' => 'Apex Legends',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-02-04'),
                'description' => 'Apex Legends is a free-to-play battle royale game developed by Respawn Entertainment and published by Electronic Arts.',
                'price' => 0,
                'editor' => 'Electronic Arts'
            ],
            [
                'name' => 'League of Legends',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2009-10-27'),
                'description' => 'League of Legends is a multiplayer online battle arena video game developed and published by Riot Games.',
                'price' => 0,
                'editor' => 'Riot Games'
            ],
            [
                'name' => 'Valorant',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-06-02'),
                'description' => 'Valorant is a free-to-play first-person shooter game developed and published by Riot Games.',
                'price' => 0,
                'editor' => 'Riot Games'
            ],
            [
                'name' => 'Among Us',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2018-06-15'),
                'description' => 'Among Us is an online multiplayer social deduction game developed and published by InnerSloth.',
                'price' => 4.99,
                'editor' => 'InnerSloth'
            ],
            [
                'name' => 'Animal Crossing: New Horizons',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-03-20'),
                'description' => 'Animal Crossing: New Horizons is a life simulation video game developed and published by Nintendo.',
                'price' => 59.99,
                'editor' => 'Nintendo'
            ],
            [
                'name' => 'FIFA 21',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-10-09'),
                'description' => 'FIFA 21 is a football simulation video game published by Electronic Arts as part of the FIFA series.',
                'price' => 59.99,
                'editor' => 'Electronic Arts'
            ],
            [
                'name' => 'NBA 2K21',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-09-04'),
                'description' => 'NBA 2K21 is a basketball simulation video game developed by Visual Concepts and published by 2K Sports.',
                'price' => 59.99,
                'editor' => '2K Sports'
            ],
            [
                'name' => 'The Last of Us Part II',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-06-19'),
                'description' => 'The Last of Us Part II is an action-adventure game developed by Naughty Dog and published by Sony Interactive Entertainment.',
                'price' => 59.99,
                'editor' => 'Sony Interactive Entertainment'
            ],
            [
                'name' => 'Ghost of Tsushima',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-07-17'),
                'description' => 'Ghost of Tsushima is an action-adventure game developed by Sucker Punch Productions and published by Sony Interactive Entertainment.',
                'price' => 59.99,
                'editor' => 'Sony Interactive Entertainment'
            ],
            [
                'name' => 'Death Stranding',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-11-08'),
                'description' => 'Death Stranding is an action game developed by Kojima Productions and published by Sony Interactive Entertainment.',
                'price' => 49.99,
                'editor' => 'Sony Interactive Entertainment'
            ],
            [
                'name' => 'God of War',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2018-04-20'),
                'description' => 'God of War is an action-adventure game developed by Santa Monica Studio and published by Sony Interactive Entertainment.',
                'price' => 39.99,
                'editor' => 'Sony Interactive Entertainment'
            ],
            [
                'name' => 'Horizon Zero Dawn',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2017-02-28'),
                'description' => 'Horizon Zero Dawn is an action role-playing game developed by Guerrilla Games and published by Sony Interactive Entertainment.',
                'price' => 29.99,
                'editor' => 'Sony Interactive Entertainment'
            ],
            [
                'name' => 'Cyberpunk 2077',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-12-10'),
                'description' => 'Cyberpunk 2077 is an open-world action-adventure game developed and published by CD Projekt.',
                'price' => 59.99,
                'editor' => 'CD Projekt'
            ],
            [
                'name' => 'Battlefield V',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2018-11-20'),
                'description' => 'Battlefield V is a first-person shooter video game developed by EA DICE and published by Electronic Arts.',
                'price' => 39.99,
                'editor' => 'Electronic Arts'
            ],
            [
                'name' => 'Star Wars Jedi: Fallen Order',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-11-15'),
                'description' => 'Star Wars Jedi: Fallen Order is an action-adventure game developed by Respawn Entertainment and published by Electronic Arts.',
                'price' => 59.99,
                'editor' => 'Electronic Arts'
            ],
            [
                'name' => 'DOOM Eternal',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-03-20'),
                'description' => 'DOOM Eternal is a first-person shooter video game developed by id Software and published by Bethesda Softworks.',
                'price' => 49.99,
                'editor' => 'Bethesda Softworks'
            ],
            [
                'name' => 'Sekiro: Shadows Die Twice',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-03-22'),
                'description' => 'Sekiro: Shadows Die Twice is an action-adventure game developed by FromSoftware and published by Activision.',
                'price' => 59.99,
                'editor' => 'Activision'
            ],
            [
                'name' => 'World of Warcraft',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2004-11-23'),
                'description' => 'World of Warcraft is a massively multiplayer online role-playing game developed by Blizzard Entertainment.',
                'price' => 14.99,
                'editor' => 'Blizzard Entertainment'
            ],
            [
                'name' => 'Overwatch',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2016-05-24'),
                'description' => 'Overwatch is a team-based multiplayer first-person shooter developed and published by Blizzard Entertainment.',
                'price' => 39.99,
                'editor' => 'Blizzard Entertainment'
            ],
            [
                'name' => 'The Elder Scrolls V: Skyrim',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2011-11-11'),
                'description' => 'The Elder Scrolls V: Skyrim is an action role-playing game developed by Bethesda Game Studios and published by Bethesda Softworks.',
                'price' => 39.99,
                'editor' => 'Bethesda Softworks'
            ],
            [
                'name' => 'Rocket League',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2015-07-07'),
                'description' => 'Rocket League is a vehicular soccer video game developed and published by Psyonix.',
                'price' => 19.99,
                'editor' => 'Psyonix'
            ],
            [
                'name' => 'Dota 2',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2013-07-09'),
                'description' => 'Dota 2 is a multiplayer online battle arena video game developed and published by Valve Corporation.',
                'price' => 0,
                'editor' => 'Valve Corporation'
            ],
            [
                'name' => 'Counter-Strike: Global Offensive',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2012-08-21'),
                'description' => 'Counter-Strike: Global Offensive is a multiplayer first-person shooter video game developed and published by Valve Corporation.',
                'price' => 0,
                'editor' => 'Valve Corporation'
            ],
            [
                'name' => 'Mortal Kombat 11',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-04-23'),
                'description' => 'Mortal Kombat 11 is a fighting game developed by NetherRealm Studios and published by Warner Bros. Interactive Entertainment.',
                'price' => 49.99,
                'editor' => 'Warner Bros. Interactive Entertainment'
            ],
            [
                'name' => 'Super Smash Bros. Ultimate',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2018-12-07'),
                'description' => 'Super Smash Bros. Ultimate is a crossover fighting game developed by Bandai Namco Studios and Sora Ltd., and published by Nintendo.',
                'price' => 59.99,
                'editor' => 'Nintendo'
            ],
            [
                'name' => 'Persona 5',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2017-04-04'),
                'description' => 'Persona 5 is a role-playing video game developed by Atlus.',
                'price' => 49.99,
                'editor' => 'Atlus'
            ],
            [
                'name' => 'Resident Evil Village',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2021-05-07'),
                'description' => 'Resident Evil Village is a survival horror game developed and published by Capcom.',
                'price' => 59.99,
                'editor' => 'Capcom'
            ],
            [
                'name' => 'Fall Guys: Ultimate Knockout',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-08-04'),
                'description' => 'Fall Guys: Ultimate Knockout is a platformer battle royale game developed by Mediatonic and published by Devolver Digital.',
                'price' => 19.99,
                'editor' => 'Devolver Digital'
            ],
            [
                'name' => 'Among Us',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2018-06-15'),
                'description' => 'Among Us is an online multiplayer social deduction game developed and published by InnerSloth.',
                'price' => 4.99,
                'editor' => 'InnerSloth'
            ],
            [
                'name' => 'Super Mario Odyssey',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2017-10-27'),
                'description' => 'Super Mario Odyssey is a platform game developed and published by Nintendo.',
                'price' => 59.99,
                'editor' => 'Nintendo'
            ],
            [
                'name' => 'Pokémon Sword and Shield',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-11-15'),
                'description' => 'Pokémon Sword and Shield are role-playing video games developed by Game Freak and published by The Pokémon Company and Nintendo.',
                'price' => 59.99,
                'editor' => 'Nintendo'
            ],
            [
                'name' => 'The Legend of Zelda: Breath of the Wild',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2017-03-03'),
                'description' => 'The Legend of Zelda: Breath of the Wild is an action-adventure game developed and published by Nintendo.',
                'price' => 59.99,
                'editor' => 'Nintendo'
            ],
            [
                'name' => 'Monster Hunter: World',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2018-01-26'),
                'description' => 'Monster Hunter: World is an action role-playing game developed and published by Capcom.',
                'price' => 39.99,
                'editor' => 'Capcom'
            ],
            [
                'name' => 'Persona 5 Royal',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-10-31'),
                'description' => 'Persona 5 Royal is a role-playing video game developed by Atlus.',
                'price' => 59.99,
                'editor' => 'Atlus'
            ],
            [
                'name' => 'Final Fantasy VII Remake',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-04-10'),
                'description' => 'Final Fantasy VII Remake is an action role-playing game developed and published by Square Enix.',
                'price' => 59.99,
                'editor' => 'Square Enix'
            ],
            [
                'name' => 'NieR: Automata',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2017-02-23'),
                'description' => 'NieR: Automata is an action role-playing game developed by PlatinumGames and published by Square Enix.',
                'price' => 39.99,
                'editor' => 'Square Enix'
            ],
            [
                'name' => 'Tom Clancy\'s Rainbow Six Siege',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2015-12-01'),
                'description' => 'Tom Clancy\'s Rainbow Six Siege is a tactical shooter video game developed by Ubisoft Montreal and published by Ubisoft.',
                'price' => 19.99,
                'editor' => 'Ubisoft'
            ],
            [
                'name' => 'Tom Clancy\'s The Division 2',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-03-15'),
                'description' => 'Tom Clancy\'s The Division 2 is an online action role-playing video game developed by Massive Entertainment and published by Ubisoft.',
                'price' => 29.99,
                'editor' => 'Ubisoft'
            ],
            [
                'name' => 'Hitman 3',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2021-01-20'),
                'description' => 'Hitman 3 is a stealth game developed and published by IO Interactive.',
                'price' => 59.99,
                'editor' => 'IO Interactive'
            ],
            [
                'name' => 'Control',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-08-27'),
                'description' => 'Control is an action-adventure video game developed by Remedy Entertainment and published by 505 Games.',
                'price' => 39.99,
                'editor' => '505 Games'
            ],
            [
                'name' => 'The Outer Worlds',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-10-25'),
                'description' => 'The Outer Worlds is an action role-playing game developed by Obsidian Entertainment and published by Private Division.',
                'price' => 39.99,
                'editor' => 'Private Division'
            ],
            [
                'name' => 'Sea of Thieves',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2018-03-20'),
                'description' => 'Sea of Thieves is an action-adventure game developed by Rare and published by Xbox Game Studios.',
                'price' => 39.99,
                'editor' => 'Xbox Game Studios'
            ],
            [
                'name' => 'Hades',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-09-17'),
                'description' => 'Hades is a roguelike action dungeon crawler video game developed and published by Supergiant Games.',
                'price' => 24.99,
                'editor' => 'Supergiant Games'
            ],
            [
                'name' => 'Borderlands 3',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-09-13'),
                'description' => 'Borderlands 3 is an action role-playing first-person shooter video game developed by Gearbox Software and published by 2K Games.',
                'price' => 49.99,
                'editor' => '2K Games'
            ],
            [
                'name' => 'Days Gone',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-04-26'),
                'description' => 'Days Gone is an action-adventure survival horror video game developed by Bend Studio and published by Sony Interactive Entertainment.',
                'price' => 39.99,
                'editor' => 'Sony Interactive Entertainment'
            ],
            [
                'name' => 'The Sims 4',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2014-09-02'),
                'description' => 'The Sims 4 is a life simulation video game developed by Maxis and published by Electronic Arts.',
                'price' => 39.99,
                'editor' => 'Electronic Arts'
            ],
            [
                'name' => 'Detroit: Become Human',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2018-05-25'),
                'description' => 'Detroit: Become Human is an adventure game developed by Quantic Dream and published by Sony Interactive Entertainment.',
                'price' => 39.99,
                'editor' => 'Sony Interactive Entertainment'
            ],
            [
                'name' => 'Star Wars: Squadrons',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-10-02'),
                'description' => 'Star Wars: Squadrons is a space combat game developed by Motive Studios and published by Electronic Arts.',
                'price' => 39.99,
                'editor' => 'Electronic Arts'
            ],
            [
                'name' => 'Tony Hawk\'s Pro Skater 1 + 2',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-09-04'),
                'description' => 'Tony Hawk\'s Pro Skater 1 + 2 is a skateboarding video game developed by Vicarious Visions and published by Activision.',
                'price' => 39.99,
                'editor' => 'Activision'
            ],
            [
                'name' => 'Marvel\'s Spider-Man: Miles Morales',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-11-12'),
                'description' => 'Marvel\'s Spider-Man: Miles Morales is an action-adventure game developed by Insomniac Games and published by Sony Interactive Entertainment.',
                'price' => 49.99,
                'editor' => 'Sony Interactive Entertainment'
            ],
            [
                'name' => 'Control',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-08-27'),
                'description' => 'Control is an action-adventure video game developed by Remedy Entertainment and published by 505 Games.',
                'price' => 39.99,
                'editor' => '505 Games'
            ],
            [
                'name' => 'The Outer Worlds',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-10-25'),
                'description' => 'The Outer Worlds is an action role-playing game developed by Obsidian Entertainment and published by Private Division.',
                'price' => 39.99,
                'editor' => 'Private Division'
            ],
            [
                'name' => 'Sea of Thieves',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2018-03-20'),
                'description' => 'Sea of Thieves is an action-adventure game developed by Rare and published by Xbox Game Studios.',
                'price' => 39.99,
                'editor' => 'Xbox Game Studios'
            ],
            [
                'name' => 'Hades',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-09-17'),
                'description' => 'Hades is a roguelike action dungeon crawler video game developed and published by Supergiant Games.',
                'price' => 24.99,
                'editor' => 'Supergiant Games'
            ],
            [
                'name' => 'Borderlands 3',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-09-13'),
                'description' => 'Borderlands 3 is an action role-playing first-person shooter video game developed by Gearbox Software and published by 2K Games.',
                'price' => 49.99,
                'editor' => '2K Games'
            ],
            [
                'name' => 'Days Gone',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2019-04-26'),
                'description' => 'Days Gone is an action-adventure survival horror video game developed by Bend Studio and published by Sony Interactive Entertainment.',
                'price' => 39.99,
                'editor' => 'Sony Interactive Entertainment'
            ],
            [
                'name' => 'The Sims 4',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2014-09-02'),
                'description' => 'The Sims 4 is a life simulation video game developed by Maxis and published by Electronic Arts.',
                'price' => 39.99,
                'editor' => 'Electronic Arts'
            ],
            [
                'name' => 'Detroit: Become Human',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2018-05-25'),
                'description' => 'Detroit: Become Human is an adventure game developed by Quantic Dream and published by Sony Interactive Entertainment.',
                'price' => 39.99,
                'editor' => 'Sony Interactive Entertainment'
            ],
            [
                'name' => 'Star Wars: Squadrons',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-10-02'),
                'description' => 'Star Wars: Squadrons is a space combat game developed by Motive Studios and published by Electronic Arts.',
                'price' => 39.99,
                'editor' => 'Electronic Arts'
            ],
            [
                'name' => 'Tony Hawk\'s Pro Skater 1 + 2',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-09-04'),
                'description' => 'Tony Hawk\'s Pro Skater 1 + 2 is a skateboarding video game developed by Vicarious Visions and published by Activision.',
                'price' => 39.99,
                'editor' => 'Activision'
            ],
            [
                'name' => 'Marvel\'s Spider-Man: Miles Morales',
                'picture' => 'https://picsum.photos/200',
                'release_date' => new \DateTimeImmutable('2020-11-12'),
                'description' => 'Marvel\'s Spider-Man: Miles Morales is an action-adventure game developed by Insomniac Games and published by Sony Interactive Entertainment.',
                'price' => 49.99,
                'editor' => 'Sony Interactive Entertainment'
            ],
        ];

        // Création des jeux
        $games = [];
        foreach ($gamesData as $gameData) {
            $game = new Game();
            $game->setName($gameData['name'])
                ->setPicture($gameData['picture'])
                ->setReleaseDate($gameData['release_date'])
                ->setDescription($gameData['description'])
                ->setPrice($gameData['price'])
                ->setEditor($gameData['editor']);

            // Troncature de la description à un maximum de 255 caractères
            $description = $gameData['description'];
            if (strlen($description) > 255) {
                $description = substr($description, 0, 252) . '...';
            }
            $game->setDescription($description);

            // Assigner des catégories aléatoires au jeu
            $randomCategoryKeys = array_rand($categoryEntityList, min(3, count($categoryEntityList)));
            if (!is_array($randomCategoryKeys)) {
                $randomCategoryKeys = [$randomCategoryKeys];
            }
            foreach ($randomCategoryKeys as $key) {
                $game->addHasCategory($categoryEntityList[$key]);
            }

            // Assigner des tags aléatoires au jeu
            $randomTagKeys = array_rand($tagEntityList, min(5, count($tagEntityList)));
            if (!is_array($randomTagKeys)) {
                $randomTagKeys = [$randomTagKeys];
            }
            foreach ($randomTagKeys as $key) {
                $game->addHasTag($tagEntityList[$key]);
            }
            $manager->persist($game);
            $games[] = $game;
        }

        // Création d'un utilisateur administrateur spécifique
        $adminUser = new User();
        $adminUser->setFirstname('Admin');
        $adminUser->setLastname('User');
        $adminUser->setNickname('admin');
        $adminUser->setPicture('https://picsum.photos/200/300');
        $adminUser->setEmail('admin@oplay.fr');
        $hashedAdminPassword = $this->passwordHasher->hashPassword($adminUser, 'admin'); // Hashage du mot de passe 'admin'
        $adminUser->setPassword($hashedAdminPassword);
        $adminUser->setChooseTheme($themes[array_rand($themes)]);
        $adminUser->setRoles(['ROLE_ADMIN']); // Attribution du rôle ROLE_ADMIN à cet utilisateur
        $manager->persist($adminUser);

        $manager->flush();

        // Création de 20 utilisateurs
        $users = [];
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setNickname($faker->userName);
            $user->setPicture('https://picsum.photos/200/300');
            $user->setEmail($faker->email);
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password'); // Utilisation d'un mot de passe simple à des fins de test
            $user->setPassword($hashedPassword);
            $user->setChooseTheme($themes[array_rand($themes)]);
            $user->setRoles([$faker->randomElement(['ROLE_ADMIN', 'ROLE_USER'])]); // Attribution aléatoire des rôles

        // Assignation de catégories aléatoires à l'utilisateur
        $randomCategoryKeys = array_rand($categoryEntityList, min(3, count($categoryEntityList)));
        if (!is_array($randomCategoryKeys)) {
            $randomCategoryKeys = [$randomCategoryKeys];
        }
        foreach ($randomCategoryKeys as $key) {
            $user->addSelectedCategory($categoryEntityList[$key]);
        }

        // Assignation de tags aléatoires à l'utilisateur
        $randomTagKeys = array_rand($tagEntityList, min(5, count($tagEntityList)));
        if (!is_array($randomTagKeys)) {
            $randomTagKeys = [$randomTagKeys];
        }
        foreach ($randomTagKeys as $key) {
            $user->addPreferedTag($tagEntityList[$key]);
        }
        $manager->persist($user);
        $users[] = $user;

        // Création de commandes pour chaque utilisateur
        $order = new Order();
        $order->setUser($user);
        $order->setStatus('pending');
        $order->setCreatedAt(new \DateTimeImmutable());

        // Ajouter des GameOrder aléatoires à la commande
        $randomGames = $faker->randomElements($games, mt_rand(1, 5));
        foreach ($randomGames as $game) {
            $gameOrder = new GameOrder();
            $gameOrder->setGame($game);
            $gameOrder->setOrder($order);
            $gameOrder->setQuantity(mt_rand(1, 3)); // Générer une quantité aléatoire
            $gameOrder->setTotalPrice($game->getPrice() * $gameOrder->getQuantity());
            $order->addGameOrder($gameOrder);
            $manager->persist($gameOrder);
        }
        // Calculer le total de la commande
        $total = 0;
        foreach ($order->getGameOrders() as $gameOrder) {
            $total += $gameOrder->getTotalPrice();
        }
        $order->setTotal($total);

        // Persiste la commande
        $manager->persist($order);
    }

    $manager->flush();

    // Rechercher les commandes en cours pour chaque utilisateur et valider les commandes
    foreach ($users as $user) {
    $orderRepository = $manager->getRepository(Order::class);;
    // Rechercher les commandes en attente pour l'utilisateur
    $pendingOrders = $orderRepository->findBy([
        'user' => $user,
        'status' => 'pending'
    ]);

    foreach ($pendingOrders as $order) {
        // Générer et associer des clés aléatoires aux jeux dans le panier
        foreach ($order->getGameOrders() as $gameOrder) {
            $game = $gameOrder->getGame();

            // Générer une clé par quantité commandée
            for ($j = 0; $j < $gameOrder->getQuantity(); $j++) {
                $activationKey = $this->generateActivationKey();

                // Créer une instance de UserGameKey
                $userGameKey = new UserGameKey();
                $userGameKey->setUser($user);
                $userGameKey->setGame($game);
                $userGameKey->setGameKey($activationKey);
                $userGameKey->setCreatedAt($order->getCreatedAt()); // Utiliser la date de création de la commande

                // Persiste l'entité UserGameKey
                $manager->persist($userGameKey);
            }
        }

        // Mettre à jour le statut de la commande à 'validated'
        $order->setStatus('validated');
    }
}

// Flush des changements dans la base de données
$manager->flush();
}

// Méthode pour générer une clé aléatoire de jeu
private function generateActivationKey(): string
{
    return bin2hex(random_bytes(16)); // Exemple de clé hexadécimale de 16 octets
}
}

