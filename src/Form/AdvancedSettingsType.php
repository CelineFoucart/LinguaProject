<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AdvancedSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('APP_NAME', TextType::class, [
                'label' => "Nom de l'application*",
            ])
            ->add('APP_LANGUAGE_NAME', TextType::class, [
                'label' => 'Nom de la langue*'
            ])
            ->add('APP_ORIGINAL_LANGUAGE_NAME', TextType::class, [
                'label' => 'Nom de la langue en version originale',
                'required' => false,
            ])
            ->add('APP_DESCRIPTION', TextType::class, [
                'label' => "Description",
                'required' => false,
                'help' => "Description rapide de moins de 160 caractères, utilisé pour le référencement"
            ])
            ->add('PER_PAGE_INDEX', IntegerType::class, [
                'label' => "Eléments par page sur l'index",
                'help' => "Nombre d'éléments à afficher par page sur l'index des articles"
            ])
            ->add('PER_PAGE_CATEGORY', IntegerType::class, [
                'label' => "Eléments par page sur la catégorie",
                'help' => "Nombre d'éléments à afficher par page sur l'index des articles de la page par catégorie"
            ])
            ->add('bannerFile', FileType::class, [
                'label' => 'Bannière',
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxWidth' => 1900,
                        'minHeight' => 400,
                    ])
                ],
                'help' => "La bannière doit faire au maximum 1400 pixels de large et au minimum 400 pixels de hauteur.",
            ])
            ->add('faviconFile', FileType::class, [
                'label' => 'Favicon',
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxWidth' => 160,
                        'maxHeight' => 160,
                    ]),
                ],
                'help' => "Le favicon doit faire au maximum 160 pixels de large et 160 pixels de hauteur.",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
