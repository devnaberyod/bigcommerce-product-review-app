<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends AuthBaseController
{
    public function appInstallAction(Request $request)
    {

        $params = $request->query->all();

        if (!isset($params['code'])) return new Response('Invalid query parameters.');

    	$isInstall = $this->install($params);
        
        return $this->redirectToRoute('app_login', ['is_new' => 1], 301);
    }

    public function appLoadAction(Request $request)
    {
        $params = $request->query->all();

        if (!isset($params['signed_payload'])) return new Response('Invalid query parameters.');

        list($encodedData, $encodedSignature) = explode('.', $params['signed_payload'], 2); 

        // decode the data
        $signature = base64_decode($encodedSignature);
        $jsonStr = base64_decode($encodedData);
        $data = json_decode($jsonStr, true);
        $appConfig = $this->getParameter('app_config');
        // confirm the signature
        $expectedSignature = hash_hmac('sha256', $jsonStr, $appConfig['client_secret'], $raw = false);
        
        if (!hash_equals($expectedSignature, $signature)) {
            return new Response('Bad signed request from Bigcommerce');
        }

        return $this->redirectToRoute('app_dashboard');
    }

    public function appUninstallAction(Request $request)
    {
        
    }   

    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'AppBundle::index.html.twig',
            array(
                'last_username' => $lastUsername,
                'error'         => $error,
                'params' => $request->query->all()
            )
        );
      
    }

    public function loginshekAction(Request $request)
    {
        
    }
}
