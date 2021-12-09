<?php

namespace PaladinBackend\Model\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use PaladinBackend\Model\Document\DocumentTrait\ActiveTrait;
use PaladinBackend\Model\Document\DocumentTrait\CreatedTrait;
use PaladinBackend\Model\Document\DocumentTrait\UpdatedTrait;

/**
 * @ODM\Document(repositoryClass="PaladinBackend\Model\Repository\AuthenticationTokenRepository")
 * @ODM\HasLifecycleCallbacks
 */
class AuthenticationToken extends AbstractDocument
{
    use ActiveTrait;
    use CreatedTrait;
    use UpdatedTrait;

    /** @ODM\Field(type="string") */
    private string $selector;

    /** @ODM\Field(type="string") */
    private string $hashedValidator;

    /** @ODM\ReferenceOne(targetDocument=User::class) */
    private User $user;

    /**
     * @return string
     */
    public function getSelector(): string
    {
        return $this->selector;
    }

    /**
     * @param string $selector
     */
    public function setSelector(string $selector): void
    {
        $this->selector = $selector;
    }

    /**
     * @return string
     */
    public function getHashedValidator(): string
    {
        return $this->hashedValidator;
    }

    /**
     * @param string $hashedValidator
     */
    public function setHashedValidator(string $hashedValidator): void
    {
        $this->hashedValidator = $hashedValidator;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}