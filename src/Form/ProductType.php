<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, [
                'label' => 'Référence',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: KAZM-001'],
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Brume Corporelle'],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité initiale',
                'attr' => ['class' => 'form-control', 'placeholder' => '0'],
            ])
            ->add('alertThreshold', IntegerType::class, [
                'label' => 'Seuil d\'alerte',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: 10'],
            ])
            ->add('supplier', TextType::class, [
                'label' => 'Fournisseur',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom du fournisseur'],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'placeholder' => 'Sélectionnez une catégorie',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('scent', ChoiceType::class, [
                'label' => 'Senteur',
                'placeholder' => 'Choisir une senteur',
                'choices' => [
                    '🍊 Fleur d\'Orange' => 'fleur-orange',
                    '🌺 Monoï' => 'monoi',
                    '🌹 Rose Matik' => 'rose-matik',
                    '🍓 Fruit Rouge' => 'fruit-rouge',
                    '🤍 Misk Blanc' => 'misk-blanc',
                    '🪵 Oud' => 'oud',
                    '💛 Amber' => 'amber',
                    '🌸 Oud Jasmin' => 'oud-jasmin',
                ],
                'attr' => ['class' => 'form-select'],
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'label' => 'Photo du produit',
                'attr' => ['class' => 'form-control'],
                'required' => false,
                // important : ne pas le lier directement à la propriété "image" (string)
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG ou WEBP)',
                    ]),
                ],
                'help' => 'Formats acceptés: JPG, PNG, WEBP (max 5MB)',
                'help_attr' => ['class' => 'form-text text-muted'],
            ])
            ->add('entryDate', DateType::class, [
                'label' => 'Date d\'entrée',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
