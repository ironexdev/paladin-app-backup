<?php

namespace Paladin\Model\Repository;

use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Error;
use Paladin\Enum\ResponseStatusCodeEnum;
use Paladin\Model\Document\User;

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