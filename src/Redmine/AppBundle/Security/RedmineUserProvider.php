<?php

namespace Redmine\AppBundle\Security;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Redmine\AppBundle\Entity\RedmineUser;
use Redmine\AppBundle\Entity\Settings;
use Redmine\AppBundle\RedmineAPIHelper\GuzzleClient;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class RedmineUserProvider implements UserProviderInterface
{
    /** @var EntityManager $em */
    private $em;

    private $client;
    private $encoder;

    public function __construct(EntityManager $em, GuzzleClient $client, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->client = $client;
        $this->encoder = $encoder;
    }

    public function authUserFromRedmine(UsernamePasswordToken $token)
    {
        $pass = $token->getCredentials();
        $username = $token->getUser();
        try {
            $response = $this->client->redmineLogin($username, $pass);

            $q = json_decode($response);

            $em = $this->em;
            $user = $em->getRepository("RedmineAppBundle:RedmineUser")->findOneBy([
                'redmineToken' => $q->user->api_key,
                'redmineUserID' => $q->user->id
            ]);
            if ($user) {
                return $user->getUsername();
            } else {
                $user = new RedmineUser();
                $user
                    ->setUsername($q->user->login)
                    ->setEmail($q->user->mail)
                    ->setPassword($this->encoder->encodePassword($user, "redmine"))
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

                $this->em->persist($user);
                $this->em->persist($settings);

                $this->em->flush();

                return $user->getUsername();
            }

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository("RedmineAppBundle:RedmineUser")->findOneBy(['username' => $username]);
        if ($user and $user instanceof RedmineUser)
        {
            return $user;
        }

        throw new UsernameNotFoundException();
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'Redmine\AppBundle\Entity\RedmineUser' === $class;
    }
}
