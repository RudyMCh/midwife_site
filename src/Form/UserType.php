<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function __construct(private readonly ParameterBagInterface $params, private readonly Security $security)
    {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $roles = [];
        /** @var array<string, array<string>> $hierarchy */
        $hierarchy = $this->params->get('security.role_hierarchy.roles');
        foreach ($hierarchy as $key => $value) {
            if ($this->security->isGranted($key)) {
                $roles[str_replace('ROLE_', '', $key)] = $key;
            }
        }
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('email')
//            ;
//        if ($options["data"]->getId() === null || $options["data"] === $this->security->getUser()){
//            $builder
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les champs mot de passe doivent être identiques.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => null === $options['data']->getId(),
                    'first_options' => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmation mot de passe'],
                ])
//                ;
//        }
//        $builder
            ->add('roles', ChoiceType::class, [
                'choices' => $roles,
                'attr' => [
                    'class' => 'select2',
                ],
                'multiple' => true,
                'required' => false,
                'label' => 'Rôles',
                'help' => 'Indiquez les roles attribués',
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
