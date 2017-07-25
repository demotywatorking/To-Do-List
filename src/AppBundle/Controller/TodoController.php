<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Todo;
use AppBundle\Form\TodoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TodoController extends Controller
{
    /**
     * @Route("/todo", name="todo_all")
     *
     * Method shows user's all created to-do tasks
     *
     * @param Request $request A Request instance
     *
     * @return Response A Response instance
     */
    public function allAction(Request $request): Response
    {
        $locale = $request->getLocale();
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

        $todos = $em->getRepository('AppBundle:Todo')
                    ->findAllByUserIdWithLocalePriority($userId, $locale);

        return $this->render('all.html.twig',[
            'todos' => $todos
        ]);
    }

    /**
     * @Route("/todo/add", name="todo_add")
     */
    public function addAction(Request $request)
    {
        $locale = $request->getLocale();
        $task = new Todo();
        $task->setUserId($this->getUser()->getId());
        $task->setDone(0);
        $form = $this->createForm(TodoType::class, $task);
        $form->add('priority', ChoiceType::class, [ 'choices' => $this->addChoicesToForm($locale) ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $priority = $em->getRepository('AppBundle:Priority')
                        ->findOneBy(['priorityId' => $task->getPriority()]);
            $task->setPriorityDatabase($priority);
            $em->getRepository('AppBundle:Todo');
            $em->persist($task);
            $em->flush();
            $this->addFlash('success', 'add.success')
            ;
            return $this->redirectToRoute('todo_details', [
                'id' => $task->getId()
            ]);
        }

        return $this->render('add.html.twig', [
            'title' => 'add.title.header',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/todo/details/{id}", name="todo_details")
     *
     * Method shows details about task
     *
     * @param int $id Task's Id
     * @param Request $request A Request instance
     *
     * @throws NotFoundHttpException When task is not found in database
     *
     * @return Response A Response instance
     */
    public function detailsAction(int $id, Request $request): Response
    {
        $locale = $request->getLocale();
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

        $todo = $em->getRepository('AppBundle:Todo')
                ->findByTodoIdAndUserId($id, $userId);
        $priority = $todo->getPriorityDatabase()->{'getPriority'.$locale}();

        if (!$todo) {
            throw $this->createNotFoundException('Task not Found');
        }

        return $this->render('details.html.twig', [
            'todo' => $todo,
            'priority' => $priority
        ]);
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     *
     * Method to edit task if task exists and was created by current user
     *
     * @param int $id Task's Id
     * @param Request $request A Request instance
     *
     * @return mixed Response instance or RedirectResponse instance
     */
    public function editAction(int $id, Request $request)
    {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

        $todo = $em->getRepository('AppBundle:Todo')
                ->findByTodoIdAndUserId($id, $userId);
        if (!$todo) {
            $this->addFlash('success','all.notfound');
            return $this->redirectToRoute('todo_all', [
            ]);
        }

        $form = $this->createForm(TodoType::class, $todo);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($todo);
            $em->flush();
            $this->addFlash('success', 'edit.success');
            return $this->redirectToRoute('todo_details', [
                'id' => $id
            ]);
        }

        return $this->render('add.html.twig', [
            'title' => 'add.title.edit',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     *
     * Method delete task from database if task exists and was created by current user
     *
     * @param int $id Task's Id
     *
     * @return Response A Response instance
     */
    public function deleteAction(int $id): Response
    {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

        $toDelete = $em->getRepository('AppBundle:Todo')
                   ->findByTodoIdAndUserId($id, $userId);

        if ($toDelete) {
            $em->remove($toDelete);
            $em->flush();
            $this->addFlash('success', 'delete.success');
        } else {
            $this->addFlash('warning', 'delete.warning');
        }

        return $this->render('delete.html.twig');
    }

    /**
     * @Route("/todo/done/{id}", name="todo_done")
     *
     * Method sets task as done if task exists and was created by current user
     *
     * @param int $id Task's Id
     *
     * @return Response A Response instance
     */
    public function doneAction(int $id): Response
    {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

        $toDone = $em->getRepository('AppBundle:Todo')
            ->findByTodoIdAndUserId($id, $userId);

        if ($toDone) {
            $toDone->setDone(1);
            $em->persist($toDone);
            $em->flush();
            $this->addFlash('success', 'done.success');
        } else {
            $this->addFlash('warning', 'done.warning');
        }

        return $this->render('done.html.twig', [
            'id' => $id
        ]);
    }

}
?>