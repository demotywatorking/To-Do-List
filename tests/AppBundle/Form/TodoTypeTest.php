<?php
namespace Tests\AppBundle\Form\Type;

use AppBundle\Entity\Todo;
use AppBundle\Form\TodoType;
use AppBundle\Repository\PriorityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class TodoTypeTest extends TypeTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @return array
     */
    protected function getExtensions()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);

        return array(
            new PreloadedExtension([
                new TodoType($this->em)
            ], [])
        );
    }

    /**
     * Test if todo form is valid
     */
    public function testValidTodoTypeForm()
    {
        $this->createRepositoryToForm();
        $todo = new Todo();

        $form = $this->factory->create(TodoType::class, $todo, ['locale' => 'en']);

        $todoValid = $this->createValidTodo($todo);
        $form->submit($todoValid);

        $this->assertTrue($form->isSubmitted() &&  $form->isValid());
    }

    /**
     * create valid todo which will be used to submit form
     *
     * @param Todo $todo
     * @return Todo
     */
    private function createValidTodo(Todo $todo): Todo
    {
        $todo->setTitle('any title');
        $todo->setPriority(1);
        $todo->setUserId(111);
        $todo->setDueDate(new \DateTime('2015-08-21 15:00:00 EDT'));
        $todo->setContent('any content');
        $todo->setDone(0);
        return $todo;
    }

    /**
     * create repository with prioritys
     */
    private function createRepositoryToForm()
    {
        $repoMock = $this->createMock(PriorityRepository::class);
        $repoMock->expects($this->any())
            ->method('getPrioritysInUserLocaleToForm')
            ->with('en')
            ->willReturn([
                1 => 'low',
                2 => 'medium',
                3 => 'high',
                4 => 'very high'
            ]);

        $this->em->expects($this->any())
            ->method('getRepository')
            ->with('AppBundle:Priority')
            ->willReturn($repoMock);
    }
}