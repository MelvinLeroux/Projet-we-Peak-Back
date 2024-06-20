<?php

namespace App\Controller\Admin;

use App\Entity\Difficulty;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class DifficultyCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Difficulty::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud

            ->setEntityLabelInPlural('Difficultés');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('value')->setLabel('Nom'),
            TextField::new('label')->setLabel('Nom affiché'),
            DateTimeField::new('createdAt')
                ->onlyOnIndex()
                ->setLabel('Créé le'),
            DateTimeField::new('updatedAt')
                ->onlyOnIndex()
                ->setLabel('Modifié le'),
            CollectionField::new('sports')
                ->onlyOnDetail()
                ->onlyOnIndex(),
            AssociationField::new('sports')
                ->onlyOnForms()
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('sports')->setFormTypeOption('mapped', false)->setLabel('Sports'));
    } 
}
