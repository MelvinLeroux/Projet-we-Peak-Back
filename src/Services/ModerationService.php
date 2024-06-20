<?php
namespace App\Services;

class ModerationService
{
    private $motsInterdits;

    public function __construct()
    {
        // Initialisez le tableau de mots interdits directement dans le constructeur du service
        $this->motsInterdits = [ 'Putain', 'Enculé', 'Connard', 'Salope', 
        'Pédé', 'Fils de pute', 'Bite', 'Couille', 
        'Niquer', 'Enculée', 'Sucer', 'Enculeur', 'Chienne', 'Baiser',
        'Pute', 'Bâtard', 'Pénis', 'Fellation', 'Orgasme', 'Nudité', 'Porno', 
        'Gros con', 'con','Froisser', 'Bitch',
        'Chibre', 'Cochonne', 'Queue', 'Tromper', 'Éjaculation',
        'Turlutte', 'Salaud', 'Salop', 'Tapette',
        'Bourré', 'Tête de nœud', 'Pétasse', 'Défonce',
        'Connasse', 'Suceur', 'Suceuse', 'Pute',
        'Conne', 'Nique ta mère', 'Gouine', 
        'Défoncer', 'Niquer', 'Brouter', 
        'Encule', 'Enculé', 'Éjaculer', 'Ejac', 'Foutre',
        'Chibre', 'Culot', 'Culs', 'Culottée',
        'Érection', 'Nudité', 'Sexe', 'Sexuel', 'Sex', 'Bisexuel',
        'Bisex', 'Bi', 'Hétéro', 'Homo', 'Homosexuel',
        'Bite', 'Branler', 'Branlette', 'Branle', 'Branleuse',
        'Branlée', 'Bourrer', 'Bourrage', 'Bourré', 
        'Cocu', 'Cocus', 'Cunnilingus', 'Cunni',
        'Cul', 'Sodomie', 'Salope', 'Salaud',
        'Salop', 'Salopards', 'Suce', 'Sucer', 'Suceuse',
        'Turlute', 'Turlutte', 'Zizi', 'Zézette', 'Fellations',
        'Pénétration', 'Plaisir', 'Orgasme', 'Ejac', 'Giclée',
        'Foutre', 'Foutaise', 'Foutaises', 'Vulve', 'Vagin',
        'Sexe', 'Sexuel', 'Sexe', 'Bisexuel',
        'Bisex', 'Bi', 'Hétéro', 'Homo', 'Homosexuel',
        'Bite', 'Branlette', 'Branleuse',
        'Branlée', ];
    }

    public function detecterMotInterdit(string $texte): bool
    {
        foreach ($this->motsInterdits as $mot) {
            if (stripos($texte, $mot) !== false) {
                return true;
            }
        }
        return false;
    }
}