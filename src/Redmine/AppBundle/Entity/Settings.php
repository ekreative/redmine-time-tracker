<?php

namespace Redmine\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="none_", type="boolean")
     */
    protected $none;

    /**
     * @ORM\Column(name="checkFirst", type="time", nullable=true)
     */
    protected $checkFirst;

    /**
     * @ORM\Column(name="checkSecond", type="time", nullable=true)
     */
    protected $checkSecond;

    /**
     * @ORM\Column(name="checkThird", type="time", nullable=true)
     */
    protected $checkThird;

    /**
     * @ORM\OneToOne(targetEntity="Redmine\AppBundle\Entity\RedmineUser", inversedBy="settings")
     */
    protected $user;

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
     * Set none
     *
     * @param boolean $none
     * @return Settings
     */
    public function setNone($none)
    {
        $this->none = $none;

        return $this;
    }

    /**
     * Get none
     *
     * @return boolean 
     */
    public function isNone()
    {
        return $this->none;
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
     * Get sms
     *
     * @return boolean 
     */
    public function getSms()
    {
        return $this->sms;
    }

    /**
     * Get push
     *
     * @return boolean 
     */
    public function getPush()
    {
        return $this->push;
    }

    /**
     * Get none
     *
     * @return boolean 
     */
    public function getNone()
    {
        return $this->none;
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
}
