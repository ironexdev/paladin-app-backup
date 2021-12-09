<?php

namespace PaladinBackend\Mailer;

use Swift_Events_EventListener;
use Swift_Mime_SimpleMessage;

interface MailerInterface
{
    /**
     * @param string $service
     * @return mixed
     */
    public function createMessage($service = "message");

    /**
     * @param Swift_Mime_SimpleMessage $message
     * @param null $failedRecipients
     * @return mixed
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null);

    /**
     * @param Swift_Events_EventListener $plugin
     * @return mixed
     */
    public function registerPlugin(Swift_Events_EventListener $plugin);

    /**
     * @return mixed
     */
    public function getTransport();
}