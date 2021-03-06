<?php

namespace Redmine\AppBundle\Entity\DTO;

class Device
{
    protected $pushToken;
    protected $deviceId;

    /**
     * @return mixed
     */
    public function getPushToken()
    {
        return $this->pushToken;
    }

    /**
     * @param mixed $pushToken
     * @return Device
     */
    public function setPushToken($pushToken)
    {
        $this->pushToken = $pushToken;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @param mixed $deviceId
     * @return Device
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;

        return $this;
    }
}
