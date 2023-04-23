<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $id = $options['data']->getId();
        $requiredPasswordStar = $id === null ? '*' : '';
        $isRequired = $id === null;

        $passwordConstraints[] = new Length([
            'min' => 6,
            'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
            'max' => 4096,
        ]);

        if ($isRequired) {
            $passwordConstraints[] = new NotBlank([
                'message' => 'Veuillez entrer un mot de passe',
            ]);
        }

        $builder
            ->add('username', TextType::class, [
                'label' => 'Pseudo*'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email*'
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles*',
                'choices'  => [
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
                    'ROLE_USER' => 'ROLE_USER',
                ],
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => $id === null,
                'first_options'  => ['label' => 'Mot de passe'.$requiredPasswordStar,'attr' => ['autocomplete' => 'new-password']],
                'second_options' => ['label' => 'Confirmer le mot de passe'.$requiredPasswordStar,'attr' => ['autocomplete' => 'new-password']],
                'constraints' => $passwordConstraints,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
