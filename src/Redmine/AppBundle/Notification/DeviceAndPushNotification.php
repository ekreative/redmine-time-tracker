<?php

namespace Redmine\AppBundle\Notification;

use Aws\Sns\SnsClient;
use Doctrine\ORM\EntityManager;
use Mcfedr\AwsPushBundle\Message\Message;
use Mcfedr\AwsPushBundle\Service\Devices;
use Mcfedr\AwsPushBundle\Service\Messages;
use Psr\Log\LoggerInterface;
use Redmine\AppBundle\Entity\Device;
use Redmine\AppBundle\Entity\RedmineUser;

class DeviceAndPushNotification
{
    const TTL_ONE_DAY = 86400;      // 60*60*24
    const TTL_ONE_WEEK = 604800;   //  60*60*24*7

    const PLATFORM_IOS = 'ios';
    const PLATFORM_IOS_SB = 'ios_sb';
    const PLATFORM_ANDROID = 'android';

    /** @var  LoggerInterface */
    private $logger;

    /** @var  Messages */
    private $messages;

    /** @var  Devices */
    private $devices;

    /** @var  EntityManager $em */
    protected $em;

    /** @var  Snsclient */
    private $sns;

    function __construct(LoggerInterface $logger, Messages $messages, Devices $devices, EntityManager $entityManager, SnsClient $sns)
    {
        $this->logger = $logger;
        $this->messages = $messages;
        $this->devices = $devices;
        $this->em = $entityManager;
        $this->sns = $sns;
    }

    public function sendNotify(Message $message, RedmineUser $user)
    {
        if ($user->getDevices()->count()) {
            /** @var Device $device */
            foreach ($user->getDevices() as $device) {
                if ($device->isEnabled()) {
                    $logMessage = 'platform not found';
                    $logCustom = 'custom';
                    if ($device->getPlatform() === self::PLATFORM_ANDROID) {
                        $logMessage = $message->getGcmData();
                        $logCustom = $message->getCustom();
                    }
                    if ($device->getPlatform() === self::PLATFORM_IOS) {
                        $logMessage = $message->getApnsData();
                        $logCustom = $message->getCustom();
                    }
                    if ($device->getPlatform() === self::PLATFORM_IOS_SB) {
                        $logMessage = $message->getApnsData();
                        $logCustom = $message->getCustom();
                    }

                    try {
                        $this->messages->send($message, $device->getArn());
                        $this->logger->info("\nSend message to: ", [
                            'DEVICE' => $device->toLog(),
                            'MESSAGE' => $logMessage,
                            'CUSTOM' => $logCustom
                        ]);
                    } catch (\Exception $e) {
                        $device->setEnabled(false);
                        $this->em->flush();
                        $this->logger->critical("\n Device is disabled: ", [
                            'DeviceArn: ' => $device->getArn()
                        ]);
                    }
                }
                if (!$device->isEnabled()) {
                    $newDevice = $this->getDevice($device->getUser(), $device->getDeviceId(), $device->getPushtoken(), $device->getPlatform());

                    $logMessage = 'platform not found';
                    $logCustom = 'custom';
                    if ($device->getPlatform() === self::PLATFORM_ANDROID) {
                        $logMessage = $message->getGcmData();
                        $logCustom = $message->getCustom();
                    }
                    if ($device->getPlatform() === self::PLATFORM_IOS) {
                        $logMessage = $message->getApnsData();
                        $logCustom = $message->getCustom();
                    }
                    if ($device->getPlatform() === self::PLATFORM_IOS_SB) {
                        $logMessage = $message->getApnsData();
                        $logCustom = $message->getCustom();
                    }

                    try {
                        $this->messages->send($message, $newDevice->getArn());
                        $this->logger->info("\nSend message to: ", [
                            'DEVICE' => $device->toLog(),
                            'MESSAGE' => $logMessage,
                            'CUSTOM' => $logCustom
                        ]);
                    } catch (\Exception $e) {
                        $device->setEnabled(false);
                        $this->em->flush();
                        $this->logger->critical("\n Device is disabled: ", [
                            'DeviceArn: ' => $device->getArn()
                        ]);
                    }

                }
            }  //  end foreach devices
        }
    }

    /**
     * @param null $text
     * @param bool $contentAvailable
     * @param array $options
     * @return Message
     */
    public function getMessage($text = null, $contentAvailable = false, $options = [])
    {
        $message = new Message($text);
        $message->setBadge(0);
        if ($contentAvailable) {
            $message->setContentAvailable(true);
        }
        $message->setTtl(self::TTL_ONE_DAY);

        if (count($options)) {
            $message->setCustom($options);
        }

        return $message;
    }

    /**
     * @param RedmineUser $user
     * @param $deviceId
     * @param $pushToken
     * @param $platform
     * @return Device
     */
    public function getDevice(RedmineUser $user, $deviceId, $pushToken, $platform)
    {
        $device = $this->em->getRepository('RedmineAppBundle:Device')->findOneBy(['pushToken' => $pushToken]);
        if (!$device) {
            $device = $this->em->getRepository('RedmineAppBundle:Device')->findOneBy(['deviceId' => $deviceId]);
            if (!$device) {
                $device = $this->newDevice($user, $deviceId, $pushToken, $platform);
            }
        }

        if (!$device->isEnabled() || $device->getPlatform() != $platform || $device->getUser() != $user) {
            $this->removeDevice($device);
            $device = $this->newDevice($user, $deviceId, $pushToken, $platform);
        }

        $this->em->flush();

        return $device;
    }

    private function removeDevice(Device $device)
    {
        if ($device->getArn()) {
            $this->devices->unregisterDevice($device->getArn());
        }

        $this->em->remove($device);
        $this->em->flush();
        $this->logger->info("\n Removed device: ", ['DEVICE' => $device->toLog()]);
    }

    private function newDevice(RedmineUser $user, $deviceId, $pushToken, $platform)
    {
        $device = $this->em->getRepository('RedmineAppBundle:Device')->findOneBy(['pushToken' => $pushToken]);

        if (!$device) {
            $device = $this->em->getRepository('RedmineAppBundle:Device')->findOneBy(['deviceId' => $deviceId]);
        }
        if ($device) {
            $device->setUser($user);
            $device->setPlatform($platform);
            $device->setPushtoken($pushToken);

            return $device;
        } else {
            $device = new Device();
            $device->setDeviceid($deviceId);
            $device->setEnabled(true);
            $device->setArn($this->devices->registerDevice($pushToken, $platform));
            $device->setPushtoken($pushToken);
            $device->setPlatform($platform);
            $device->setUser($user);

            $this->em->persist($device);
            $this->logger->info("\n New device: ", ['DEVICE' => $device->toLog()]);

            return $device;
        }
    }
}
