<?php

namespace Redmine\AppBundle\Controller\Dashboard;

use Doctrine\ORM\EntityManager;
use Redmine\AppBundle\Entity\RedmineUser;
use Redmine\AppBundle\Form\SettingsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SettingsController extends Controller
{
    /**
     * @Route("/settings", name="user_settings")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function settingsAction(Request $request)
    {
        /** @var RedmineUser $user */
        $user = $this->getUser();
        $settings = $user->getSettings();

        $form = $this->createForm(new SettingsType(), $settings);

        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            if ($form->isValid()) {

                /** @var EntityManager $em */
                $em = $this->getDoctrine()->getManager();
                $em->flush();

//                $this->get('redmine.timeChecker')->start($user);

                $this->addFlash('success', 'Settings was updated successfully. Resque manager was restarted with new parameters');
                return $this->redirectToRoute('admin_user_home');
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
