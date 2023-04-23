<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('languageTranslatedName', TextType::class, [
                'label' => 'Nom de la langue*'
            ])
            ->add('languageOriginalName', TextType::class, [
                'label' => 'Nom de la langue en version originale',
                'required' => false,
            ])
            ->add('languageDescription', TextareaType::class, [
                'label' => 'Description rapide de la langue',
                'required' => false,
            ])
            ->add('languageAbout', TextareaType::class, [
                'label' => 'Présentation de la langue',
                'required' => false,
            ])
            ->add('bannerFile', VichImageType::class, [
                'label' => 'Bannière',
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxWidth' => 1400,
                        'minHeight' => 200,
                        'maxWidthMessage' => "La largeur de l'image ne doit pas dépasser 1400 pixels.",
                        'minHeightMessage' => "L'image doit fait au minimum 200 pixels de hauteur.",
                    ])
                ],
                'help' => "La bannière doit faire au maximum 1400 pixels de large et 200 pixels de hauteur.",
            ])
            ->add('faviconFile', VichImageType::class, [
                'label' => 'Logo du site',
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxWidth' => 513,
                        'maxHeight' => 513,
                        'maxWidthMessage' => "La largeur de l'image ne doit pas dépasser 513 pixels.",
                        'maxHeightMessage' => "La hauteur de l'image ne doit pas dépasser 513 pixels."
                    ]),
                ],
                'help' => "Le logo doit faire au maximum 513 pixels de large et 513 pixels de hauteur.",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
