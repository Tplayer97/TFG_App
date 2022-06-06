<?php

namespace App\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateMoodleUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', \Symfony\Component\Form\Extension\Core\Type\TextType::class,[
                'label' => ' '
            ])

            ->add('password', \Symfony\Component\Form\Extension\Core\Type\TextType::class,[
                'label' => ' '
            ])
            ->add('roles', ChoiceType::class,[
                'label' => ' ',
                'choices'=>[
                    'Admin' => 'ROLE_ADMIN',
                    'Gestor de Contenido' =>' ROLE_GESTOR'
                ]
            ])
            ->add('post', SubmitType::class, [
                'label'     => 'Crear Usuario',
                'attr'      => ['class' => 'btn btn-primary', 'style' => ''],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
