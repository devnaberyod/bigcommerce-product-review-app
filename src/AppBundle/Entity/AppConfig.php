<?php

namespace AppBundle\Entity;

/**
 * AppConfig
 */
class AppConfig
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $widgetTheme;

    /**
     * @var \DateTime
     */
    private $dateCreated;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set widgetTheme
     *
     * @param string $widgetTheme
     *
     * @return AppConfig
     */
    public function setWidgetTheme($widgetTheme)
    {
        $this->widgetTheme = $widgetTheme;

        return $this;
    }

    /**
     * Get widgetTheme
     *
     * @return string
     */
    public function getWidgetTheme()
    {
        return $this->widgetTheme;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return AppConfig
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
    /**
     * @var \AppBundle\Entity\User
     */
    private $user;


    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return AppConfig
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
