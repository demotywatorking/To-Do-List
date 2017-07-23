<?php

namespace AppBundle\Repository;

/**
 * TodoRepository
 *
 */
class TodoRepository extends \Doctrine\ORM\EntityRepository
{

    public function findAllByUserIdWithLocalePriority(int $userId, string $locale): array
    {
        $tables = $this->findBy([ 'userId' => $userId ], [
            'priority' => "DESC",
            'dueDate' => "DESC"
        ]);
        return $this->addPriorityToTodoTable($tables, $locale);
    }

    private function addPriorityToTodoTable(array $todos, string $locale): array
    {
        foreach ($todos as $todo) {
            $todo->setPriority($todo->getPriorityDatabase()->{'getPriority'.$locale}());
        }
        return $todos;
    }

    public function findByTodoIdAndUserId(int $userId, int $todoId)
    {
        
    }
}
?>