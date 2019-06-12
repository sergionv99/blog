<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', null, [
                'label' => 'Titulo',
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('content', null, [
                'attr' => ['rows' => 25],
                'label' => 'Contenido',
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('tags', null, [
                'label' => 'Tags',
                'required' => false,
                'attr'=>[
                    'data-role'=>'tagsinput',
                    'class'=>'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
