<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Pseudo'
                ]
            ])
            ->add('content', CKEditorType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Votre commentaire ...',
                    'rows' => '5'
                ]
            ])
            ->add('Commenter', SubmitType::class, [
                'label' => 'Commenter'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class
        ]);
    }
}
