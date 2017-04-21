<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RecipeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder->add('title')->add('summary')->add('ingredients')->add('steps')->add('collection')->add('author')->add('tags');
        $builder->add('title')->add('summary')->add('ingredients')->add('steps')->add('tags');

        $this->var = $options['user_id'];

        $builder->add('collection', EntityType::class, [

            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->orderBy('u.title', 'ASC')
                    ->where('u.author = :var')
                    ->setParameter("var", $this->var);
            },

            'class' => 'AppBundle:Collection',
            'choice_label' => 'title',
        ]);

        $builder->add('tags', EntityType::class, [



            'class' => 'AppBundle:Tag',
            'choice_label' => 'name',
            'expanded' => true,
            'multiple' => true,
        ]);

    }


    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Recipe',
            'user_id' => null,
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
