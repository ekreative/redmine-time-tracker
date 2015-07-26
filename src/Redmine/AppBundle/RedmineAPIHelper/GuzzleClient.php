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

    public function getSpentTime($redmineToken, $day, $redmineUserId)
    {
        $client = new Client();

        //   https://redmine.ekreative.com/time_entries.json?spent_on=2015-07-23&user_id=90

        $guzzleResponse = $client->get($this->redmine_base_url . 'time_entries.json', [
            'headers' => [
                "X-Redmine-API-Key" => $redmineToken
            ],
            'query' => [
                "user_id" => $redmineUserId,
                "spent_on" => $day
            ]
        ]);

        $result = json_decode($guzzleResponse->getBody());

        $hours = 0;
        foreach ($result->time_entries as $project) {
            $hours += $project->hours;
        }

        return $hours;
    }

    public function getInformationForTodayTrackedProject($redmineToken, $day, $redmineUserId)
    {
        $client = new Client();

        $guzzleResponse = $client->get($this->redmine_base_url . 'time_entries.json', [
            'headers' => [
                "X-Redmine-API-Key" => $redmineToken
            ],
            'query' => [
                "user_id" => $redmineUserId,
                "spent_on" => $day
            ]
        ]);

        $redmineResponse = json_decode($guzzleResponse->getBody());

        $result = [];
        $hours = 0;
        foreach ($redmineResponse->time_entries as $project) {
            $hours += $project->hours;
            $result[] = [
                'name' => $project->project->name,
                'hours' => $project->hours,
                'comment' => $project->comments ? $project->comments : ''
            ];
        }

        return ['info' => $result, 'totalHours' => $hours];
    }
}
