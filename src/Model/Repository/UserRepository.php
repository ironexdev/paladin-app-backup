<?php

namespace PaladinBackend\Model\Repository;

use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Error;
use PaladinBackend\Enum\ResponseStatusCodeEnum;
use PaladinBackend\Model\Document\User;

class UserRepository extends DocumentRepository
{
    /**
     * @param User $user
     * @return bool
     */
    public function isUnique(User $user): bool
    {
        try {
            $result = $this->dm->createQueryBuilder(User::class)
                ->field("nickname")
                ->equals($user->getNickname())
                ->field("email")
                ->equals($user->getEmail())
                ->count()
                ->getQuery()
                ->execute();
        } catch (MongoDBException $e) {
            throw new Error($e->getMessage(), ResponseStatusCodeEnum::INTERNAL_SERVER_ERROR, $e);
        }

        return !$result;
    }
}