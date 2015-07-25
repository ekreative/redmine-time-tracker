<?php

namespace Redmine\AppBundle\TimeChecker;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Mcfedr\AwsPushBundle\Message\Message;
use Mcfedr\AwsPushBundle\Service\Messages;
use Mcfedr\ResqueBundle\Manager\ResqueManager;
use Mcfedr\ResqueBundle\Worker\WorkerInterface;
use Redmine\AppBundle\Entity\Device;
use Redmine\AppBundle\Entity\RedmineUser;
use Redmine\AppBundle\Notification\SMSClient;
use Redmine\AppBundle\RedmineAPIHelper\GuzzleClient;

class Checker implements WorkerInterface
{
    /** @var  EntityManager $em */
    protected $em;

    protected $queueName;

    /** @var  ResqueManager */
    protected $resqueManager;

    /** @var Messages $messages */
    protected $messages;

    /** @var GuzzleClient $client */
    protected $client;

    protected $alfa_sms_name;
    protected $alfa_sms_ID;
    protected $alfa_sms_password;
    protected $alfa_sms_api_key;

    public function __construct(
        EntityManager $entityManager,
        ResqueManager $resqueManager,
        $queue_name,
        Messages $messages,
        GuzzleClient $client,
        $alfa_sms_name,
        $alfa_sms_ID,
        $alfa_sms_password,
        $alfa_sms_api_key
    ) {
        $this->em = $entityManager;
        $this->resqueManager = $resqueManager;
        $this->queueName = $queue_name;
        $this->messages = $messages;
        $this->client = $client;

        $this->alfa_sms_name = $alfa_sms_name;
        $this->alfa_sms_ID = $alfa_sms_ID;
        $this->alfa_sms_password = $alfa_sms_password;
        $this->alfa_sms_api_key = $alfa_sms_api_key;
    }

    public function start(RedmineUser $user)
    {
        if ($oldJob = $user->getJobDescription()) {
            $this->resqueManager->delete($oldJob);
        }

        $now = Carbon::now();
        if ($now->dayOfWeek == Carbon::SATURDAY) {
            $now->addDays(2);
        } elseif ($now->dayOfWeek == Carbon::SUNDAY) {
            $now->addDay();
        }

        if ($now->format('H:i') < $user->getSettings()->getCheckFirst()->format('H:i')) {
            $nextDate = $now->copy();
            $nextDate->startOfDay()->hour((int)$user->getSettings()->getCheckFirst()->format('H'))->minute((int)$user->getSettings()->getCheckFirst()->format('i'));

            $job = $this->resqueManager->put('redmine.timeChecker', [
                'userId' => $user->getId(),
                'date' => $nextDate->format('Y-m-d'),
                'checkNum' => 1
            ], $this->queueName, $nextDate);
            $user->setJobDescription($job);
            $this->em->flush();
        }

        if (($now->format('H:i') > $user->getSettings()->getCheckFirst()->format('H:i')) and ($now->format('H:i') < $user->getSettings()->getCheckSecond()->format('H:i'))) {
            $nextDate = $now->copy();
            $nextDate->startOfDay()->hour((int)$user->getSettings()->getCheckSecond()->format('H'))->minute((int)$user->getSettings()->getCheckSecond()->format('i'));

            $job = $this->resqueManager->put('redmine.timeChecker', [
                'userId' => $user->getId(),
                'date' => $nextDate->format('Y-m-d'),
                'checkNum' => 2
            ], $this->queueName, $nextDate);
            $user->setJobDescription($job);
            $this->em->flush();
        }

        if ($now->format('H:i') > $user->getSettings()->getCheckSecond()->format('H:i')) {
            $nextDate = $now->copy();
            $nextDate->addDay();
            $lastWorkDate = null;
            if ($nextDate->dayOfWeek == Carbon::SATURDAY) {
                $lastWorkDate = $nextDate->copy();
                $nextDate->addDays(2);
                $lastWorkDate->subDays(1);
            } elseif ($nextDate->dayOfWeek == Carbon::SUNDAY) {
                $lastWorkDate = $nextDate->copy();
                $nextDate->addDays(1);
                $lastWorkDate->subDays(2);
            }

            $nextDate->startOfDay()->hour((int)$user->getSettings()->getCheckThird()->format('H'))->minute((int)$user->getSettings()->getCheckThird()->format('i'));

            $job = $this->resqueManager->put('redmine.timeChecker', [
                'userId' => $user->getId(),
                'date' => $lastWorkDate ? $lastWorkDate->format('Y-m-d') : $nextDate->format('Y-m-d'),
                'checkNum' => 3
            ], $this->queueName, $nextDate);
            $user->setJobDescription($job);
            $this->em->flush();
        }
    }

