<?php

namespace App\Form;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Tag;

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
            ->add('imageFile', FileType::class, [
                'label' => 'Photo de l\'auteur',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci d\'uploader une image valide (JPEG, PNG, WEBP)',
                    ])
                ],
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'mapped' => false,
                'label' => 'Mots-clés (tags)',
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
