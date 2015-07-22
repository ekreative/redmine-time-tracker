<?php

namespace Redmine\AppBundle\Controller\Api;

use Doctrine\ORM\EntityManager;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use Redmine\AppBundle\Entity\DTO\ApiUserLogin;
use Redmine\AppBundle\Entity\RedmineUser;
use Redmine\AppBundle\Entity\Settings;
use Redmine\AppBundle\Form\LoginApiType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends JsonController
{
    /**
     * @Route("/login", name="api_login")
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
                    ->setPassword($passwordEncoder->encodePassword($user, "redmine"))
                    ->setName($q->user->firstname)
                    ->setSurname($q->user->lastname)
                    ->setRedmineUserID($q->user->id)
                    ->setRedmineToken($q->user->api_key);

                $settings = new Settings();
                $settings
                    ->setSms(false)
                    ->setPush(false)
                    ->setNone(true)
                    ->setUser($user);

                $em->persist($user);
                $em->persist($settings);

                $em->flush();
            }
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Redmine user: bad credentials'], 403);
        }

        $this->get('redmine.device.notification')->getDevice($user, $userDTO->getDeviceId(), $userDTO->getPushToken(), $userDTO->getPushPlatform());

        return new JsonResponse($user);
    }

    /**
     * @Route("/get/user")
     * @Method("GET")
     */
    public function getUserAction()
    {
        return new JsonResponse($this->getUser());
    }
}
