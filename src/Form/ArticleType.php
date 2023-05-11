<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre*'
            ])
            ->add('presentation', TextareaType::class, [
                'label' => 'Courte présentation*',
                'help' => 'Ecrivez une courte présentation de moins de 1000 caractères'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Corps de l\'article*',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')->orderBy('c.position', 'ASC');
                },
                'choice_label' => function (?Category $category) {
                    if (!$category) {
                        return '';
                    }

                    $title = $category->getTitle();

                    if ($category->getParent() === null) {
                        return $title;
                    }
                    
                    return '-- ' . $title;
                },
                'label' => 'Catégorie*',
                'attr' => [
                    'data-choices' => 'choices',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
