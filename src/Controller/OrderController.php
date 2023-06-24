<?php

namespace App\Controller;

use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use App\Entity\Order;

class OrderController extends AbstractController
{
    /**
    * @Route("/order",name="order_index")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $orders = $em->getRepository(Order::class)->findAll();

        return $this->render('order/index.html.twig', array(
            'orders' => $orders,
        ));
    }

    /**
    * @Route("/order/create", name="order_create", methods={"GET","POST"})
    */
    public function createAction(Request $request)
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();
            return $this->redirectToRoute('order_show', array('id' => $order->getId()));
        }
        return $this->render('order/create.html.twig', array(
            'order' => $order,
            'form' => $form->createView(),
        ));
    }

    /**
    * @Route("/order/edit/{id}", name="order_edit", methods={"GET","POST"})
    */
    public function editAction(Request $request, $id)
   {
       $em = $this->getDoctrine()->getManager();
       $order = $em->getRepository(Order::class)->find($id);
       
       $editForm = $this->createForm(OrderType::class, $order);
       
       $editForm->handleRequest($request);
        if($editForm->isSubmitted() && $editForm->isValid()){
            $em = $this->getDoctrine()->getManager()->flush();
           return $this->redirectToRoute('order_show', array('id' => $id));
       }
       
       return $this->render('order/edit.html.twig', [
           'id' => $id,
           'edit_form' => $editForm->createView()
       ]);
    }

    /**
     * @Route("/order/delete/{id}", name="order_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->find($id);
        $em->remove($order);
        $em->flush();
        
        $this->addFlash(
            'error',
            'Order deleted'
        );
        
        return $this->redirectToRoute('order_index');
    }

    /**
     * @Route("/order/{id}", name="order_show")
     */
    public
    function showAction($id)
    {
        $order = $this->getDoctrine()
            ->getRepository(Order::class)
            ->find($id);

        return $this->render('order/show.html.twig', [
            'order' => $order
        ]);
    }
}
