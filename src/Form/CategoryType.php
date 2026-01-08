<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', ChoiceType::class, [
                'label' => 'Type de produit',
                'choices' => [
                    'Brume corporelle' => 'Brume corporelle',
                    'Gel douche' => 'Gel douche',
                    'Baume déodorant' => 'Baume déodorant',
                    'Autobronzant' => 'Autobronzant',
                    'Crème corporelle' => 'Crème corporelle',
                ],
                'placeholder' => 'Choisir un type',
            ])
            ->add('description', TextType::class, [
                'required' => false,
            ])
            ->add('location', TextType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
