<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TodoType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * TodoType constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', Type\TextType::class)
            ->add('content', Type\TextareaType::class)
            ->add('priority', Type\ChoiceType::class, [ 'choices' => $this->addChoicesInUserLocale($options['locale']) ])
            ->add('dueDate', Type\DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'html5' => false,
            ]);
    }

    /**
     * Configure defaults options
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults( [
            'locale' => 'en',
        ] );
    }
    /**
     * Method adds array with choices to ChoiceType in builder
     *
     * @param string $locale User's locale
     *
     * @return array All priority in user _locale formatted as array e.g. ['1' => 'low', ...]
     */
    private function addChoicesInUserLocale(string $locale): array
    {
        return $this->em->getRepository('AppBundle:Priority')
                ->getPrioritysInUserLocaleToForm($locale);
    }

}
?>