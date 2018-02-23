<?php

namespace Test\Model;

use DomainEventFactory\Event\EventObjectInterface;

/**
 * Class User
 * @package Test\Model
 * @DomainEventFactory user_created, user_updated
 * @EventPayloadName Payload
 */
class ConfigurableAnnotationNameUser implements EventObjectInterface
{
    /**
     * @var string $email
     * @Payload user_created, user_updated
     */
    private $email;
    /**
     * @var string $username
     * @Payload user_created, user_updated
     */
    private $username;
    /**
     * @var string $name
     * @Payload user_created, user_updated
     */
    private $name;
    /**
     * @var string $lastname
     * @Payload user_created, user_updated
     */
    private $lastname;
    /**
     * @var \DateTime $createdAt
     * @Payload user_created, user_updated
     */
    private $createdAt;
    /**
     * @var \DateTime $updatedAt
     * @Payload user_updated
     */
    private $updatedAt;
    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }
    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }
    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
    }
    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}