<?php

namespace Paladin\Model\Document\DocumentTrait;

use DateTime;
use DateTimeZone;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Exception;

/* @ODM\HasLifecycleCallbacks */
trait CreatedTrait
{
    /** @ODM\Field(type="date") */
    protected DateTime $created;

    /**
     * @ODM\PrePersist
     * @throws Exception
     */
    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $this->created = new DateTime("now", new DateTimeZone("UTC"));
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }
}