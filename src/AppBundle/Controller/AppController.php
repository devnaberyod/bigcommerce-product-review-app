<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppController extends BigcommerceApiController
{
    public function indexAction(Request $request)
    {
        if ($this->user) $this->redirectToRoute('app_dashboard');

		$authenticationUtils = $this->get('security.authentication_utils');

		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();

		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();
	
		return $this->render(
			'AppBundle::index.html.twig',
			array(
					// last username entered by the user
					'last_username' => $lastUsername,
					'error'         => $error
				)
			);
      
    }

    public function dashboardAction(Request $request)
    {
        $this->setConfig();
       
        $isAuthorize = $this->isAuthorize();

        if (!$isAuthorize) return $this->redirectToRoute('app_auth_logout');

        $products = [];

        $widgets  = $this->getDoctrine()->getRepository('AppBundle:Widget')->findBy(array('user' => $this->user->getId()));
        
        // $products = $this->getProducts();
        
        if (!is_array($products)) return new Response($products);

        $productReviews = [];

        if ($products) {
            foreach ($products as $key => $value) {

                $id = $value['id'];

                $reviews = $this->getProductReviews($id);

                if (!is_array($reviews)) continue;

                // var_dump($reviews); exit;

                foreach($reviews as $review) {
                    $productReviews[$review['id']] = array(
                        'id' => $review['id'], 
                        'product_name' => $value['name'], 
                        'author' => $review['author'], 
                        'title' => $review['title'], 
                        'review' => $review['review'], 
                        'rating' => $review['rating'], 
                        'status' => $review['status'],
                        'date_created' => $review['date_created']
                    );
                }
            }
        }
        
        return $this->render('AppBundle::dashboard.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'product_reviews' => $productReviews,
            'products' => $products,
            'api_key' => $this->apiKey,
            'widgets' => $widgets
        ]);
    }

    public function reviewsAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('AppBundle::reviews.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    public function widgetAction(Request $request)
    {
        $this->setConfig();
        
        $isAuthorize = $this->isAuthorize();

        if (!$isAuthorize) return $this->redirectToRoute('app_auth_logout');

        $products = [];
        $widgetId = $request->query->get('id');
        $widget = [];
        $themeSelected = 'widget1'; //TODO: Add to theme class manager

        $products = $this->getProducts();

        $productReviews = [];
        $productReviewSelected = [];

        if (count($products) > 0) {
            foreach ($products as $key => $value) {

                $id = $value['id'];

                $reviews = $this->getProductReviews($id);

                if (!is_array($reviews)) continue;

                if(isset($widgetId)) {
                    $userId = $this->user->getId();

                    $widget  = $this->getDoctrine()->getRepository('AppBundle:Widget')->findOneBy(array('id' => $widgetId, 'user' => $userId));

                    $widgetData = json_decode($widget->getData(), true);

                    $themeSelected = $widget->getTheme();
                    
                    foreach ($widgetData as $each) {
                        $review = $this->getProductReview($each['prod_id'], $each['id']);

                        if (!is_array($review)) continue;
                      
                        $productReviewSelected[$review['id']] = array(
                            'id' => $review['id'],
                        );
                    }
                }

                foreach($reviews as $review) {
                    $productReviews[$review['id']] = array(
                        'id' => $review['id'], 
                        'product_name' => $value['name'], 
                        'product_id' => $review['product_id'], 
                        'author' => $review['author'], 
                        'title' => $review['title'], 
                        'review' => $review['review'], 
                        'rating' => $review['rating'], 
                        'status' => $review['status'],
                        'date_created' => $review['date_created'],
                        'is_selected' => isset($productReviewSelected[$review['id']])
                    );
                }
            }
        }

        return $this->render('AppBundle::widget.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'api_key' => $this->apiKey,
            'product_reviews' => $productReviews,
            'widget' => $widget,
            'theme_selected' => $themeSelected
        ]);
    }


    public function settingAction(Request $request)
    {
        $this->setConfig();

        $isAuthorize = $this->isAuthorize();

        if (!$isAuthorize) return $this->redirectToRoute('app_auth_logout');
        // replace this example code with whatever you need
        return $this->render('AppBundle::setting.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'api_key' => $this->apiKey
        ]);
    }
}
