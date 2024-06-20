<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class UserCrudController extends AbstractCrudController
{
    use Trait\DeleteOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud

            ->setEntityLabelInPlural('Utilisateurs');
    }

    
    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id'),
            TextField::new('pseudo'),
            EmailField::new('email'),
            TextField::new('firstname')->setLabel('Prénom'),
            TextField::new('lastname')->setLabel('Nom'),
            TextField::new('description'),
            DateField::new('birthdate')->setLabel('Date de naissance'),
            TextField::new('city')->setLabel('Ville'),
            ImageField::new('thumbnail')->setLabel('Image'),
            DateTimeField::new('createdAt')->setLabel('Créé le'),
            DateTimeField::new('updatedAt')->setLabel('Modifié le'),
            AssociationField::new('sports')->setLabel('Sports')
                ->onlyOnIndex(),
            ArrayField::new('sports')->setLabel('Sports')
                ->onlyOnDetail(),

            AssociationField::new('comments')->setLabel('Commentaires'),
            
        ];

        // Champ 'getActivitiesCreated' pour l'index
        $fields[] = IntegerField::new('getActivitiesCreatedCount', 'Activities created')->onlyOnIndex();

        // Champ 'getActivitiesCreated' pour les détails
        $fields[] = ArrayField::new('getActivitiesCreated', 'Activities created')
            ->onlyOnDetail()
            ->formatValue(function ($value, $entity) {
                // Récupérer les noms des activités
                $activityNames = [];
                foreach ($entity->getActivitiesCreated() as $activity) {
                    $activityNames[] = $activity->getName(); // Assurez-vous que votre entité d'activité a une méthode getName() pour récupérer le nom
                }
                return implode(', ', $activityNames);
            });
    
            // Champ 'participations' pour l'index
        $fields[] = AssociationField::new('participations')->onlyOnIndex();

        // Champ 'participations' pour les détails
        $fields[] = ArrayField::new('participations')
            ->onlyOnDetail()
            ->formatValue(function ($value, $entity) {
                // Récupérer les noms des activités correspondantes aux participations
                $activityNames = [];
                foreach ($entity->getParticipations() as $participation) {
                    $activityNames[] = $participation->getActivity()->getName(); // Assurez-vous que votre entité de participation a une méthode getActivity() pour récupérer l'activité associée
                }
                return implode(', ', $activityNames);
            });

        return $fields;
    }

    public function configureFilters(Filters $filters): Filters
    {

        return $filters
            ->add(TextFilter::new('city')->setLabel('Ville'))
            ->add(TextFilter::new('firstname')->setLabel('Prénom'))
            ->add(TextFilter::new('lastname')->setLabel('Nom'));
    }
       
}