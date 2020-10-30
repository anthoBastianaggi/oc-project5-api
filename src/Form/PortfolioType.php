<?php

namespace App\Form;

use App\Entity\CategoryPortfolio;
use App\Entity\Portfolio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; 
class PortfolioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('image', TextType::class)
            ->add('link', TextType::class)
            ->add('category', EntityType::class, array(
                'class' => CategoryPortfolio::class,
                'choice_value' => function (?CategoryPortfolio $entity) {
                    return $entity ? $entity->getId() : '';
                },
                'choice_label'  => 'name'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Portfolio::class,
            'csrf_protection' => false
        ]);
    }
}
