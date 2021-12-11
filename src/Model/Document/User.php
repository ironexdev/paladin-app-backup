<?php

namespace Paladin\Model\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use Paladin\Model\Document\DocumentTrait\ActiveTrait;
use Paladin\Model\Document\DocumentTrait\CreatedTrait;
use Paladin\Model\Document\DocumentTrait\UpdatedTrait;

/**
 * @ODM\Document(repositoryClass="Paladin\Model\Repository\UserRepository")
 * @ODM\HasLifecycleCallbacks
 */
#[Type]
class User extends AbstractDocument
{
    use ActiveTrait;
    use CreatedTrait;
    use UpdatedTrait;

    /** @ODM\Field(type="string") */
    #[Field]
    private string $firstName;

    /** @ODM\Field(type="string") */
    #[Field]
    private string $lastName;

    /**
     * @ODM\Field(type="string")
     * @ODM\UniqueIndex(order="asc")
     */
    #[Field]
    private string $nickname;

    /**
     * @ODM\Field(type="string")
     * @ODM\UniqueIndex(order="asc")
     */
    #[Field]
    private string $email;

    /** @ODM\Field(type="string") */
    #[Field]
    private string $password;

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
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */

    public function getNickname(): string
    {
        return $this->nickname;
    }

    /**
     * @param string $nickname
     */
    public function setNickName(string $nickname): void
    {
        $this->nickname = $nickname;
    }
}