<?php

namespace Paladin\Security;

use Error;
use Exception;
use Firebase\JWT\JWT;
use Paladin\Enum\ResponseStatusCodeEnum;

class SecurityService implements SecurityServiceInterface
{
    /**
     * @param string $token
     * @param string $algorithm
     * @return object
     */
    public function decodeJWT(string $token, string $algorithm = "HS256"): object
    {
        return JWT::decode($token, $_ENV["JWT_KEY"], [$algorithm]);
    }

    /**
     * @param array $payload
     * @param string $algorithm
     * @return string
     */
    public function encodeJWT(array $payload, string $algorithm = "HS256"): string
    {
        return JWT::encode($payload, $_ENV["JWT_KEY"], $algorithm);
    }

    /**
     * @param string $password
     * @param string|int|null $algo
     * @param array $options
     * @return string
     */
    public function passwordHash(string $password, string|int|null $algo = PASSWORD_DEFAULT, array $options = []): string
    {
        $result = password_hash($password, $algo, $options);

        if($result === false)
        {
            throw new Error("Function password_hash function has returned false value.", ResponseStatusCodeEnum::INTERNAL_SERVER_ERROR);
        }
        else if($result === null)
        {
            throw new Error("Function password_hash function has returned null value.", ResponseStatusCodeEnum::INTERNAL_SERVER_ERROR);
        }

        return $result;
    }

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function passwordVerify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * @param string $algorithm
     * @param string $data
     * @param bool $binary
     * @return string
     */
    public function hash(string $algorithm, string $data, bool $binary = false): string
    {
        $result = hash($algorithm, $data, $binary);

        if($result === false)
        {
            throw new Error("Function hash has returned false value.", ResponseStatusCodeEnum::INTERNAL_SERVER_ERROR);
        }

        return $result;
    }

    /**
     * @param string $knownString
     * @param string $userString
     * @return bool
     */
    public function hashEquals(string $knownString, string $userString): bool
    {
        return hash_equals($knownString, $userString);
    }

    /**
     * @param int $length
     * @return string
     */
    public function randomBytes(int $length): string
    {
        try {
            return random_bytes($length);
        } catch (Exception $e) {
            throw new Error($e->getMessage(), ResponseStatusCodeEnum::INTERNAL_SERVER_ERROR, $e);
        }
    }

    /**
     * @param string $string
     * @return string
     */
    public function bin2hex(string $string): string
    {
        return bin2hex($string);
    }

    /**
     * @return string
     */
    public function csrfToken(): string
    {
        return $this->bin2hex($this->randomBytes(32));
    }
}