<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Product name',
                'required' => true,
                'attr' => [
                    'class' => 'form-control mb-2',
                    'placeholder' => 'Enter the product name'
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control mb-2',
                    'row' => 5
                ]
            ])
            ->add('price', NumberType::class, [
                'attr' => [
                    'class' => 'form-control mb-2'
                ],
                'scale' => 2, //allow 2 decimal points
                'html5' => true
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id', // entity field name
                'label' => 'User ID',
                'placeholder' => 'Select user Id',
                'attr' => [
                    'class' => 'form-control mb-2'
                ]
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => $options['is_edit'] ? false : true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => $options['is_edit'] ? 'Update' : 'Create',
                'attr' => [
                    'class' => 'btn btn-success mt-2'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'is_edit' => false, // default to 'false' for create form
        ]);
    }
}
