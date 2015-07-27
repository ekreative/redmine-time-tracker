<?php

namespace Redmine\AppBundle\Controller\Api;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use Redmine\AppBundle\Entity\Device;
use Redmine\AppBundle\Entity\DTO\ApiUserLogin;
use Redmine\AppBundle\Entity\RedmineUser;
use Redmine\AppBundle\Entity\Settings;
use Redmine\AppBundle\Form\LoginApiType;
use Redmine\AppBundle\Form\SettingsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends JsonController
{
    /**
     * @Route("/device", name="api_login")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function loginAction(Request $request)
    {
        $userDTO = new ApiUserLogin();
        $form = $this->createForm(new LoginApiType(), $userDTO);
        $this->handleJsonForm($form, $request);

        $redmineClient = $this->get('redmine.guzzle_client');
        try {
            $redmineResponse = $redmineClient->redmineLogin($userDTO->getUsername(), $userDTO->getPassword());

            $q = json_decode($redmineResponse);

            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("RedmineAppBundle:RedmineUser")->findOneBy([
                'redmineToken' => $q->user->api_key,
                'redmineUserID' => $q->user->id
            ]);
            if (!$user) {
                $passwordEncoder = $this->get('security.password_encoder');
                $user = new RedmineUser();
                $user
                    ->setUsername($q->user->login)
                    ->setEmail($q->user->mail)
                    ->setPassword($passwordEncoder->encodePassword($user, md5(uniqid())))
                    ->setName($q->user->firstname)
                    ->setSurname($q->user->lastname)
                    ->setRedmineUserID($q->user->id)
                    ->setRedmineToken($q->user->api_key);

                $settings = new Settings();
                $settings
                    ->setSms(false)
                    ->setPush(false)
                    ->setCheckFirst(Carbon::createFromTime(17, 45))
                    ->setCheckSecond(Carbon::createFromTime(20, 0))
                    ->setCheckThird(Carbon::createFromTime(9, 30))
                    ->setUser($user);

                $em->persist($user);
                $em->persist($settings);

                $em->flush();
            }
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Redmine user: bad credentials'], 403);
        }

        $this->get('redmine.device.notification')->getDevice($user, $userDTO->getDeviceId(), $userDTO->getPushToken(), $userDTO->getPushPlatform());

        $device = $em->getRepository('RedmineAppBundle:Device')->findOneBy([
            'deviceId' => $userDTO->getDeviceId(),
            'pushToken' => $userDTO->getPushToken()
        ]);

        return new JsonResponse($device);
    }

    /**
     * @Route("/device/{id}", name="api_uregister_device")
     * @Method("DELETE")
     * @ParamConverter("device", class="RedmineAppBundle:Device")
     * @param Device $device
     * @return JsonResponse
     */
    public function unRegisterDeviceAction(Device $device)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $em->remove($device);
        $em->flush();

        $this->get('redmine.timeChecker')->stop($this->getUser());

        return new JsonResponse(['message' => "removed"]);
    }

    /**
     * @Route("/user/settings", name="api_settings_update")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function settingsAction(Request $request)
    {
        /** @var RedmineUser $user */
        $user = $this->getUser();

        $settings = $user->getSettings();
        $form = $this->createForm(new SettingsType(), $settings);

        if ($request->getMethod() == "POST") {
            $this->handleJsonForm($form, $request);
            if ($form->isValid()) {
                /** @var EntityManager $em */
                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $this->get('redmine.timeChecker')->start($user);

                return new JsonResponse($user->getSettings());
            }
        }

        return new JsonResponse(['message' => 'Something wrong'], 400);
    }

    /**
     * @Route("/user/settings", name="api_usersettings_get")
     * @Method("GET")
     */
    public function getUserSettings()
    {
        /** @var RedmineUser $user */
        $user = $this->getUser();

        return new JsonResponse($user->getSettings(), 200);
    }
}
