<?php

namespace App\Form;

use App\Entity\Movement;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de mouvement',
                'choices' => [
                    '📥 Entrée de stock' => 'ENTREE',
                    '📤 Sortie de stock' => 'SORTIE',
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => function(Product $product) {
                    return $product->getReference() . ' - ' . $product->getName();
                },
                'label' => 'Produit',
                'attr' => ['class' => 'form-select']
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
                'attr' => ['placeholder' => 'Nombre d\'unités', 'min' => 1]
            ])
            ->add('destination', TextType::class, [
                'label' => 'Destination / Provenance',
                'required' => false,
                'attr' => ['placeholder' => 'Client, Magasin, Fournisseur...']
            ])
            ->add('reference', TextType::class, [
                'label' => 'Référence (Commande, Bon de livraison)',
                'required' => false,
                'attr' => ['placeholder' => 'N° de commande, bon...']
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes / Commentaires',
                'required' => false,
                'attr' => ['rows' => 3, 'placeholder' => 'Informations complémentaires...']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movement::class,
        ]);
    }
}
