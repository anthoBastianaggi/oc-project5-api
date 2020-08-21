<?php

namespace App\Form;

use App\Entity\CategorySkills;
use App\Entity\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; 
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SkillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('percentage', TextType::class)
            ->add('category', EntityType::class, array(
                'class' => CategorySkills::class,
                'choice_value' => function (?CategorySkills $entity) {
                    return $entity ? $entity->getId() : '';
                },
                'choice_label'  => 'name'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Skill::class,
            'csrf_protection' => false
        ]);
    }
}
