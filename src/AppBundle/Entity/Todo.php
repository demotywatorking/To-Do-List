<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Todo
 *
 * @ORM\Table(name="todo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TodoRepository")
 */
class Todo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank(
     *     message = "Nie można stworzyć zadania bez tytułu"
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="userid", type="integer")
     * @ORM\OneToOne(targetEntity="AppBundle:User")
     * @ORM\JoinColumn(name="userid", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255)
     * @Assert\NotBlank(
     *     message = "Nie można stworzyć zadania bez szczegółów"
     * )
     *
     */
    private $content;

    /**
     * @var string
     * @ORM\Column(name="priority", type="integer")
     * @Assert\Range(
     *     min = 1,
     *     max = 4
     * )
     * @Assert\NotBlank(
     *     message = "Musi być ustawiony priorytet zadania"
     * )
     *
     */
    private $priority;

    /**
     * @return mixed
     */
    public function getPriorityDatabase()
    {
        return $this->priorityDatabase;
    }

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Priority", inversedBy="todos")
     * @ORM\JoinColumn(name="priority", referencedColumnName="priority_id")
     */
    private $priorityDatabase;
    /**
     * @var string
     *
     * @ORM\Column(name="dueDate", type="datetime")
     * @Assert\DateTime(
     *     message = "Nieprawddłowy format daty lub godziny"
     * )
     */
    private $dueDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="done", type="boolean")
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\LessThanOrEqual(1)
     */
    private $done;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Todo
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Todo
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set priority
     *
     * @param string $priority
     *
     * @return Todo
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
     * Set userId
     *
     * @param integer $userId
     *
     * @return Todo
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate
     *
     * @return Todo
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set done
     *
     * @param boolean $done
     *
     * @return Todo
     */
    public function setDone($done)
    {
        $this->done = $done;

        return $this;
    }

    /**
     * Get done
     *
     * @return boolean
     */
    public function getDone()
    {
        return $this->done;
    }

    /**
     * Set priorityDatabase
     *
     * @param \AppBundle\Entity\Priority $priorityDatabase
     *
     * @return Todo
     */
    public function setPriorityDatabase(\AppBundle\Entity\Priority $priorityDatabase = null)
    {
        $this->priorityDatabase = $priorityDatabase;

        return $this;
    }
}
