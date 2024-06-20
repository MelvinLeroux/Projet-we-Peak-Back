<?php

namespace App\Services\Validations;

use Error;

class GenericValidationService
{
    public function validateRequiredFields(array $data, array $requiredFields, array $errorMessages = [])
    {
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errorMessage = $errorMessages[$field] ?? "Le champ '$field' est requis.";
                throw new Error($errorMessage);
            }
        }
    }

    public function validateNumeric($value, $fieldName)
    {
        if (!is_numeric($value)) {
            throw new Error("Le champ '$fieldName' doit être un nombre.");
        }
    }

    // Autres méthodes de validation génériques...
}