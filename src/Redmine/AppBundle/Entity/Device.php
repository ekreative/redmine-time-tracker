<?php

namespace Redmine\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Device
 *
 * @ORM\Table("devices")
 * @ORM\Entity(repositoryClass="Redmine\AppBundle\Entity\Repository\DeviceRepository")
 */
class Device implements \JsonSerializable
{
    use CreateUpdateTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="deviceId", type="string", length=255, nullable=true)
     */
    private $deviceId;

    /**
     * @var string
     *
     * @ORM\Column(name="pushToken", type="text", nullable=true)
     */
    private $pushToken;

    /**
     * @var string
     *
     * @ORM\Column(name="arn", type="string", length=255, nullable=true)
     */
    private $arn;

    /**
     * @var string
     *
     * @ORM\Column(name="platform", type="string", length=15, nullable=true)
     */
    private $platform;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    private $enabled;

    /**
     * @ORM\ManyToOne(targetEntity="Redmine\AppBundle\Entity\RedmineUser", inversedBy="devices")
     */
    protected $user;

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return [
            "pushPlatform" => $this->getPlatform(),
            "pushToken" => $this->getPushToken(),
            "deviceId" => $this->getDeviceId(),
            "enabled" => $this->isEnabled()
        ];
    }

    /**
     * @return array
     */
    public function toLog()
    {
        return [
            "deviceId" => $this->getId(),
            "deviceArn" => $this->getArn()
        ];
    }

    /**
     * Set deviceId
     *
     * @param string $deviceId
     * @return Device
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;

        return $this;
    }

    /**
     * Get deviceId
     *
     * @return string
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * Set pushToken
     *
     * @param string $pushToken
     * @return Device
     */
    public function setPushToken($pushToken)
    {
        $this->pushToken = $pushToken;

        return $this;
    }

    /**
     * Get pushToken
     *
     * @return string
     */
    public function getPushToken()
    {
        return $this->pushToken;
    }

    /**
     * Set arn
     *
     * @param string $arn
     * @return Device
     */
    public function setArn($arn)
    {
        $this->arn = $arn;

        return $this;
    }

    /**
     * Get arn
     *
     * @return string
     */
    public function getArn()
    {
        return $this->arn;
    }

    /**
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param string $platform
     * @return Device
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Device
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return RedmineUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param RedmineUser $user
     * @return Device
     */
    public function setUser(RedmineUser $user)
    {
        $this->user = $user;

        return $this;
    }
}
