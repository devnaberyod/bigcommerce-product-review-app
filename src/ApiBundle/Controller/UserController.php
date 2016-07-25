<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Bigcommerce\Api\Client as Bigcommerce;

class UserController extends FOSRestController
{
    public function allAction()
    {
        $data = array('users'=> 1);

        // $data['ports'] = $this->getDoctrine()->getRepository('AppBundle:Port')->findByCountryId($countryId);
        
        $view = $this->view($data, 200);
        $view->setFormat('json');

        return $this->handleView($view);
        
    }

    public function getAction($id)
    {
        return $this->render('ApiBundle:User:get.html.twig', array(
            // ...
        ));
    }

    public function editAction($id)
    {
        return $this->render('ApiBundle:User:get.html.twig', array(
            // ...
        ));
    }

    public function deleteAction($id)
    {
        return $this->render('ApiBundle:User:get.html.twig', array(
            // ...
        ));
    }

    // Sample:

    // public function getCountryPortsAction($countryId)
    // {   
    //     $data = array();

    //     $data['ports'] = $this->getDoctrine()->getRepository('AppBundle:Port')->findByCountryId($countryId);
        
    //     $view = $this->view($data, 200);

    //     return $this->handleView($view);
    // }

}
