<?php

namespace App\Controller;

use App\Entity\Customers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use App\Form\CustomersType;

class CustomersController extends AbstractController
{
    /**
    * @Route("/customer",name="customer_index")
    */
    public function indexAction() 
    {
        $em = $this->getDoctrine()->getManager();

        $customers = $em->getRepository(Customers::class)->findAll();

        return $this->render('customer/index.html.twig', array(
            'customers' => $customers,
        ));
    }
    
    /**
    * @Route("/customer/create", name="customer_create", methods={"GET","POST"})
    */
    public function createAction(Request $request)
    {
        $customer = new Customers();
        $form = $this->createForm(CustomersType::class, $customer);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();
            return $this->redirectToRoute('customer_show', array('id' => $customer->getId()));
        }
        return $this->render('customer/create.html.twig', array(
            'customer' => $customer,
            'form' => $form->createView(),
        ));
    }

    /**
    * @Route("/customer/edit/{id}", name="customer_edit", methods={"GET","POST"})
    */
    public function editAction(Request $request, $id)
   {
       $em = $this->getDoctrine()->getManager();
       $product = $em->getRepository(Customers::class)->find($id);
       
       $editForm = $this->createForm(CustomersType::class, $product);
       
       $editForm->handleRequest($request);
        if($editForm->isSubmitted() && $editForm->isValid()){
            $em = $this->getDoctrine()->getManager()->flush();
           return $this->redirectToRoute('customer_show', array('id' => $id));
       }
       
       return $this->render('customer/edit.html.twig', [
           'id' => $id,
           'edit_form' => $editForm->createView()
       ]);
    }

    /**
     * @Route("/customer/delete/{id}", name="customer_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $customer = $em->getRepository(Customers::class)->find($id);
        $em->remove($customer);
        $em->flush();
        
        $this->addFlash(
            'error',
            'Customer deleted'
        );
        
        return $this->redirectToRoute('customer_index');
    }

    /**
     * @Route("/customer/{id}", name="customer_show")
     */
    public
    function showAction($id)
    {
        $customer = $this->getDoctrine()
            ->getRepository(Customers::class)
            ->find($id);

        return $this->render('customer/show.html.twig', [
            'customer' => $customer
        ]);
    }
}
