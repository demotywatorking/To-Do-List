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
                ->add('success', 'add.success')
            ;
            return $this->redirectToRoute('details', [
                'id' => $task->getId()
            ]);
        }

        return $this->render('add.html.twig', [
            'title' => 'add.title.header',
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
        if (!$todo) {
            return $this->render('details.html.twig', [
                'todo' => ''
            ]);
        }
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
            'title' => 'add.title.edit',
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
                ->add('success', 'delete.success')
            ;
        } else {
            $request->getSession()
                ->getFlashBag()
                ->add('warning', 'delete.warning')
            ;
        }
        return $this->render('delete.html.twig');
    }

    /**
     * @Route("/todo/done/{id}", name="done")
     */
    public function doneAction($id, Request $request)
    {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $toDone = $em->getRepository('AppBundle:Todo')
            ->findOneBy([
                'id' => $id,
                'UserId' => $userId
        ]);
        if ($toDone) {
            $toDone->setDone(1);
            $em->persist($toDone);
            $em->flush();
            $request->getSession()
                ->getFlashBag()
                ->add('success', 'done.success')
            ;
        } else {
            $request->getSession()
                ->getFlashBag()
                ->add('warning', 'done.warning')
            ;
        }
        return $this->render('done.html.twig', [
            'id' => $id
        ]);
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
/*
 * ALL WITHOUT TABLE
{% if(todo.done) %}
                            <div class="row bg-success margin-bottom">
                        {% else %}
                            <div class="row bg-warning margin-bottom">
                        {% endif %}
                            <div class="col-xs-2">
                                {{ todo.title }}
                            </div>
                            <div class="col-xs-2">
                                {{  todo.priority }}
                            </div>
                            <div class="col-xs-3">
                                {{ todo.dueDate|date }}
                            </div>
                            <div class="col-xs-5">
                                <div class="btn-group" role="group">
                                    <a href="{{ path('details', {'id' : todo.id}) }}" class="btn btn-primary" type="button">Zobacz</a>
                                    <a href="{{ path('edit', {'id' : todo.id}) }}" class="btn btn-info" type="button">Edytuj</a>
                                    <a href="{{ path('delete', {'id' : todo.id}) }}" class="btn btn-danger" type="button">Usuń</a>
                                    {% if(todo.done == 0) %}
                                        <a href="{{ path('done', {'id' : todo.id}) }}" class="btn btn-success" type="button">Oznacz jako wykonane</a>
                                    {% else %}
                                        <button class="btn btn-default" type="button">Zrobione</button>
                                    {% endif %}
                                </div>
                            </div>
                        </div>
*/