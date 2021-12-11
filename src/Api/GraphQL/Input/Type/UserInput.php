<?php

namespace Paladin\Api\GraphQL\Input\Type;

use Symfony\Component\Validator\Constraints as Assert;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Input;
use Paladin\Enum\TranslatorEnum;

#[Input]
class UserInput extends AbstractInput
{
    #[Assert\Length(
        min: 2,
        max: 25,
        minMessage: TranslatorEnum::STRING_MIN_LENGTH,
        maxMessage: TranslatorEnum::STRING_MAX_LENGTH
    )]
    #[Field]
    private string $firstName;

    #[Assert\Length(
        min: 1,
        max: 25,
        minMessage: TranslatorEnum::STRING_MIN_LENGTH,
        maxMessage: TranslatorEnum::STRING_MAX_LENGTH
    )]
    #[Field]
    private string $lastName;

    #[Assert\Length(
        min: 2,
        max: 12,
        minMessage: TranslatorEnum::STRING_MIN_LENGTH,
        maxMessage: TranslatorEnum::STRING_MAX_LENGTH
    )]
    #[Field]
    private string $nickname;

    #[Assert\NotBlank]
    #[Assert\Email(
        message: TranslatorEnum::INVALID_EMAIL_FORMAT
    )]
    #[Field]
    private string $email;

    #[Assert\Length(
        min: 8,
        max: 128,
        minMessage: TranslatorEnum::STRING_MIN_LENGTH,
        maxMessage: TranslatorEnum::STRING_MAX_LENGTH
    )]
    #[Assert\Regex(
        pattern: "/^\S*(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/",
        message: TranslatorEnum::INVALID_PASSWORD_FORMAT
    )]
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