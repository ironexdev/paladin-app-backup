<?php

namespace Paladin\Model\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ODM\MappedSuperclass
 */
abstract class AbstractDocument
{
    /** @ODM\Id */
    protected string $id;

    /**
     * @return string
     */
     #[Field(outputType: "ID")]
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    abstract public function getCreated(): DateTime;

    /**
     * @return DateTime
     */
    abstract public function getUpdated(): DateTime;
}