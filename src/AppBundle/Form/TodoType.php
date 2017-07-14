<?php
/**
 * Created by PhpStorm.
 * User: demot
 * Date: 09.07.2017
 * Time: 16:17
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;

class TodoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', Type\TextType::class)
            ->add('content', Type\TextareaType::class)
            //->add('priority', Type\ChoiceType::class, $this->priority )
            ->add('category')
            ->add('dueDate', Type\DateTimeType::class)
            ->add('save', Type\SubmitType::class)
        ;
    }

}