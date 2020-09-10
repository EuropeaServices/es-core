<?php

namespace Es\CoreBundle\Form\Type\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Es\CoreBundle\Entity\Security\User;

class ResettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'options' => array(
                'attr' => array(
                    'autocomplete' => 'nouveau mot depasse',
                ),
            ),
            'first_options' => array('label' => 'nouveau mdp'),
            'second_options' => array('label' => 'confirmation'),
            'invalid_message' => 'erreur',
        ));
    }

        /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'csrf_token_id' => 'resetting',
        ));
    }
}