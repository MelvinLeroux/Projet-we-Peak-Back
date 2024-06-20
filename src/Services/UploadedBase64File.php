<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadedBase64File extends UploadedFile
{

    private $relativePath;

    public function __construct(string $base64String, string $originalName)
    {
        // Chemin relatif du répertoire de destination dans votre projet Symfony
        $directory = '/images'; // Chemin relatif du répertoire
        $destinationDirectory = $_SERVER['DOCUMENT_ROOT'] . $directory;

        // Vérification si le répertoire existe, sinon le créer
        if (!file_exists($destinationDirectory)) {
            mkdir($destinationDirectory, 0755, true);
        }

        // Nom du fichier
        $filename = uniqid('uploaded_file_', true) . '.' . pathinfo($originalName, PATHINFO_EXTENSION);
        $filePath = $destinationDirectory . '/' . $filename;

        // Décode la chaîne base64 et écrit les données dans le fichier
        $data = base64_decode($base64String);
        file_put_contents($filePath, $data);

        // Stocker le chemin relatif du fichier
        $this->relativePath = $directory . '/' . $filename;

        // Appel du constructeur parent avec le chemin du fichier local
        parent::__construct($filePath, $originalName);
    } 

    public function getRelativePath(): string
    {
        return $this->relativePath;
    }
}
