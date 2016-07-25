<?php

namespace AppBundle\Utils;

class KeyGenerator
{
    public function generateSalt()
    {
    	return sha1(time() . uniqid());
    }
}