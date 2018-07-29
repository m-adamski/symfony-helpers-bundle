<?php

namespace Adamski\Symfony\HelpersBundle\Model;

use Swift_Message;

class MailerMessage extends Swift_Message {

    /**
     * @var string
     */
    protected $defaultSenderAddress;

    /**
     * @var string
     */
    protected $defaultSenderName;

    /**
     * MailerMessage constructor.
     *
     * @param string      $defaultSenderAddress
     * @param string      $defaultSenderName
     * @param null|string $subject
     * @param null|string $body
     * @param null|string $contentType
     * @param null|string $charset
     */
    public function __construct(string $defaultSenderAddress, string $defaultSenderName, ?string $subject = null, ?string $body = null, ?string $contentType = null, ?string $charset = null) {
        parent::__construct($subject, $body, $contentType, $charset);

        $this->defaultSenderAddress = $defaultSenderAddress;
        $this->defaultSenderName = $defaultSenderName;

        parent::setFrom(
            $defaultSenderAddress, $defaultSenderName
        );
    }

    /**
     * Set provided HTML content to body of the message.
     *
     * @param string $content
     * @param null   $charset
     * @return Swift_Message
     */
    public function setHTMLBody(string $content, $charset = null) {
        return parent::setBody(
            $content, "text/html", $charset
        );
    }

}
