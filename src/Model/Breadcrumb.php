<?php

namespace Adamski\Symfony\HelpersBundle\Model;

class Breadcrumb {

    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $translationDomain;

    /**
     * @var array
     */
    protected $translationParameters;

    /**
     * @var bool
     */
    protected $translate;

    /**
     * Breadcrumb constructor.
     *
     * @param string $text
     * @param string $url
     * @param string $translationDomain
     * @param array  $translationParameters
     * @param bool   $translate
     */
    public function __construct(string $text, string $url, string $translationDomain, array $translationParameters, bool $translate = true) {
        $this->text = $text;
        $this->url = $url;
        $this->translationDomain = $translationDomain;
        $this->translationParameters = $translationParameters;
        $this->translate = $translate;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getTranslationDomain() {
        return $this->translationDomain;
    }

    /**
     * @return array
     */
    public function getTranslationParameters() {
        return $this->translationParameters;
    }

    /**
     * @return bool
     */
    public function isTranslate() {
        return $this->translate;
    }
}
