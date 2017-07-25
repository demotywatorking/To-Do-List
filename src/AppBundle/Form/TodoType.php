<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class TodoType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Session
     */
    private $session;

    /**
     * TodoType constructor.
     *
     * @param EntityManagerInterface $em
     * @param Session $session
     */
    public function __construct(EntityManagerInterface $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
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
            ->add('priority', Type\ChoiceType::class, [ 'choices' => $this->addChoicesInUserLocale() ])
            ->add('dueDate', Type\DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'html5' => false,
            ]);
    }

    /**
     * Method adds array with choices to ChoiceType in builder
     *
     * @return array All priority in user _locale formatted as array e.g. ['1' => 'low', ...]
     */
    private function addChoicesInUserLocale(): array
    {
        $locale = $this->session->get('_locale');
        return $this->em->getRepository('AppBundle:Priority')
                ->getPrioritysInUserLocaleToForm($locale);
    }

}
?>