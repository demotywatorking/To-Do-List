<?php

namespace AppBundle\Repository;

/**
 * TodoRepository
 *
 */
class TodoRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Method to find every task created by user
     *
     * @param int $userId User's Id
     * @param string $locale User's _locale
     *
     * @return array
     */
    public function findAllByUserIdWithLocalePriority(int $userId, string $locale): array
    {
        $tables = $this->findBy([ 'userId' => $userId ], [
            'priority' => "DESC",
            'dueDate' => "DESC"
        ]);
        return $this->addPriorityToTodoTable($tables, $locale);
    }

    /**
     * Method to find one task created by user
     *
     * @param int $id Task's Id
     * @param int $userId User's Id
     *
     * @return null|object Return task if found it in database
     */
    public function findByTodoIdAndUserId(int $id, int $userId)
    {
        return $this->findOneBy([
            'userId' => $userId,
            'id' => $id,
        ]);
    }

    /**
     * Method to set priority name in user's _locale instead of priority id
     *
     * @param array $todos all user's tasks
     * @param string $locale User's _locale
     *
     * @return array Array with all user's tasks with priority's name
     */
    private function addPriorityToTodoTable(array $todos, string $locale): array
    {
        foreach ($todos as $todo) {
            $todo->setPriority($todo->getPriorityDatabase()->{'getPriority'.$locale}());
        }
        return $todos;
    }
}
?>