<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Guzzle\Http\Client;

abstract class BigcommerceApiController extends Controller
{
    protected $accessToken = null;
    protected $apiKey = null;
    protected $storeHash = null;
    protected $clientId = null;
    protected $apiUrl = null;
    protected $client = null;
    private $headers = null;
    protected $user = null;

    public function setConfig()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $this->user = $user;
        
        $authCredentials = $user->getAuthCredentials();

        $this->apiKey =  $authCredentials->getApiKey();
        $this->accessToken =  $authCredentials->getAccessToken();
        $scope = $authCredentials->getContext();
        
        $appConfig = $this->getParameter('app_config');
        $store = explode('/' , $scope);
        $this->storeHash = $store[1];
        $this->clientId = $appConfig['client_id'];
        $this->apiUrl = $appConfig['api_url'] . '/' . $scope . '/v2/';

        $this->client = new Client($this->apiUrl);

        $this->headers = array(
            'Accept' => 'application/json',
            'X-Auth-Client' => $this->clientId,
            'X-Auth-Token' => $this->accessToken,
            'Content-Type' => 'application/json'
        );
    }

    protected function isAuthorize() {
        $req = $this->client->get('time', $this->headers, array('exceptions' => FALSE));
        $resp = $req->send();
        
        return ($resp->getStatusCode() == 200);
    }

    protected function getProducts()
    {  
        $req = $this->client->get('products', $this->headers, array('exceptions' => FALSE));
        $resp = $req->send();
    
        if ($resp->getStatusCode() == 200) {
            return $resp->json();
        } else {
            return $resp->getMessage();
        }
    }

    protected function getProductReviews($prodId)
    {
        $req = $this->client->get('products/' . $prodId . '/reviews', $this->headers, array('exceptions' => FALSE));
        $resp = $req->send();
        
        if ($resp->getStatusCode() == 200) {
            return $resp->json();
        } else {
            return $resp->getMessage();
        }
    }

    protected function getProductReview($prodId, $prodReviewId) 
    {
        $req = $this->client->get('products/' . $prodId . '/reviews/' . $prodReviewId, $this->headers, array('exceptions' => FALSE));
        $resp = $req->send();
     
        if ($resp->getStatusCode() == 200) {
            return $resp->json();
        } else {
            return $resp->getMessage();
        }
    }
}
