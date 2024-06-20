<?php

namespace App\Services;

use App\Entity\Activity;
use App\Entity\Pictures;
use App\Entity\User;
use DateInvalidOperationException;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Vich\UploaderBundle\Naming\Base64Namer;

class PicturesService
{
    private $entityManager;
    private $pictureDirectory;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger, string $pictureDirectory)
    {
        $this->entityManager = $entityManager;
        $this->pictureDirectory = $pictureDirectory;
    }

    function createPicture(array $data, EntityManagerInterface $entityManager,Base64FileExtractor $base64FileExtractor): array
    {
        $createdPictures = [];
        // Vérifier si des liens d'images sont fournis
        if (isset($data['picturefiles'])) {
            foreach ($data['picturefiles'] as $linkBase64) {
                $picture = new Pictures();
                $linkBase64 = $base64FileExtractor->extractBase64String($linkBase64);
                $pictureName =  new UploadedBase64File($linkBase64, "blabla");
                $picture->setLink($pictureName);
                // Récupérer l'activité correspondante
                $activityId = $data['activity']['id'];
                $activity = $entityManager->getRepository(Activity::class)->find($activityId);
    
                // Récupérer l'utilisateur créateur de l'activité
                $user = $activity->getCreatedBy();
                $picture->setActivity($activity);
    
                // Récupérer l'utilisateur à partir de son ID fourni dans les données
                $user = $entityManager->getRepository(User::class)->find($data['user']['id']);
                $picture->setUser($user);
                
                // Définir la date de création de l'image
                $picture->setCreatedAt(new \DateTimeImmutable());
                // Persister l'entité image et la stocker dans le tableau $createdPictures
                $entityManager->persist($picture);
                $createdPictures[] = $picture;
            }
            // Enregistrer les modifications dans la base de données
            $entityManager->flush();
        }
        // Retourner le tableau des images créées, même si aucun lien d'image n'est fourni
        return $createdPictures;
    }       // Vérifier si la date de l'activité est passée}
    


    
    

    public function updatePicture(Pictures $picture, array $data): Pictures
    {
        if (isset($data['link'])) {
            $picture->setLink($data['link']);
        }

        // Gestion du fichier picture
        if (isset($data['pictureFile'])) {
            $pictureFileName = $data['pictureFile'];
            $picture->setLink($pictureFileName); // Définir le nom du fichier picture
                
            // Créer une instance UploadedFile pour le fichier picture
            $pictureFilePath = $this->pictureDirectory . $pictureFileName;
            $pictureFile = new UploadedFile(
                $pictureFilePath, $pictureFileName
            );

            // Définir le fichier picture sur l'entité Picture
            $picture->setPictureFile($pictureFile);
        }

        return $picture;
    }

    
}