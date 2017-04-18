<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RecipeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')->add('summary')->add('ingredients')->add('steps')->add('author');

        $builder->add('collection', EntityType::class, [
            'class' => 'AppBundle:Collection',
            'choice_label' => 'title',
        ]);

        $builder->add('tags', EntityType::class, [
            'class' => 'AppBundle:Tag',
            'multiple' => true,
            'expanded' => true,
            'choice_label' => 'name',
        ]);


    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Recipe'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_recipe';
    }


}
