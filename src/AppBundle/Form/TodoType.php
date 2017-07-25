<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;

class TodoType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $request;

    /**
     * TodoType constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, RequestStack $request)
    {
        $this->em = $em;
        $this->request = $request->getCurrentRequest();
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
        return $this->em->getRepository('AppBundle:Priority')
                ->getPrioritysInUserLocaleToForm($this->request->getLocale());
    }

}
?>