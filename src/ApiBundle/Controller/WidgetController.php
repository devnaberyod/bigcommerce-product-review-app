<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Guzzle\Http\Client;
use Symfony\Component\HttpFoundation\Response;


class WidgetController extends FOSRestController
{
    private $accessToken = null;
    private $apiKey = null;
    private $storeHash = null;
    private $clientId = null;
    private $apiUrl = null;
    private $headers = null;

    public function getAction(Request $request, $id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
        $userId = $user->getId();

        $widget  = $this->getDoctrine()->getRepository('AppBundle:Widget')->findOneBy(array('id' => $id, 'user' => $userId));

        if (!$widget) return new Response('Widget not found.');
        
        $data = [];

        $widgetData = json_decode($widget->getData(), true);
        $widgetName = $widget->getTheme(); 

        $authCredentials = $user->getAuthCredentials();

        $this->apiKey =  $authCredentials->getApiKey();
        $this->accessToken =  $authCredentials->getAccessToken();
        $scope = $authCredentials->getContext();

        $appConfig = $this->getParameter('app_config');
        $store = explode('/' , $scope);
        $this->storeHash = $store[1];
        $this->clientId = $appConfig['client_id'];
        $this->apiUrl = $appConfig['api_url'] . '/' . $scope . '/v2/';
        $productReviews = [];

        foreach ($widgetData as $each) {
            $review = $this->getProductReviews($each['prod_id'], $each['id']);

            if (!is_array($review)) continue;
          
            $productReviews[] = array(
                'id' => $review['id'], 
                'author' => $review['author'], 
                'title' => $review['title'], 
                'review' => $review['review'], 
                'rating' => $review['rating'], 
                'status' => $review['status'],
                'date_created' => $review['date_created']
            );
        }

        $data['reviews'] = $productReviews;
        $data['widget_name'] = $widget->getName();

        $view = $this->view($data, 200)
          ->setTemplate("ApiBundle:Widget:" . $widgetName . '.html.twig')
          // ->setTemplateVar('reviews')
        ;

        return $this->handleView($view);
      
    }

    public function getDataAction(Request $request, $id)
    {
        $params = $request->request->all();
        
        $data = array("data" => [
                [
                    "DT_RowId" => "1",
                    "id" => "1",
                    "name" => "Axel Kho",
                    "title" => "Review for product",
                    "email" => "axelckho@patuli.com",
                    "message" => "Very Good product perme ko magawsan kada gabee",
                    "rating" =>5,
                    "date" => "2011/04/25",
                    "status" => 1
                ],
                [
                    "DT_RowId" => "2",
                    "id" => "2",
                    "name" => "Jason Bayola",
                    "title" => "Review for product",
                    "email" => "jasonbayola@pisot.com",
                    "message" => "Utgan ko kada adlaw",
                    "rating" =>4,
                    "date" => "2011/04/25",
                    "status" => 0
                ]
            ],
            "recordsTotal"=> 30,
            "recordsFiltered"=> 30,
            "draw" => $request->request->get('draw')
        );

        $view = $this->view($data, 200);

        return $this->handleView($view);
    }

    public function addAction(Request $request)
    {
        $data = $request->request->all();

        $user = $this->container->get('security.token_storage')->getToken()->getUser()->getUsername();
    
        $widget = new \AppBundle\Entity\Widget();

        if (isset($data['id']) && $data['id']) {
            $userId = $user->getId();
            $widget  = $this->getDoctrine()->getRepository('AppBundle:Widget')->findOneBy(array('id' => $data['id'], 'user' => $userId));
        }
        
        $widget->setName($data['name']);
        $widget->setContainerId($data['container_id']);
        $widget->setTheme($data['theme']);
        $widget->setStatus($data['status']);
        $widget->setUser($user);
        $widget->setData(json_encode($data['data_selected']));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($widget);
        $entityManager->flush();

        // Generate Script Todo: Create Widget Class Manager
        $widgetData = array('containerId' => $widget->getContainerId(), 'widgetId' => $widget->getId(), 'apiKey' => $request->query->get('api_key'));

        $rendered = $this->renderView('ApiBundle:Widget:snippnet.js.twig', $widgetData);
        $response = new \Symfony\Component\HttpFoundation\Response($rendered);
        $response->headers->set( 'Content-Type', 'text/javascript' );
        // Generate Script End
        return $response;
    }

    public function editAction(Request $request, $id)
    {

    }

    public function removeAction($id)
    {
        $widget  = $this->getDoctrine()->getRepository('AppBundle:Widget')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($widget);
        $em->flush();

        return $this->redirect($this->generateUrl('app_dashboard'));
    }

    public function getSnippnetAction(Request $request, $id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
        $userId = $user->getId();

        $widget  = $this->getDoctrine()->getRepository('AppBundle:Widget')->findOneBy(array('id' => $id, 'user' => $userId));
        // Generate Script Todo: Create Widget Class Manager
        $widgetData = array('containerId' => $widget->getContainerId(), 'widgetId' => $widget->getId(), 'apiKey' => $request->query->get('api_key'));

        $rendered = $this->renderView('ApiBundle:Widget:snippnet.js.twig', $widgetData);
        $response = new \Symfony\Component\HttpFoundation\Response($rendered);
        $response->headers->set( 'Content-Type', 'text/javascript' );
        // Generate Script End
        return $response;

        
        return $response;
    }

    public function productReviewsAction(Request $request, $id)
    {
        $data['id'] = $id;
        $view = $this->view($data, 200);

        return $this->handleView($view);
    }

    //TODO: Transfer this WidgetClient Manager class
    private function getProductReviews($prodId, $prodReviewId) 
    {
        $client = new Client($this->apiUrl);

        $headers = array(
            'Accept' => 'application/json',
            'X-Auth-Client' => $this->clientId,
            'X-Auth-Token' => $this->accessToken,
            'Content-Type' => 'application/json'
        );

        $req = $client->get('products/' . $prodId . '/reviews/' . $prodReviewId, $headers, array('exceptions' => FALSE));
        $resp = $req->send();
     
        if ($resp->getStatusCode() == 200) {
            return $resp->json();
        } else {
            return $resp->getMessage();
        }
    }

    // public function formAction(Request $request)
    // {
    //   $widgetId = $request->query->get('widget_id'); // get data, in this case list of users.

    //   $widget = $this->getDoctrine()->getRepository('AppBundle:Widget')->find($widgetId);
    //   //Find widget data
    //   $formWidget = $name . '.html.twig';

    // 	$view = $this->view($widget->getData(), 200)
    // 	    ->setTemplate("ApiBundle:Widget:" . $formWidget)
    // 	    ->setTemplateVar('widgetData')
    // 	;

    // 	return $this->handleView($view);
    // }
}

/*
	Examples
	
	public function getUsersAction()
   	{
       $data = ...; // get data, in this case list of users.
       $view = $this->view($data, 200)
           ->setTemplate("MyBundle:Users:getUsers.html.twig")
           ->setTemplateVar('users')
       ;

       return $this->handleView($view);
   }

*/