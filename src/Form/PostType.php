<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{FileType, SubmitType, TextareaType, TextType};
use Symfony\Component\Validator\Constraints\File;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Enter title',
                'attr' => [
                    'placeholder' => 'Enter the title here'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Enter Content',
                'attr' => [
                    'placeholder' => 'Enter the content from here'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG/PNG)',
                    ])
                ]
            ]);

        // ->add('submit', SubmitType::class, [
        //     'attr' => [
        //         'class' => 'btn btn-primary'
        //     ]
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
