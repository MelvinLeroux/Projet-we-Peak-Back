<?php

namespace App\Controller\Admin;


use App\Entity\Sport;
use App\Form\DifficultyType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;


class SportCrudController extends AbstractCrudController
{
    use Trait\ShowTrait;

    private $entityManager;


    public static function getEntityFqcn(): string
    {
        return Sport::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud

            ->setEntityLabelInPlural('Sports');
    }

    public function configureFields(string $pageName): iterable
    {
        // Initialize with AssociationField for index and new page
        $difficultiesField = AssociationField::new('difficulties')->setLabel('Choisir parmi les difficultés existantes')
            ->onlyOnForms();
        $newDifficultiesField = CollectionField::new('newDifficulties', 'Créer une nouvelle difficulté')
            ->onlyOnForms()
            ->setFormTypeOptions([
                'entry_type' => DifficultyType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
            
        $fields = [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name')->setLabel('Nom'),
            TextField::new('description'),
            TextField::new('label')->setLabel('Nom affiché'),
            DateTimeField::new('createdAt')->onlyOnIndex()->setLabel('Créé le'),
            DateTimeField::new('updatedAt')->onlyOnIndex()->setLabel('Modifié le'),
            CollectionField::new('difficulties')->onlyOnDetail()->setLabel('Difficultés'),
            AssociationField::new('difficulties')->onlyOnIndex()->setLabel('Difficultés'),
            $difficultiesField
        ];

        if ($pageName === Crud::PAGE_NEW) {
        
            $fields[] = $newDifficultiesField;
        }
    
        return $fields;          
    }
}