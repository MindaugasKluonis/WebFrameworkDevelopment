<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RecipeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('title');

        $this->var = $options['user_id'];
        $this->tagApproved = 'Approved';

        $builder->add('summary', TextareaType::class, array(
            'attr' => array('class' => ''),
        ));

        $builder->add('ingredients', TextareaType::class, array(
            'attr' => array('class' => ''),
        ));


        $builder->add('steps', TextareaType::class, array(
            'attr' => array('class' => ''),
        ));

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

            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->orderBy('u.name', 'ASC')
                    ->where('u.status = :tagApproved')
                    ->setParameter("tagApproved", $this->tagApproved);
            },

            'class' => 'AppBundle:Tag',
            'choice_label' => 'name',
            'expanded' => true,
            'multiple' => true,
        ]);

        if($options['public'] != 'PUBLIC') {
            $builder->add('public', ChoiceType::class, array(
                'choices' => array(
                    'Public' => 'PUBLIC',
                    'Private' => 'PRIVATE'
                ),
                'required' => true,
                'empty_data' => null
            ));
        }

    }


    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Recipe',
            'user_id' => null,
            'public' => null
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
