<?php

namespace Redmine\AppBundle\Entity\DTO;

use DateTime;

class Date
{
    protected $filterDate;

    /**
     * @return DateTime
     */
    public function getFilterDate()
    {
        return $this->filterDate;
    }

    /**
     * @param DateTime $filterDate
     * @return Date
     */
    public function setFilterDate(DateTime $filterDate)
    {
        $this->filterDate = $filterDate;

        return $this;
    }
}
