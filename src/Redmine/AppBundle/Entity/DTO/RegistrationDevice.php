<?php

namespace Redmine\AppBundle\Entity\DTO;

class RegistrationDevice
{
    protected $pushPlatform;
    protected $pushToken;
    protected $deviceId;

    /**
     * @return mixed
     */
    public function getPushPlatform()
    {
        return $this->pushPlatform;
    }

    /**
     * @param mixed $pushPlatform
     * @return RegistrationDevice
     */
    public function setPushPlatform($pushPlatform)
    {
        $this->pushPlatform = $pushPlatform;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPushToken()
    {
        return $this->pushToken;
    }

    /**
     * @param mixed $pushToken
     * @return RegistrationDevice
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
     * @return RegistrationDevice
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;

        return $this;
    }
}
