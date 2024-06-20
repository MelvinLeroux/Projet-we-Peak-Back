<?php

namespace App\Form;

use App\Entity\Sport;
use App\Entity\Difficulty;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('difficulties', EntityType::class, [
            'class' => Difficulty::class,
            'multiple' => true,
            'expanded' => true, // Affiche les choix sous forme de cases à cocher
            'required' => false,
            'by_reference' => false,
            'label' => 'Existing Difficulties',
            ])
            ->add('newDifficulties', CollectionType::class, [
                'entry_type' => DifficultyType::class,
                'allow_add' => true,
                'by_reference' => false,
                'label' => 'New Difficulties',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sport::class,
        ]);
    }
}