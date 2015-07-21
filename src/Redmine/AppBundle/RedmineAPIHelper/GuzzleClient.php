<?php

namespace Redmine\AppBundle\RedmineAPIHelper;

use GuzzleHttp\Client;

class GuzzleClient 
{
    protected $redmine_base_url;

    public function __construct($redmine_url)
    {
        $this->redmine_base_url = $redmine_url;
    }

    public function redmineLogin($username, $password)
    {
        $client = new Client();

        $guzzleResponse = $client->get($this->redmine_base_url . '/users/current.json', [
            'auth' => [$username, $password]
        ]);

        return $guzzleResponse->getBody()->getContents();
    }
}
