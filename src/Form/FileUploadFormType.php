<?php

namespace App\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileUploadFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Title', \Symfony\Component\Form\Extension\Core\Type\TextType::class,[
                'label' => " ",
                'attr' => ['style' => 'width: 50%' ]
            ])
            ->add('File', FileType::class,[
                'label' => " ",

            ])
            ->add('Guardar', SubmitType::class,[
                'label' => 'Guardar',
                'attr'  => ['class'=>'primary save', 'style' => 'margin-top: 30px']
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
