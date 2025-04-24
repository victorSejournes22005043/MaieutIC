<?php

namespace App\Form;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('birthYear', TextType::class, [
                'label' => 'Année de naissance',
                'required' => true,
            ])
            ->add('deathYear', TextType::class, [
                'label' => 'Année de décès',
                'required' => false,
            ])
            ->add('nationality', TextType::class, [
                'label' => "Nationalité (2 caractères minuscules d'après la norme ISO 3166-1 alpha-2) : https://documentation.abes.fr/sudoc/formats/CodesPays.htm",
                'required' => true,
            ])
            ->add('link', TextType::class, [
                'label' => "Lien d'une biographie de l'auteur",
                'required' => true,
            ])
            ->add('image', TextType::class, [
                'label' => "Lien d'une image de l'auteur",
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
