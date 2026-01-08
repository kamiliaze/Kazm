<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Entrez le prénom']
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Entrez le nom']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'exemple@kazmstock.com']
            ])
            ->add('cin', TextType::class, [
                'label' => 'CIN',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: IB257900']
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: 0612345678']
            ])
            ->add('office', TextType::class, [
                'label' => 'Bureau',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Bureau 25']
            ])
            ->add('hireDate', DateType::class, [
                'label' => 'Date d\'embauche',
                'required' => false,
                'widget' => 'single_text'
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'mapped' => false,
                'required' => false,
                'attr' => ['placeholder' => 'Laissez vide pour conserver le mot de passe actuel']
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    '🔴 ADMIN - Super administrateur' => 'ROLE_ADMIN',
                    '🔴 DIRECTEUR - Accès total' => 'ROLE_DIRECTEUR',
                    '🟠 MANAGER - Gestion opérationnelle' => 'ROLE_MANAGER',
                    '🟡 SENIOR - Contributeur expérimenté' => 'ROLE_SENIOR',
                    '🟢 JUNIOR - Consultation uniquement' => 'ROLE_USER',
                ],
                'multiple' => false,
                'expanded' => false,
            ]);
        
        // Transformer pour convertir array en string et vice-versa
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // Transform array to string (pour afficher dans le formulaire)
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // Transform string to array (pour sauvegarder)
                    return $rolesString ? [$rolesString] : [];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
