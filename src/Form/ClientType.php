<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['maxlength' => 100]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => ['maxlength' => 100]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['maxlength' => 180]
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['maxlength' => 20]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => ['maxlength' => 255]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-warning mt-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
