<?php

namespace App\Controller\Admin;

use App\Entity\Pictures;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class PicturesCrudController extends AbstractCrudController
{

    use Trait\DeleteOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return Pictures::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud

            ->setEntityLabelInPlural('Photos');
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            ImageField::new('link')->setLabel('Image'),
            AssociationField::new('user')->setLabel('Postée par'),
            AssociationField::new('activity')->setLabel('Activité'),
            DateTimeField::new('createdAt')
                ->onlyOnDetail()
                ->setLabel('Créé le'),
            DateTimeField::new('updatedAt')
                ->onlyOnDetail()
                ->setLabel('Modifié le'),
        ];
    }
    
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('user')->setFormTypeOption('mapped', false)->setLabel('Utilisateur'))
            ->add(EntityFilter::new('activity')->setFormTypeOption('mapped', false)->setLabel('Activité'));
    }  
}
