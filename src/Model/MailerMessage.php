<?php

namespace Adamski\Symfony\HelpersBundle\Model;

use Swift_Message;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;
use Twig\Environment;

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
     * @var Environment
     */
    protected $twigEnvironment;

    /**
     * MailerMessage constructor.
     *
     * @param Environment $twigEnvironment
     * @param string      $defaultSenderAddress
     * @param string      $defaultSenderName
     * @param null|string $subject
     * @param null|string $body
     * @param null|string $contentType
     * @param null|string $charset
     */
    public function __construct(Environment $twigEnvironment, string $defaultSenderAddress, string $defaultSenderName, ?string $subject = null, ?string $body = null, ?string $contentType = null, ?string $charset = null) {
        parent::__construct($subject, $body, $contentType, $charset);

        $this->defaultSenderAddress = $defaultSenderAddress;
        $this->defaultSenderName = $defaultSenderName;
        $this->twigEnvironment = $twigEnvironment;

        parent::setSender(
            $defaultSenderAddress, $defaultSenderName
        );
    }

    /**
     * Render provided template file and set result as body of the message.
     *
     * @param string $template
     * @param array  $data
     * @param null   $charset
     * @return Swift_Message
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function setHTMLBody(string $template, array $data = [], $charset = null) {
        return parent::setBody(
            $this->twigEnvironment->render($template, $data), "text/html", $charset
        );
    }

}
