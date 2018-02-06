<?php

namespace Adamski\Symfony\HelpersBundle\Helper;

use Adamski\Symfony\HelpersBundle\Model\PDFDocument;
use Twig\Environment;

class PDFHelper {

    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;

    /**
     * @var Environment
     */
    protected $twigEnvironment;

    /**
     * PDFHelper constructor.
     *
     * @param DirectoryHelper $directoryHelper
     * @param Environment     $twigEnvironment
     */
    public function __construct(DirectoryHelper $directoryHelper, Environment $twigEnvironment) {
        $this->directoryHelper = $directoryHelper;
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * Create new PDF Document.
     *
     * @param string $orientation
     * @param string $unit
     * @param string $format
     * @param bool   $unicode
     * @param string $encoding
     * @param int    $fontSize
     * @return PDFDocument
     */
    public function initDocument(string $orientation = "P", string $unit = "mm", string $format = "A4", bool $unicode = true, string $encoding = "UTF-8", int $fontSize = 10) {
        return new PDFDocument($this->directoryHelper, $this->twigEnvironment, $orientation, $unit, $format, $unicode, $encoding, $fontSize);
    }
}
