<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Http\Client;

class AuthBaseController extends Controller
{

    protected function install(Array $oauthTokenData)
    {
        $appConfig = $this->getParameter('app_config');
        $headerParams = [];

        $headerParams['code'] = $oauthTokenData['code'];
        $headerParams['scope'] = $oauthTokenData['scope'];
        $headerParams['context'] = $oauthTokenData['context'];
        $headerParams['client_id'] = $appConfig['client_id'];
        $headerParams['client_secret'] = $appConfig['client_secret'];
        $headerParams['redirect_uri'] = 'https://cleverlocalapps.com/app-clevreviews/web/auth/app/install';
        $headerParams['grant_type'] = 'authorization_code';


    	$client = new Client($appConfig['oauth_url']);
    	$req = $client->post('/oauth2/token', [], $headerParams, array(
    		'exceptions' => false,
    	));

    	$resp = $req->send();

    	if ($resp->getStatusCode() == 200) {

    		$data = $resp->json();
    		
    		$credentials = [];
          
            $credentials['access_token'] = $data['access_token'];
            $credentials['api_key'] = sha1(time());
            $credentials['scope'] = $data['scope'];
            $credentials['context'] = $data['context'];
            $credentials['store_user'] = json_encode($data['user']);
           
    		$userCredential = $this->saveStoreCredentials($credentials);
            $user = $this->createUserCredential($userCredential);

            $sendEmail = $this->sendCredentialsToEmail($data['user']['email'], $user);
            
            return $sendEmail;
                
    	} else
    		 return $resp;
    }

    private function saveStoreCredentials(Array $data)
    {
    	$storeCredential = new \AppBundle\Entity\StoreCredential();
    	$storeCredential->setAccessToken($data['access_token']);
    	$storeCredential->setApiKey($data['api_key']);
        $storeCredential->setStoreUser($data['store_user']);
        $storeCredential->setContext($data['context']);
    	$storeCredential->setScope($data['scope']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($storeCredential);
        $entityManager->flush();

        return $storeCredential;
    }

    private function createUserCredential($authCredential)
    {
        $user = new \AppBundle\Entity\User();

        $username = substr(number_format(time() * rand(),0,'',''),0,6);
        $encoder =  $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setSalt(md5(uniqid()));
        $encoded = $encoder->encodePassword($username, $user->getSalt());
       
        $user->setUsername($username);
        $user->setPassword($encoded);
        $user->setEmail($username . '@clevreviews.com');
        $user->setRoles(array('ROLE_ADMIN'));
        $user->setAuthCredentials($authCredential);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    private function createStoreWebHook(Array $data)
    {

    }

    private function sendCredentialsToEmail($toEmail, $user)
    {
        $data = [];
        $data['username'] = $user->getUsername();
        $data['password'] = $user->getUsername();

        $message = \Swift_Message::newInstance()
            ->setSubject('Temporary User Credentials')
            ->setFrom('clevreviews@cleverlocal.com.au')
            ->setTo([$toEmail, 'diovannie@cleverlocal.com.au', 'jayson@cleverlocal.com.au', 'armand@cleverlocal.com.au'])
            ->setBody(
                $this->renderView(
                    'AppBundle::email.html.twig',
                    array('user' => $data)
                ),
                'text/html'
            );
      
        return $this->get('mailer')->send($message);
    }
}