    /**
     * @param RedmineUser $user
     */
    public function stop(RedmineUser $user)
    {
        $job = $user->getJobDescription();

        if ($job) {
            $this->resqueManager->delete($job);
            $user->setJobDescription(null);
            $this->em->flush();
        }
    }

    /**
     * Called to start the queued task
     *
     * @param array $options
     * @throws \Exception
     */
    public function execute(array $options = null)
    {
        $userId = (int)$options['userId'];
        $date = $options['date'];   //2015-07-23  Y-m-d
        $checkNum = (int)$options['checkNum'];

        $user = $this->em->getRepository('RedmineAppBundle:RedmineUser')->find($userId);
        $spentHours = $this->client->getSpentTime($user->getRedmineToken(), $date, $user->getRedmineUserID());

        if ($spentHours == 0) {
            $notificationMessage = "You need track your time!";
        } else {
            $notificationMessage = "You tracked only {$spentHours} hours.";
        }

        if ($checkNum == 1) {
            if ($spentHours < 7) {

                if ($user->getSettings()->isPush()) {
                    $this->sendPush($user, $notificationMessage);
                }
                if ($user->getSettings()->isSms() and $user->getSettings()->getPhone()) {
                    $this->sendSMS($user, $notificationMessage);
                }
            }

            $nextDate = Carbon::now();
            $nextDate->startOfDay()->hour((int)$user->getSettings()->getCheckSecond()->format('H'))->minute((int)$user->getSettings()->getCheckSecond()->format('i'));

            $job = $this->resqueManager->put('redmine.timeChecker', [
                'userId' => $user->getId(),
                'date' => $nextDate->format('Y-m-d'),
                'checkNum' => 2
            ], $this->queueName, $nextDate);
            $user->setJobDescription($job);
            $this->em->flush();
        }

        if ($checkNum == 2) {
            if ($spentHours < 7) {

                if ($user->getSettings()->isPush()) {
                    $this->sendPush($user, $notificationMessage);
                }
                if ($user->getSettings()->isSms() and $user->getSettings()->getPhone()) {
                    $this->sendSMS($user, $notificationMessage);
                }
            }

            $nextDate = Carbon::createFromFormat('Y-m-d', $date);
            $nextDate->addDay();
            if ($nextDate->dayOfWeek == Carbon::SATURDAY) {
                $nextDate->addDays(2);
            } elseif ($nextDate->dayOfWeek == Carbon::SUNDAY) {
                $nextDate->addDays(1);
            }

            $nextDate->startOfDay()->hour((int)$user->getSettings()->getCheckThird()->format('H'))->minute((int)$user->getSettings()->getCheckThird()->format('i'));

            $job = $this->resqueManager->put('redmine.timeChecker', [
                'userId' => $user->getId(),
                'date' => $date,
                'checkNum' => 3
            ], $this->queueName, $nextDate);
            $user->setJobDescription($job);
            $this->em->flush();
        }

        if ($checkNum == 3) {
            if ($spentHours < 7) {

                if ($user->getSettings()->isPush()) {
                    $this->sendPush($user, $notificationMessage);
                }
                if ($user->getSettings()->isSms() and $user->getSettings()->getPhone()) {
                    $this->sendSMS($user, $notificationMessage);
                }
            }

            $nextDate = Carbon::now();
            $nextDate->startOfDay()->hour((int)$user->getSettings()->getCheckFirst()->format('H'))->minute((int)$user->getSettings()->getCheckFirst()->format('i'));

            $job = $this->resqueManager->put('redmine.timeChecker', [
                'userId' => $user->getId(),
                'date' => $nextDate->format('Y-m-d'),
                'checkNum' => 1
            ], $this->queueName, $nextDate);
            $user->setJobDescription($job);
            $this->em->flush();
        }

    }

    private function sendPush(RedmineUser $user, $message)
    {
        $message = new Message($message);
        $message->setTtl(86400);
        $message->setBadge(0);

        /** @var Device $device */
        foreach ($user->getDevices() as $device) {
            try {
                $this->messages->send($message, $device->getArn());
            } catch (\Exception $e) {
                $device->setEnabled(false);
                $this->em->flush();
            }
        }
    }

    private function sendSMS(RedmineUser $user, $message)
    {
        $clientSMS = new SMSClient($this->alfa_sms_ID, $this->alfa_sms_password, $this->alfa_sms_api_key);

        try {
            $clientSMS->sendSMS($this->alfa_sms_name, $user->getSettings()->getPhone(), $message);
        } catch (\Exception $e) {}
    }
}
