<?php

namespace Redmine\AppBundle\RedmineAPIHelper;

use GuzzleHttp\Client;

class GuzzleClient 
{
    const REDMINE_BASE_URL = "https://redmine.ekreative.com/";

    public function redmineLogin($username, $password)
    {
        $client = new Client();

        $guzzleResponse = $client->get(self::REDMINE_BASE_URL . 'users/current.json', [
            'auth' => [$username, $password]
        ]);

        return $guzzleResponse->getBody()->getContents();
    }
}
