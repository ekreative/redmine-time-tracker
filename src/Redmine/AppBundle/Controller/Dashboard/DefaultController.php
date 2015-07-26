<?php

namespace Redmine\AppBundle\Controller\Dashboard;

use Carbon\Carbon;
use Redmine\AppBundle\Entity\RedmineUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="admin_user_home")
     * @Template()
     */
    public function userDefaultAction()
    {
        /** @var RedmineUser $user */
        $user = $this->getUser();
        $date = Carbon::now()->format('Y-m-d');

        $trackedInformation = $this->get('redmine.guzzle_client')->getInformationForTodayTrackedProject($user->getRedmineToken(), $date, $user->getRedmineUserID());

        return [
            'date' => $date,
            'hours' => $trackedInformation['totalHours'],
            'info' => $trackedInformation['info']
        ];
    }
}
