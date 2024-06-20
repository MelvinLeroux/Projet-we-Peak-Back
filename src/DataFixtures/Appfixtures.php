<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Comment;
use App\Entity\Difficulty;
use App\Entity\Filter;
use App\Entity\Participation;
use App\Entity\Pictures;
use App\Entity\Sport;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class Appfixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    private SluggerInterface $slugger;

    private array $cities = [
        'Bourg-en-Bresse' => ['latitude' => 46.2057, 'longitude' => 5.2258],
        'Saint-Étienne' => ['latitude' => 45.4397, 'longitude' => 4.3872],
        'Valence' => ['latitude' => 44.9334, 'longitude' => 4.8929],
        'Grenoble' => ['latitude' => 45.1885, 'longitude' => 5.7245],
        'Mâcon' => ['latitude' => 46.3069, 'longitude' => 4.8283],
        'Chambéry' => ['latitude' => 45.5645, 'longitude' => 5.9175],
        'Annecy' => ['latitude' => 45.8992, 'longitude' => 6.1294],
        'Vienne' => ['latitude' => 45.5300, 'longitude' => 4.8786],
        'Roanne' => ['latitude' => 46.0368, 'longitude' => 4.0711],
        'Villefranche-sur-Saône' => ['latitude' => 45.9884, 'longitude' => 4.7216],
        'Montélimar' => ['latitude' => 44.5588, 'longitude' => 4.7495],
        'Autun' => ['latitude' => 46.9496, 'longitude' => 4.2983],
        'Tarare' => ['latitude' => 45.8947, 'longitude' => 4.4322],
        'Annonay' => ['latitude' => 45.2393, 'longitude' => 4.6763],
        'Bourgoin-Jallieu' => ['latitude' => 45.5848, 'longitude' => 5.2732],
        'Tournus' => ['latitude' => 46.5703, 'longitude' => 4.9125],
        'Crest' => ['latitude' => 44.7273, 'longitude' => 5.0243],
        'Nyons' => ['latitude' => 44.3592, 'longitude' => 5.1426],
        'Belley' => ['latitude' => 45.7550, 'longitude' => 5.6863],
        'Ambérieu-en-Bugey' => ['latitude' => 45.9562, 'longitude' => 5.3574],
        'Trévoux' => ['latitude' => 45.9336, 'longitude' => 4.7661],
        'Pérouges' => ['latitude' => 45.9030, 'longitude' => 5.1772],
        'Le Puy-en-Velay' => ['latitude' => 45.0419, 'longitude' => 3.8831],
        'Cluny' => ['latitude' => 46.4372, 'longitude' => 4.6591],
        'Crémieu' => ['latitude' => 45.7222, 'longitude' => 5.2561],
        'Péage-de-Roussillon' => ['latitude' => 45.3778, 'longitude' => 4.7688],
        'Pont-de-Vaux' => ['latitude' => 46.4183, 'longitude' => 4.9296],
        'Villefranche-sur-Mer' => ['latitude' => 43.7034, 'longitude' => 7.3052],
        'Aix-les-Bains' => ['latitude' => 45.6888, 'longitude' => 5.9150],
        'Romans-sur-Isère' => ['latitude' => 45.0460, 'longitude' => 5.0538],
        'Megève' => ['latitude' => 45.8576, 'longitude' => 6.6153],
        'Combloux' => ['latitude' => 45.8925, 'longitude' => 6.6461],
        'Passy' => ['latitude' => 45.9361, 'longitude' => 6.7006],
        'Saint-Gervais-les-Bains' => ['latitude' => 45.8919, 'longitude' => 6.7018],
        'Les Houches' => ['latitude' => 45.8934, 'longitude' => 6.7914],
        'Domancy' => ['latitude' => 45.8989, 'longitude' => 6.6500],
        'Cordon' => ['latitude' => 45.9250, 'longitude' => 6.6199],
        'Demi-Quartier' => ['latitude' => 45.8883, 'longitude' => 6.6339],
        'Capbreton' => ['latitude' => 43.6448, 'longitude' => -1.4370],
        'Hossegor' => ['latitude' => 43.6631, 'longitude' => -1.4370],
        'Seignosse' => ['latitude' => 43.6923, 'longitude' => -1.3984],
        'Labenne' => ['latitude' => 43.5924, 'longitude' => -1.4509],
        'Ondres' => ['latitude' => 43.5595, 'longitude' => -1.4346],
        'Tarnos' => ['latitude' => 43.5397, 'longitude' => -1.4641],
        'Boucau' => ['latitude' => 43.5130, 'longitude' => -1.4672],
        'Bayonne' => ['latitude' => 43.4933, 'longitude' => -1.4746],
        'Anglet' => ['latitude' => 43.4857, 'longitude' => -1.5232],
        'Biarritz' => ['latitude' => 43.4832, 'longitude' => -1.5586],
        'Lille' => ['latitude' => 50.6293, 'longitude' => 3.0573],
        'Tourcoing' => ['latitude' => 50.7235, 'longitude' => 3.1615],
        'Roubaix' => ['latitude' => 50.6916, 'longitude' => 3.1746],
        'Marcq-en-Barœul' => ['latitude' => 50.6728, 'longitude' => 3.0977],
        'La Madeleine' => ['latitude' => 50.6513, 'longitude' => 3.0664],
        'Croix' => ['latitude' => 50.6782, 'longitude' => 3.1455],
        'Wambrechies' => ['latitude' => 50.6839, 'longitude' => 3.0315],
        'Wasquehal' => ['latitude' => 50.6677, 'longitude' => 3.1234],
        'Halluin' => ['latitude' => 50.7977, 'longitude' => 3.1333],
        'Lambersart' => ['latitude' => 50.6515, 'longitude' => 3.0178],
        'La Ravoire' => ['latitude' => 45.5699, 'longitude' => 5.9344],
        'Barberaz' => ['latitude' => 45.5736, 'longitude' => 5.9021],
        'Saint-Alban-Leysse' => ['latitude' => 45.5606, 'longitude' => 5.9539],
        'Challes-les-Eaux' => ['latitude' => 45.5627, 'longitude' => 5.9718],
        'Montmélian' => ['latitude' => 45.4870, 'longitude' => 5.9204],
        'Chambéry-le-Vieux' => ['latitude' => 45.6048, 'longitude' => 5.9181],
        'Le Bourget-du-Lac' => ['latitude' => 45.6539, 'longitude' => 5.8507],
        'Sallanches' => ['latitude' =>  45.9228, 'longitude' => 6.7015],
    ]; 

    public function __construct(UserPasswordHasherInterface $hasher, SluggerInterface $slugger)
    {
        $this->hasher = $hasher;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $faker->addProvider(new \Smknstd\FakerPicsumImages\FakerPicsumImagesProvider($faker));

        $users = $this->loadUsers($manager, $faker);

        $admin = $this->loadAdmin($manager, $faker);

        $activities = $this->loadActivities($manager, $faker, $users);

        $this->loadPictures($manager, $faker, $activities, $users);

        $this->loadSportsAndDifficulties($manager, $faker, $activities, $users);

        $this->loadParticipations($manager, $faker, $activities, $users);

        $this->loadFilters($manager, $faker);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager, $faker)
    {
        $users = [];
        $sports = ['escalade','ski de randonnée'];
        
        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $pseudo = $faker->userName();
            $user->setPseudo($pseudo);
            $user->setPassword($this->hasher->hashPassword($user, 'wepeak'));
            $user->setDescription($faker->text(255));
            $user->setRoles(['ROLE_USER']);
            $user->setEmail($faker->email);
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setBirthdate($faker->dateTimeBetween('-60 years', '-18 years'));
            $user->setCity($faker->city);
            $user->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime('now')));
            $user->setSlug($this->slugger->slug($pseudo));
            $imageURL = sprintf('https://source.unsplash.com/200x300/?portrait,person&sig={random_number}', mt_rand(1, 1000));
            $user->setThumbnail($imageURL);
            $user->setAge(true);    

            $manager->persist($user);
            $users[] = $user;
        }
        return $users;
    }

    private function loadAdmin(ObjectManager $manager, $faker)
    {
        $admin = new User();
        $admin->setPseudo('admin');
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setEmail('admin@admin.com');
        $admin->setFirstname('admin');
        $admin->setLastname('admin');
        $admin->setBirthdate($faker->dateTimeBetween('-60 years', '-18 years'));
        $admin->setCity('admin');
        $admin->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime('now')));
        $admin->setSlug($this->slugger->slug('admin'));
        $admin->setAge(true);
        $manager->persist($admin);
        return $admin;
    }

    private function loadActivities(ObjectManager $manager, $faker, $users)
    {
        $activities = [];

        $cityName = array_keys($this->cities);

        
        for ($i = 0; $i < 200; $i++) {
            // Sélectionner aléatoirement une ville du tableau $cities
            
            $randomCity = array_rand($this->cities);
            $coordinates = $this->cities[$randomCity];
            $randomCityName = $faker->randomElement($cityName);
            $coordinates = $this->cities[$randomCityName];
            // Créer une activité pour cette ville
            $activity = new Activity();
            $activity->setName($faker->sentence(3));
            $name = $activity->getName();
            $activity->setDescription($faker->text(1000));
            $activity->setDate($faker->dateTimeBetween('-1 month', '+1 month'));
            $activity->setLat($coordinates['latitude']);
            $activity->setLng($coordinates['longitude']);
            $activity->setCity($randomCityName);
            $activity->setGroupSize($faker->numberBetween(2, 15));
            $activity->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime('now')));
            $randomUser = $faker->randomElement($users);
            $activity->setCreatedBy($randomUser);
            $activity->setSlug($this->slugger->slug($name));
            $imageURL = sprintf('https://source.unsplash.com/200x300/?hiking,mountain&sig={random_number}', mt_rand(1, 1000));
            $activity->setThumbnail($imageURL);
            $manager->persist($activity);
            $activities[] = $activity;
        }

        for ($i = 0; $i < 10; $i++)  {
            $armentieres = $this->cities['Lille'];
            $activity = new Activity();
            $activity->setName($faker->sentence(3));
            $name = $activity->getName();
            $activity->setDescription($faker->text(1000));
            $activity->setDate($faker->dateTimeBetween('-1 month', '+1 month'));
            $activity->setLat($armentieres['latitude']);
            $activity->setLng($armentieres['longitude']);
            $activity->setCity($randomCityName);
            $activity->setGroupSize($faker->numberBetween(2, 10));
            $activity->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime('now')));
            $randomUser = $faker->randomElement($users);
            $activity->setCreatedBy($randomUser);
            $activity->setSlug($this->slugger->slug($name));
            $imageURL = sprintf('https://source.unsplash.com/200x300/?hiking,mountain&sig={random_number}', mt_rand(1, 1000));
            $activity->setThumbnail($imageURL);
            $manager->persist($activity);
            $activities[] = $activity;
        }
        for ($i = 0; $i < 10; $i++)  {
            $sallanches = $this->cities['Sallanches'];
            $activity = new Activity();
            $activity->setName($faker->sentence(3));
            $name = $activity->getName();
            $activity->setDescription($faker->text(1000));
            $activity->setDate($faker->dateTimeBetween('-1 month', '+1 month'));
            $activity->setLat($sallanches['latitude']);
            $activity->setLng($sallanches['longitude']);
            $activity->setCity($randomCityName);
            $activity->setGroupSize($faker->numberBetween(2, 10));
            $activity->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime('now')));
            $randomUser = $faker->randomElement($users);
            $activity->setCreatedBy($randomUser);
            $activity->setSlug($this->slugger->slug($name));
            $imageURL = sprintf('https://source.unsplash.com/200x300/?cross-country-skiing,snow&sig={random_number}', mt_rand(1, 1000));
            $activity->setThumbnail($imageURL);
            $manager->persist($activity);
            $activities[] = $activity;
        }
        for ($i = 0; $i < 10; $i++)  {
            $chambery = $this->cities['Chambéry'];
            $activity = new Activity();
            $activity->setName($faker->sentence(3));
            $name = $activity->getName();
            $activity->setDescription($faker->text(1000));
            $activity->setDate($faker->dateTimeBetween('-1 month', '+1 month'));
            $activity->setLat($chambery['latitude']);
            $activity->setLng($chambery['longitude']);
            $activity->setCity($randomCityName);
            $activity->setGroupSize($faker->numberBetween(2, 10));
            $activity->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime('now')));
            $randomUser = $faker->randomElement($users);
            $activity->setCreatedBy($randomUser);
            $activity->setSlug($this->slugger->slug($name));
            $imageURL = sprintf('https://source.unsplash.com/200x300/?hiking,mountain&sig={random_number}', mt_rand(1, 1000));
            $activity->setThumbnail($imageURL);
            $manager->persist($activity);
            $activities[] = $activity;
        }
        for ($i = 0; $i < 10; $i++)  {
            $seignosse = $this->cities['Seignosse'];
            $activity = new Activity();
            $activity->setName($faker->sentence(3));
            $name = $activity->getName();
            $activity->setDescription($faker->text(1000));
            $activity->setDate($faker->dateTimeBetween('-1 month', '+1 month'));
            $activity->setLat($seignosse['latitude']);
            $activity->setLng($seignosse['longitude']);
            $activity->setCity($randomCityName);
            $activity->setGroupSize($faker->numberBetween(2, 10));
            $activity->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime('now')));
            $randomUser = $faker->randomElement($users);
            $activity->setCreatedBy($randomUser);
            $activity->setSlug($this->slugger->slug($name));
            $imageURL = sprintf('https://source.unsplash.com/200x300/?surfing,sea&sig={random_number}', mt_rand(1, 1000));
            $activity->setThumbnail($imageURL);
            $manager->persist($activity);
            $activities[] = $activity;
        }
        return $activities;
        
    }

    private function loadSportsAndDifficulties(ObjectManager $manager, $faker, $activities, $users)
    {
        $sport1 = $this->createSport($manager, 'Climbing', 'Escalade', $faker);
        $sport2 = $this->createSport($manager, 'back-country skiing', 'Ski de randonnée', $faker);
        $sport3 = $this->createSport($manager, 'hiking', 'Randonnée', $faker);
        $sport4 = $this->createSport($manager, 'surfing', 'Surf', $faker);
        $sport5 = $this->createSport($manager, 'mountain biking', 'VTT', $faker);

        $climbingDifficulties = ['4a', '4b', '4c', '5a', '5b', '5c', '6a', '6b', '6c', '7a', '7b', '7c', '8a', '8b', '8c', '9a', '9a+', '9b', '9b+'];
        $skiDifficulties = [
            'F' => 'Facile',
            'PD' => 'Peu Difficile',
            'AD' => 'Assez Difficile',
            'D' => 'Difficile',
            'TD' => 'Très Difficile',
            'ED' => 'Extrêmement Difficile',
            'EDx' => 'Abominable'
        ];
        $hiking =[
            'T1' => 'T1 : Randonnée',
            'T2' => 'T2 : Randonnée en montagne',
            'T3' => 'T3 : Randonnée en montagne exigeante',
            'T4' => 'T4 : Randonnée alpine',
            'T5' => 'T5 : Randonnée alpine exigeante',
            'T6' => 'T6 : Randonnée alpine extrême'
        ];
        $surfing = ['Débutant', 'Intermédiaire', 'Avancé', 'Expert'];
        $mountainBiking = [
            'S0' => 'S0 : Très Facile',
            'S1' => 'S1 : Facile',
            'S2' => 'S2 : Moyen',
            'S3' => 'S3 : Difficile',
            'S4' => 'S4 : Très Difficile',
            'S5' => 'S5 : Extrêmement Difficile'
        ];

        $this->createDifficultiesForSport($manager, $sport1, $climbingDifficulties, $faker, $climbingDifficulties);
        $this->createDifficultiesForSport($manager, $sport2, array_keys($skiDifficulties), $faker, $skiDifficulties);
        $this->createDifficultiesForSport($manager, $sport3, array_keys($hiking), $faker, $hiking);
        $this->createDifficultiesForSport($manager, $sport4, $surfing, $faker, $surfing);
        $this->createDifficultiesForSport($manager, $sport5, array_keys($mountainBiking), $faker, $mountainBiking);

        $manager->flush();
        foreach ($activities as $activity) {
            
            $randomSport = $faker->randomElement([$sport1, $sport2, $sport3, $sport4, $sport5]);
            $activity->addSport($randomSport);
            $randomDifficulty = $faker->randomElement($randomSport->getDifficulties()->toArray());
            $activity->setDifficulty($randomDifficulty);
    
            $manager->persist($activity);
        }

        $sports = $manager->getRepository(Sport::class)->findAll();
    
        foreach ($users as $user) {
            $randomSports = $faker->randomElements($sports, $faker->numberBetween(1, 4));
            foreach ($randomSports as $sport) {
                $user->addSport($sport);
            }
        
        $manager->persist($user);
    }
        $manager->flush();
    }

    private function createSport(ObjectManager $manager, $name, $label, $faker)
    {
        $sport = new Sport();
        $sport->setName($name);
        $sport->setLabel($label);
        $sport->setSlug($this->slugger->slug($name));
        $sport->setDescription($faker->text(200));
        $sport->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime('now')));
        $manager->persist($sport);
        return $sport;
    }

    private function createDifficultiesForSport(ObjectManager $manager, $sport, $difficulties, $faker, $difficultyLabels)
    {
        foreach ($difficulties as $difficultyValue) {
            $difficulty = new Difficulty();
            $difficulty->setValue($difficultyValue);
            $label = isset($difficultyLabels[$difficultyValue]) ? $difficultyLabels[$difficultyValue] : $difficultyValue;
            $difficulty->setLabel($label);
            $difficulty->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime('now')));
            $difficulty->addSport($sport);
            $sport->addDifficulty($difficulty);
            $manager->persist($difficulty);
        }
        $manager->persist($sport);
    }

    private function loadParticipations(ObjectManager $manager, $faker, $activities, $users)
    {
        foreach ($activities as $activity) {
            $groupSize = $activity->getGroupSize();
            
            // Sélection aléatoire d'un nombre d'utilisateurs pour cette activité
            $numParticipants = $faker->numberBetween(1, $groupSize);
            $participants = $faker->randomElements($users, $numParticipants);
            
            foreach ($participants as $user) {
                // Vérifier si l'utilisateur n'a pas déjà participé à cette activité
                $existingParticipation = $manager->getRepository(Participation::class)->findOneBy([
                    'user' => $user,
                    'activity' => $activity,
                ]);
                
                if (!$existingParticipation) {
                    // Créer une nouvelle participation
                    $participation = new Participation();
                    $participation->setActivity($activity);
                    $participation->setUser($user);
                    $participation->setStatus($faker->numberBetween(0, 1));
                    $manager->persist($participation);

                    $numComments = $faker->numberBetween(1, 3);
                    for ($i = 0; $i < $numComments; $i++) {
                        $comment = new Comment();
                        $comment->setDescription($faker->sentence());
                        $commentUser = $faker->randomElement($users);
                        $comment->setActivity($activity);
                        $comment->setUser($commentUser);
                        $comment->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime('now')));
                        $manager->persist($comment);
                    }
                }
            }
        }
    }

    private function loadFilters(ObjectManager $manager, $faker)
    {
        $this->createFilter($manager, 'groupSize', 'Taille du groupe', ['2-5', '6-10', '11'], $faker);
        $this->createFilter($manager, 'distance', 'Distance', ['10', '50', '100', '150'], $faker);
    }
    
    private function createFilter(ObjectManager $manager, $category, $categoryLabel, $values, $faker)
    {
        foreach ($values as $value) {
            $filter = new Filter();
            $filter->setCategory($category);
            $filter->setCategoryLabel($categoryLabel);
            $filter->setValue($value); // Garder la valeur originale
            
            // Modifier le libellé en fonction de la catégorie du filtre
            $valueLabel = '';
            switch ($category) {
                case 'groupSize':
                    if ($value === '11') {
                        $valueLabel = '11 et plus';
                    } else {
                        $range = explode('-', $value);
                        $valueLabel = "De {$range[0]} à {$range[1]}";
                    }
                    break;
                case 'distance':
                    $valueLabel = "Moins de $value km";
                    break;
                default:
                    $valueLabel = $value; // Par défaut, utiliser la valeur donnée
                    break;
            }
            
            $filter->setValueLabel($valueLabel); // Stocker le libellé modifié
            $filter->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime('now')));
            $manager->persist($filter);
        }
    }

    private function loadPictures(ObjectManager $manager, $faker, $activities, $users)
    {
        foreach ($activities as $activity) {
            $numPictures = $faker->numberBetween(0, 5);
            
            for ($i = 0; $i < $numPictures; $i++) {
                $picture = new Pictures();
                $picture->setActivity($activity);
                $randomUser = $faker->randomElement($users);
                $picture->setUser($randomUser);
                $picture->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime('now')));
                $imageURL = sprintf('https://source.unsplash.com/200x300/?nature,landscape&sig=%d', mt_rand(1, 1000));
                $picture->setLink($imageURL);
                $manager->persist($picture);
            }
        }
    }
}
