<?php

namespace AppBundle\Entity;

/**
 * StoreCredential
 */
class StoreCredential
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $storeHash;

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
     * Set accessToken
     *
     * @param string $accessToken
     *
     * @return StoreCredential
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get accessToken
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     *
     * @return StoreCredential
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set storeHash
     *
     * @param string $storeHash
     *
     * @return StoreCredential
     */
    public function setStoreHash($storeHash)
    {
        $this->storeHash = $storeHash;

        return $this;
    }

    /**
     * Get storeHash
     *
     * @return string
     */
    public function getStoreHash()
    {
        return $this->storeHash;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return StoreCredential
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
     * @return StoreCredential
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
    /**
     * @var string
     */
    private $scope;

    /**
     * @var string
     */
    private $context;

    /**
     * @var string
     */
    private $storeUser;


    /**
     * Set scope
     *
     * @param string $scope
     *
     * @return StoreCredential
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set context
     *
     * @param string $context
     *
     * @return StoreCredential
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set storeUser
     *
     * @param string $storeUser
     *
     * @return StoreCredential
     */
    public function setStoreUser($storeUser)
    {
        $this->storeUser = $storeUser;

        return $this;
    }

    /**
     * Get storeUser
     *
     * @return string
     */
    public function getStoreUser()
    {
        return $this->storeUser;
    }
}
