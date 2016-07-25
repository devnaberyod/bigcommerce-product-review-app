<?php

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testAll()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'users');
    }

    public function testGet()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'customers');
    }

}
