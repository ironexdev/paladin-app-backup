<?php

namespace Paladin\Model\Document\DocumentTrait;

use DateTime;
use DateTimeZone;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Exception;

/* @ODM\HasLifecycleCallbacks */
trait UpdatedTrait
{
    /** @ODM\Field(type="date") */
    private DateTime $updated;

    /**
     * @ODM\PreUpdate
     * @throws Exception
     */
    public function preUpdate(LifecycleEventArgs $eventArgs): void
    {
        $this->updated = new DateTime("now", new DateTimeZone("UTC"));
    }

    /**
     * @return DateTime
     */
    public function getUpdated(): DateTime
    {
        return $this->updated;
    }
}