<?php

namespace Redmine\AppBundle\Controller\Api;

use Doctrine\ORM\EntityManager;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use Redmine\AppBundle\Entity\DTO\ApiUserLogin;
use Redmine\AppBundle\Entity\RedmineUser;
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

                $user = new RedmineUser();
                $user
                    ->setUsername($q->user->login)
                    ->setEmail($q->user->mail)
                    ->setName($q->user->firstname)
                    ->setSurname($q->user->lastname)
                    ->setRedmineUserID($q->user->id)
                    ->setRedmineToken($q->user->api_key);

                $em->persist($user);
                $password = $this->get('security.password_encoder')->encodePassword($user, "redmine");
                $user->setPassword($password);

                $em->flush();
            }
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Redmine user: bad credentials'], 403);
        }

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
