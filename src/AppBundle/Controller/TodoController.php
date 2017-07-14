<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017-07-14
 * Time: 12:48
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Todo;
use AppBundle\Form\TodoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TodoController extends Controller
{
    /**
     * @Route("/todo/all", name="show_all")
     */
    public function allAction()
    {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findBy(['UserId' => $userId]);

        return $this->render('all.html.twig',[
            'todos' => $em
        ]);
    }

    /**
     * @Route("/todo/add", name="add")
     */
    public function addAction(Request $request)
    {
        $task = new Todo();
        $task->setUserId($this->getUser()->getId());
        $form = $this->createForm(TodoType::class, $task);
        $form->add('priority', ChoiceType::class, [ 'choices' => $this->addChoicesToForm() ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
            return $this->redirectToRoute('details', ['id' => $task->getId()]);
        }

        return $this->render('add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/todo/details/{id}", name="details")
     */
    public function detailsAction($id)
    {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()
            -> getRepository('AppBundle:Todo')
            ->findOneBy([
                'UserId' => $userId,
                'id' => $id,
            ]);
        echo '<pre>';
        print_r($em);
        echo '</pre>';
        return new Response('aaa');
    }

    /**
     * @Route("/admin/abcd/", name="admin")
     */
    public function adminAction()
    {
        return new Response('admin page');
    }

    private function addChoicesToForm()
    {
        $priority = $this->getDoctrine()
            ->getRepository('AppBundle:Priority')
            ->findBy([], [
                'id' => 'ASC'
            ]);
        $return = [];
        foreach ($priority as $value) {
            $return[$value->getPriority()] = $value->getId();
        }
        return $return;
    }
}