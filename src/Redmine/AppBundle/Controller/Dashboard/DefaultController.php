<?php

namespace Redmine\AppBundle\Controller\Dashboard;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Redmine\AppBundle\Entity\DTO\Date;
use Redmine\AppBundle\Entity\RedmineUser;
use Redmine\AppBundle\Form\DTO\DateDTOType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

        if (in_array($user->getRedmineUserID(), $this->getParameter('admin_users'))) {

            return $this->redirectToRoute('admin_user_adminhome');
        }

        $date = Carbon::now()->format('Y-m-d');

        $trackedInformation = $this->get('redmine.guzzle_client')->getInformationForTodayTrackedProject($user->getRedmineToken(), $date, $user->getRedmineUserID());

        return [
            'date' => $date,
            'hours' => $trackedInformation['totalHours'],
            'info' => $trackedInformation['info']
        ];
    }

    /**
     * @Route("/reports", name="admin_user_adminhome")
     * @Template()
     * @Method({"GET", "POST"})
     */
    public function adminHomePageAction(Request $request)
    {
        /** @var RedmineUser $admin */
        $admin = $this->getUser();
        $adminsList = $this->getParameter('admin_users');

        if (!in_array($admin->getRedmineUserID(), $adminsList)) {

            return $this->redirectToRoute('admin_user_home');
        }

        $now = Carbon::now();
        $reportFor = Carbon::now();
        if ($now->dayOfWeek == Carbon::SATURDAY) {
            $reportFor->subDays(1);
        } elseif ($now->dayOfWeek == Carbon::SUNDAY) {
            $reportFor->subDays(2);
        } else {
            $reportFor->subDays(1);
        }

        $dateFiler = new Date();
        $dateFiler->setFilterDate($reportFor);
        $form = $this->createForm(new DateDTOType(), $dateFiler);

        $form->handleRequest($request);

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository("RedmineAppBundle:RedmineUser")->findBy([], ['name' => 'ASC']);
        $redmineClient = $this->get('redmine.guzzle_client');

        $result = [];
        /** @var RedmineUser $user */
        foreach ($users as $user) {
            if (in_array($user->getRedmineUserID(), $adminsList)) {

                continue;
            }

            $result[] = [
                "user" => $user->getName() . " " . $user->getSurname(),
                "time" => $redmineClient->getInformationForTodayTrackedProject($admin->getRedmineToken(), $dateFiler->getFilterDate()->format('Y-m-d'), $user->getRedmineUserID())
            ];
        }

        return [
            "form" => $form->createView(),
            "result" => $result
        ];
    }
}
