<?php

namespace Es\CoreBundle\Form\Type\Security;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends ResettingType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('currentPassword', PasswordType::class, [
            'label' => 'form_current_password_label',
            'translation_domain' => 'EsCoreBundle',
            'mapped' => false,
            'constraints' => [
                new NotBlank(),
                new UserPassword(['message' => 'form_current_passwordinvalid']),
            ]
        ]);
        parent::buildForm($builder, $options);
    }
}
