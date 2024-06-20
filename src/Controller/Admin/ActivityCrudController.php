<?php

namespace App\Controller\Admin;

use App\Entity\Activity;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class ActivityCrudController extends AbstractCrudController
{
    use Trait\DeleteOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return Activity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud

            ->setEntityLabelInPlural('Activités');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('name')->setLabel('Nom'),
            TextField::new('description'),
            DateTimeField::new('date'),
            IntegerField::new('groupSize')->setLabel('Taille du groupe'),
            DateTimeField::new('createdAt')->setLabel('Créé le'),
            DateTimeField::new('updatedAt')->setLabel('Modifié le'),
            ImageField::new('thumbnail')->setLabel('Image'),
            AssociationField::new('createdBy')->setLabel('Créateur'),
            CollectionField::new('sports'),
            TextField::new('difficulty')->setLabel('Difficulté'),
            AssociationField::new('comments')->setLabel('Commentaires'),            
        ];
    }
    
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('createdBy')->setFormTypeOption('mapped', false)->setLabel('Créateur'))
            ->add(EntityFilter::new('sports')->setFormTypeOption('mapped', false)->setLabel('Sports'))
            ->add(EntityFilter::new('difficulty')->setFormTypeOption('mapped', false)->setLabel('Difficulté'));
    }  
}
