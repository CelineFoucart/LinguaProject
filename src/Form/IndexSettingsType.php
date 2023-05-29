<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class IndexSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('languageDescription', TextareaType::class, [
                'label' => "Description rapide de la langue à afficher sur la page d'accueil",
                'required' => false,
            ])
            ->add('faviconFile', VichImageType::class, [
                'label' => 'Image à afficher sur l\'index',
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxWidth' => 513,
                        'maxHeight' => 513,
                        'maxWidthMessage' => "La largeur de l'image ne doit pas dépasser 513 pixels.",
                        'maxHeightMessage' => "La hauteur de l'image ne doit pas dépasser 513 pixels."
                    ]),
                ],
                'help' => "Image illustrant la langue, comme un drapeau, qui figurera sur l'index. Elle doit faire au maximum 513 pixels de large et 513 pixels de hauteur.",
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
