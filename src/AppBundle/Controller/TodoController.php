<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017-07-14
 * Time: 12:48
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Priority;
use AppBundle\Entity\Todo;
use AppBundle\Form\TodoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;

class TodoController extends Controller
{
    /**
     * @Route("/todo", name="all")
     */
    public function allAction()
    {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $todos = $em->getRepository('AppBundle:Todo')
            ->findBy(['UserId' => $userId], [
                'priority' => "DESC",
                'dueDate' => "DESC"
        ]);
        foreach ($todos as $todo) {
            $todo->setPriority($todo->getPriorityDatabase()->getPriority());
        }
        return $this->render('all.html.twig',[
            'todos' => $todos
        ]);
    }

    /**
     * @Route("/todo/add", name="add")
     */
    public function addAction(Request $request)
    {
        $task = new Todo();
        $task->setUserId($this->getUser()->getId());
        $task->setDone(0);
        $form = $this->createForm(TodoType::class, $task);
        $form->add('priority', ChoiceType::class, [ 'choices' => $this->addChoicesToForm() ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $priority = $em->getRepository('AppBundle:Priority')
                        ->findOneBy(['priorityId' => $task->getPriority()]);
            $task->setPriorityDatabase($priority);
            $em->getRepository('AppBundle:Todo');
            $em->persist($task);
            $em->flush();
            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Z powodzeniem utworzono zadanie.')
            ;
            return $this->redirectToRoute('details', [
                'id' => $task->getId()
            ]);
        }

        return $this->render('add.html.twig', [
            'title' => 'Dodaj zadanie',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/todo/details/{id}", name="details")
     */
    public function detailsAction($id)
    {
        $userId = $this->getUser()->getId();
        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findOneBy([
                'UserId' => $userId,
                'id' => $id,
        ]);
        return $this->render('details.html.twig', [
            'todo' => $todo,
            'priority' => $todo->getPriorityDatabase()->getPriority()
        ]);
    }

    /**
     * @Route("/todo/edit/{id}", name="edit")
     *
     */
    public function editAction($id, Request $request)
    {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('AppBundle:Todo')
            ->findOneBy([
                'UserId' => $userId,
                'id' => $id,
        ]);
        $form = $this->createForm(TodoType::class, $todo);
        $form->add('priority', ChoiceType::class, [ 'choices' => $this->addChoicesToForm() ]);

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->persist($todo);
            $em->flush();
            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Z powodzeniem edytowano zadanie.')
            ;
            return $this->redirectToRoute('details', [
                'id' => $id
            ]);
        }

        return $this->render('add.html.twig', [
            'title' => 'Edytuj zadanie',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/todo/delete/{id}", name="delete")
     *
     */
    public function deleteAction($id, Request $request)
    {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $toDelete = $em->getRepository('AppBundle:Todo')
                    ->findOneBy([
                        'id' => $id,
                        'UserId' => $userId
        ]);
        if ($toDelete) {
            $em->remove($toDelete);
            $em->flush();
            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Z powodzeniem usunięto zadanie.')
            ;
        } else {
            $request->getSession()
                ->getFlashBag()
                ->add('warning', 'Nie znaleziono zadania.')
            ;
        }
        return $this->render('delete.html.twig');
    }

    private function addChoicesToForm()
    {
        $priority = $this->getDoctrine()
            ->getRepository('AppBundle:Priority')
            ->findBy([], [
                'priorityId' => 'ASC'
            ]);
        $return = [];
        foreach ($priority as $value) {
            $return[$value->getPriority()] = $value->getPriorityId();
        }
        return $return;
    }
}