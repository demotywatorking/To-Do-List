<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Priority
 *
 * @ORM\Table(name="priority")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PriorityRepository")
 */
class Priority
{
    /**
     * @var int
     *
     * @ORM\Column(name="priority_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $priorityId;


    /**
     * @var string
     *
     * @ORM\Column(name="priority", type="string", length=255, unique=true)
     */
    private $priority;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Todo", mappedBy="priorityDatabase")
     */
    protected $todos;

    public function __construct()
    {
        $this->todos = new ArrayCollection();
    }

    public function setPriorityId($id) {
        $this->priorityId = $id;
    }
    /**
     * Set priority
     *
     * @param string $priority
     *
     * @return Priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Get priorityId
     *
     * @return integer
     */
    public function getPriorityId()
    {
        return $this->priorityId;
    }

    /**
     * Add todo
     *
     * @param \AppBundle\Entity\Todo $todo
     *
     * @return Priority
     */
    public function addTodo(\AppBundle\Entity\Todo $todo)
    {
        $this->todos[] = $todo;

        return $this;
    }

    /**
     * Remove todo
     *
     * @param \AppBundle\Entity\Todo $todo
     */
    public function removeTodo(\AppBundle\Entity\Todo $todo)
    {
        $this->todos->removeElement($todo);
    }

    /**
     * Get todos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTodos()
    {
        return $this->todos;
    }
}
