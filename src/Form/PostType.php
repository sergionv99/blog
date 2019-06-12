<?php
namespace App\Form;
use App\Entity\Post;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class PostType extends AbstractType
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
            ->add('Ispublished', null, [
                'label' => 'Publicar',
                'attr'=>[
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