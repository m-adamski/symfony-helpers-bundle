<?php

namespace Adamski\Symfony\HelpersBundle\Helper;

use Swift_Mailer;
use Swift_Message;
use Swift_Attachment;
use Twig\Environment;

class MailerHelper {

    /**
     * @var Swift_Mailer
     */
    protected $swiftMailer;

    /**
     * @var Environment
     */
    protected $twigEnvironment;

    /**
     * @var string
     */
    protected $defaultSender;

    /**
     * MailerHelper constructor.
     *
     * @param Swift_Mailer $swiftMailer
     * @param Environment  $twigEnvironment
     * @param string       $defaultSender
     */
    public function __construct(Swift_Mailer $swiftMailer, Environment $twigEnvironment, string $defaultSender) {
        $this->swiftMailer = $swiftMailer;
        $this->twigEnvironment = $twigEnvironment;
        $this->defaultSender = $defaultSender;
    }

    /**
     * Send message.
     *
     * @param array       $recipients
     * @param string      $subject
     * @param string      $template
     * @param array       $data
     * @param array       $attachments
     * @param string|null $sender
     * @param array       $recipientsBCC
     * @param array       $recipientsCC
     * @return bool
     */
    public function sendMessage(array $recipients, string $subject, string $template, array $data = [],
                                array $attachments = [], string $sender = null, array $recipientsBCC = [],
                                array $recipientsCC = []
    ) {

        try {

            // Generate Swift Message
            $message = new Swift_Message($subject);
            $message->setTo($recipients)
                ->setBody($this->twigEnvironment->render($template, $data), "text/html");

            // Sender address
            $message->setFrom(
                $sender ? $sender : $this->defaultSender
            );

            // Recipients CC
            if (count($recipientsCC) > 0) {
                $message->setCc($recipientsCC);
            }

            // Recipients BCC
            if (count($recipientsBCC) > 0) {
                $message->setBcc($recipientsBCC);
            }

            // Attach specified files
            if (count($attachments) > 0) {
                foreach ($attachments as $attachment) {
                    if (file_exists($attachment)) {
                        $message->attach(Swift_Attachment::fromPath($attachment));
                    }
                }
            }

            // Send message
            return $this->swiftMailer->send($message) > 0;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
