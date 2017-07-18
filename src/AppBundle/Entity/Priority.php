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
     * @ORM\Column(name="priority_pl", type="string", length=255, unique=true)
     */
    private $priorityPl;

    /**
     * @var string
     *
     * @ORM\Column(name="priority_en", type="string", length=255, unique=true)
     */
    private $priorityEn;

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

    /**
     * Set priorityPl
     *
     * @param string $priorityPl
     *
     * @return Priority
     */
    public function setPriorityPl($priorityPl)
    {
        $this->priorityPl = $priorityPl;

        return $this;
    }

    /**
     * Get priorityPl
     *
     * @return string
     */
    public function getPriorityPl()
    {
        return $this->priorityPl;
    }

    /**
     * Set priorityEn
     *
     * @param string $priorityEn
     *
     * @return Priority
     */
    public function setPriorityEn($priorityEn)
    {
        $this->priorityEn = $priorityEn;

        return $this;
    }

    /**
     * Get priorityEn
     *
     * @return string
     */
    public function getPriorityEn()
    {
        return $this->priorityEn;
    }
}
