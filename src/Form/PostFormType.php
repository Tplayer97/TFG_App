<?php

namespace App\Form;

use App\Entity\Post;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Title', TextType::class, [
                'label' => ' ',
                 'attr' => ['style' => 'width: 100%']
            ])
            ->add('Content', CKEditorType::class, [
                'label' => ' ',
                'attr' => ['style' => 'width: 100%; height: 170px']
            ])
           // ->add('Score')
           ->add('Post', SubmitType::class, [
               'label'     => 'Postear',
               'attr'      => ['class' => 'btn btn-primary', 'style' => ''],
           ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
