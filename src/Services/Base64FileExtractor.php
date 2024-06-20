<?php 

namespace App\Services;

class Base64FileExtractor    
    {

        public function extractBase64String(string $base64Content)
        {
            $data = explode(';base64,', $base64Content);
            if (count($data) === 2) {
                return $data[1]; // Retourne uniquement les données encodées en base64 après le délimiteur
            } else {
                // Gérer le cas où le format de la chaîne n'est pas conforme
                throw new \InvalidArgumentException("Le format de la chaîne base64 est incorrect.");
            }
        }

    }