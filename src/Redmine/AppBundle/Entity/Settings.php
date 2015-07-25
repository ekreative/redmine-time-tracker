<?php

namespace Redmine\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Settings
 *
 * @ORM\Table("settings")
 * @ORM\Entity(repositoryClass="Redmine\AppBundle\Entity\Repository\SettingsRepository")
 */
class Settings
{
    use CreateUpdateTrait;

    /**
     * @ORM\Column(name="sms", type="boolean")
     */
    protected $sms;

    /**
     * @ORM\Column(name="push", type="boolean")
     */
    protected $push;

    /**
     * @Assert\NotBlank(message="Обов'язкове поле")
     * @ORM\Column(name="checkFirst", type="time", nullable=true)
     */
    protected $checkFirst;

    /**
     * @Assert\NotBlank(message="Обов'язкове поле")
     * @ORM\Column(name="checkSecond", type="time", nullable=true)
     */
    protected $checkSecond;

    /**
     * @Assert\NotBlank(message="Обов'язкове поле")
     * @ORM\Column(name="checkThird", type="time", nullable=true)
     */
    protected $checkThird;

    /**
     * @ORM\OneToOne(targetEntity="Redmine\AppBundle\Entity\RedmineUser", inversedBy="settings")
     */
    protected $user;

    /**
     * @Assert\Length(max=20, maxMessage="Не більше {{ limit }} символів")
     * @Assert\Regex(pattern="/^[+]?[0-9+() -]+$/", message="Допустимі тільки цифри")
     * @ORM\Column(name="phone", type="string", length=21, nullable=true)
     */
    protected $phone;

    /**
     * Set sms
     *
     * @param boolean $sms
     * @return Settings
     */
    public function setSms($sms)
    {
        $this->sms = $sms;

        return $this;
    }

    /**
     * Get sms
     *
     * @return boolean 
     */
    public function isSms()
    {
        return $this->sms;
    }

    /**
     * Set push
     *
     * @param boolean $push
     * @return Settings
     */
    public function setPush($push)
    {
        $this->push = $push;

        return $this;
    }

    /**
     * Get push
     *
     * @return boolean 
     */
    public function isPush()
    {
        return $this->push;
    }

    /**
     * Set user
     *
     * @param RedmineUser $user
     * @return Settings
     */
    public function setUser(RedmineUser $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return RedmineUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set checkFirst
     *
     * @param \DateTime $checkFirst
     * @return Settings
     */
    public function setCheckFirst($checkFirst)
    {
        $this->checkFirst = $checkFirst;

        return $this;
    }

    /**
     * Get checkFirst
     *
     * @return \DateTime 
     */
    public function getCheckFirst()
    {
        return $this->checkFirst;
    }

    /**
     * Set checkSecond
     *
     * @param \DateTime $checkSecond
     * @return Settings
     */
    public function setCheckSecond($checkSecond)
    {
        $this->checkSecond = $checkSecond;

        return $this;
    }

    /**
     * Get checkSecond
     *
     * @return \DateTime 
     */
    public function getCheckSecond()
    {
        return $this->checkSecond;
    }

    /**
     * Set checkThird
     *
     * @param \DateTime $checkThird
     * @return Settings
     */
    public function setCheckThird($checkThird)
    {
        $this->checkThird = $checkThird;

        return $this;
    }

    /**
     * Get checkThird
     *
     * @return \DateTime 
     */
    public function getCheckThird()
    {
        return $this->checkThird;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     * @return Settings
     */
    public function setPhone($phone)
    {
        $tmp = str_replace(' ', '', str_replace('-', '', str_replace(')','', str_replace('(','', trim($phone)))));

        $this->phone = $phone ? $tmp : null;

        return $this;
    }
}
