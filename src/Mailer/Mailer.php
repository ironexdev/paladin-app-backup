<?php declare(strict_types=1);

namespace PaladinBackend\Mailer;

use Swift_Mailer;
use Swift_SmtpTransport;

class Mailer extends Swift_Mailer implements MailerInterface
{
    /**
     * @param Swift_SmtpTransport $transport
     * @param string $user
     * @param string $password
     */
    public function __construct(Swift_SmtpTransport $transport, string $user, string $password)
    {
        $transport->setUsername($user);
        $transport->setPassword($password);

        parent::__construct($transport);
    }
}