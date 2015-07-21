<?php

namespace Redmine\AppBundle\Entity\DTO;
use Symfony\Component\Validator\Constraints as Assert;

class ApiUserLogin
{
    /**
     * @Assert\NotBlank(message="Обов'язкове поле")
     */
    protected $username;

    /**
     * @Assert\NotBlank(message="Обов'язкове поле")
     */
    protected $password;

    /**
     * @Assert\NotBlank(message="Обов'язкове поле")
     */
    protected $pushPlatform;

    /**
     * @Assert\NotBlank(message="Обов'язкове поле")
     */
    protected $pushToken;

    /**
     * @Assert\NotBlank(message="Обов'язкове поле")
     */
    protected $deviceId;

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPushPlatform()
    {
        return $this->pushPlatform;
    }

    /**
     * @param mixed $pushPlatform
     */
    public function setPushPlatform($pushPlatform)
    {
        $this->pushPlatform = $pushPlatform;
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
     */
    public function setPushToken($pushToken)
    {
        $this->pushToken = $pushToken;
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
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;
    }
}
