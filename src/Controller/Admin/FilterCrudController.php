<?php

namespace App\Controller\Admin;

use App\Entity\Filter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FilterCrudController extends AbstractCrudController
{
    use \App\Controller\Admin\Trait\WithoutAddTrait;

    public static function getEntityFqcn(): string
    {
        return Filter::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud

            ->setEntityLabelInPlural('Filtres');
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('category')->setLabel('Catégorie (slug)'),
            TextField::new('categoryLabel')->setLabel('Nom de la catégorie a afficher'),
            TextField::new('value')->setLabel('Valeur (slug)'),
            TextField::new('valueLabel')->setLabel('Nom de la valeur a afficher'),
            DateTimeField::new('createdAt')
                ->onlyOnIndex()
                ->setLabel('Créé le'),
            DateTimeField::new('updatedAt')
                ->onlyOnIndex()
                ->setLabel('Modifié le'),
        ];
    }
    
}
