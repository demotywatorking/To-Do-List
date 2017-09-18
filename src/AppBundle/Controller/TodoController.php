<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Todo;
use AppBundle\Form\TodoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        $todoService = $this->get('app.todo');
        $todos = $todoService->getAllTodos($userId, $locale);

        return $this->render('all.html.twig',[
            'todos' => $todos
        ]);
    }

    /**
     * @Route("/todo/add", name="todo_add")
     *
     * @param Request $request A Request instance
     *
     * @return RedirectResponse|Response Redirect when task is succesfully added to database,
     * rendering template when task is not subbmitted or not valid
     */
    public function addAction(Request $request)
    {
        $task = new Todo();
        $task->setUserId($this->getUser()->getId());
        $task->setDone(0);

        $form = $this->createForm(TodoType::class, $task, ['locale' => $request->getLocale()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todoService = $this->get('app.todo');
            $todoService->addTodo($task);

            $this->addFlash('success', 'add.success');
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

        $todoService = $this->get('app.todo');
        $todo = $todoService->detailsTodo($id, $userId);

        if (!$todo) {
            throw $this->createNotFoundException('Task not Found');
        }

        $priority = $todo->getPriorityDatabase()->{'getPriority'.$locale}();

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

        $todoService = $this->get('app.todo');
        $todo = $todoService->detailsTodo($id, $userId);

        if (!$todo) {
            $this->addFlash('success','all.notfound');
            return $this->redirectToRoute('todo_all');
        }

        $form = $this->createForm(TodoType::class, $todo, ['locale' => $request->getLocale()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $todoService->editTodo($todo);

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

        $todoService = $this->get('app.todo');
        $toDelete = $todoService->detailsTodo($id, $userId);

        if ($toDelete) {
            $todoService->deleteTodo($toDelete);

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

        $todoService = $this->get('app.todo');
        $toDone = $todoService->detailsTodo($id, $userId);

        if ($toDone) {
            $todoService->doneTodo($toDone);

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