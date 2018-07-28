<?php

namespace Adamski\Symfony\HelpersBundle\Helper;

use Adamski\Symfony\HelpersBundle\Model\MailerMessage;
use Swift_Mailer;
use Twig\Environment;

class MailerHelper {

    /**
     * @var string
     */
    protected $defaultSenderAddress;

    /**
     * @var string
     */
    protected $defaultSenderName;

    /**
     * @var Swift_Mailer
     */
    protected $swiftMailer;

    /**
     * @var Environment
     */
    protected $twigEnvironment;

    /**
     * MailerHelper constructor.
     *
     * @param string       $defaultSenderAddress
     * @param string       $defaultSenderName
     * @param Swift_Mailer $swiftMailer
     * @param Environment  $twigEnvironment
     */
    public function __construct(string $defaultSenderAddress, string $defaultSenderName, Swift_Mailer $swiftMailer, Environment $twigEnvironment) {
        $this->defaultSenderAddress = $defaultSenderAddress;
        $this->defaultSenderName = $defaultSenderName;
        $this->swiftMailer = $swiftMailer;
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * Generate new instance of Mailer Message.
     *
     * @param null|string $subject
     * @param null|string $body
     * @param null|string $contentType
     * @param null|string $charset
     * @return MailerMessage
     */
    public function buildMessage(?string $subject = null, ?string $body = null, ?string $contentType = null, ?string $charset = null) {
        return new MailerMessage(
            $this->twigEnvironment, $this->defaultSenderAddress, $this->defaultSenderName, $subject, $body, $contentType, $charset
        );
    }

    /**
     * Send provided message.
     * Function return the number of successful recipients. Can be zero which indicates failure.
     *
     * @param MailerMessage $mailerMessage
     * @param array|null    $failedRecipients
     * @return int
     */
    public function sendMessage(MailerMessage $mailerMessage, ?array &$failedRecipients = null) {
        return $this->swiftMailer->send($mailerMessage, $failedRecipients);
    }
}
