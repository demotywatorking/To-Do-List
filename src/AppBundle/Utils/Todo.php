<?php

namespace AppBundle\Utils;


use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Todo as TodoEntity;

class Todo
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Return all user's created tasks.
     *
     * @param int $userId User's Id
     * @param string $locale User's locale
     * @return mixed return all user's tasks | null if no tasks found
     */
    public function getAllTodos(int $userId, string $locale)
    {
        return $this->em->getRepository('AppBundle:Todo')
                    ->findAllByUserIdWithLocalePriority($userId, $locale);
    }

    /**
     * Add todo task to database
     *
     * @param TodoEntity $task task to insert into database
     */
    public function addTodo(TodoEntity $task)
    {
        $priority = $this->em->getRepository('AppBundle:Priority')
            ->findOneByPriorityId($task->getPriority());
        $task->setPriorityDatabase($priority);

        $this->em->getRepository('AppBundle:Todo');
        $this->em->persist($task);
        $this->em->flush();
    }

    /**
     * Get details about todo task
     *
     * @param int $id Task's Id
     * @param int $userId User's Id
     * @return mixed details about task | null if task not found
     */
    public function detailsTodo(int $id, int $userId)
    {
        return $this->em->getRepository('AppBundle:Todo')
            ->findByTodoIdAndUserId($id, $userId);
    }

    /**
     * Edit todo task in database
     *
     * @param TodoEntity $todo Task to edit
     */
    public function editTodo(TodoEntity $todo)
    {
        $this->em->getRepository('AppBundle:Todo');
        $this->em->persist($todo);
        $this->em->flush();
    }

    /**
     * Delete Todo task from database
     *
     * @param TodoEntity $toDelete Task to delete
     */
    public function deleteTodo(TodoEntity $toDelete)
    {
        $this->em->getRepository('AppBundle:Todo');
        $this->em->remove($toDelete);
        $this->em->flush();
    }

    /**
     * Set task as done
     *
     * @param TodoEntity $toDone task to set as done
     */
    public function doneTodo(TodoEntity $toDone)
    {
        $toDone->setDone(1);

        $this->em->getRepository('AppBundle:Todo');
        $this->em->persist($toDone);
        $this->em->flush();
    }
}