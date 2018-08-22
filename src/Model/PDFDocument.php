<?php

namespace Adamski\Symfony\HelpersBundle\Model;

use Adamski\Symfony\HelpersBundle\Helper\DirectoryHelper;
use Symfony\Component\HttpFoundation\Response;
use TCPDF_FONTS;
use Twig\Environment;

class PDFDocument {

    /**
     * @var PDFGenerator
     */
    protected $pdfGenerator;

    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;

    /**
     * @var Environment
     */
    protected $twigEnvironment;

    /**
     * @var string
     */
    protected $creator = "";

    /**
     * @var string
     */
    protected $author = "";

    /**
     * @var string
     */
    protected $title = "";

    /**
     * @var string
     */
    protected $subject = "";

    /**
     * @var array
     */
    protected $keywords = [];

    /**
     * PDFDocument constructor.
     *
     * @param DirectoryHelper $directoryHelper
     * @param Environment     $twigEnvironment
     * @param string          $orientation
     * @param string          $unit
     * @param string          $format
     * @param bool            $unicode
     * @param string          $encoding
     * @param int             $fontSize
     */
    public function __construct(DirectoryHelper $directoryHelper, Environment $twigEnvironment, string $orientation = "P", string $unit = "mm", string $format = "A4", bool $unicode = true, string $encoding = "UTF-8", int $fontSize = 10) {
        $this->directoryHelper = $directoryHelper;
        $this->twigEnvironment = $twigEnvironment;

        // Include config
        $this->includeConfig();

        // Add custom fonts
        $this->registerCustomFont();

        // Create new TCPDF instance and set default options
        $this->pdfGenerator = new PDFGenerator($orientation, $unit, $format, $unicode, $encoding);
        $this->pdfGenerator->setPrintHeader(false);
        $this->pdfGenerator->setPrintFooter(false);
        $this->pdfGenerator->SetAuthor($this->author);
        $this->pdfGenerator->SetTitle($this->title);
        $this->pdfGenerator->SetCreator($this->creator);
        $this->pdfGenerator->SetSubject($this->subject);
        $this->pdfGenerator->SetKeywords(implode(", ", $this->keywords));
        $this->pdfGenerator->setFontSubsetting(true);
        $this->pdfGenerator->SetFont($this->getDefaultFont(), "N", $fontSize);
    }

    /**
     * Write HTML into document.
     *
     * @param string $content
     * @param bool   $newPage
     */
    public function writeHTML(string $content, bool $newPage = false) {
        if ($this->pdfGenerator->getNumPages() <= 0 || $newPage) {
            $this->pdfGenerator->AddPage();
        }

        $this->pdfGenerator->writeHTML($content, true, false, true, false);
    }

    /**
     * Render specified template and write generated HTML into document.
     *
     * @param string $template
     * @param array  $data
     * @return bool
     */
    public function renderHTML(string $template, array $data = []) {
        try {
            $this->writeHTML(
                $this->twigEnvironment->render($template, $data)
            );

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Print footer.
     *
     * @param string $content
     * @param bool   $printContent
     * @param bool   $printPages
     * @param string $pagesPrefix
     * @param string $pagesPostfix
     * @param string $contentAlign
     * @param string $pagesAlign
     */
    public function setFooter(string $content, bool $printContent = true, bool $printPages = true, string $pagesPrefix = "", string $pagesPostfix = "", string $contentAlign = "L", string $pagesAlign = "R") {

        $this->pdfGenerator->setPrintFooter(true);
        $this->pdfGenerator->setFooterFunction(function (PDFGenerator $instance) use ($content, $printContent, $printPages, $pagesPrefix, $pagesPostfix, $contentAlign, $pagesAlign) {

            // Set footer Y position & font
            $instance->SetY(-15);
            $instance->SetFont($this->getDefaultFont(), "N", 8);

            // Define variables with width
            $contentCellWidth = $printContent ? ($printPages ? 173 : 203) : 0;
            $pagesCellWidth = $printContent ? ($printPages ? 30 : 0) : 203;

            if ($printContent) {
                $instance->Cell($contentCellWidth, 10, $content, 0, false, $contentAlign);
            }

            if ($printPages) {
                $instance->Cell($pagesCellWidth, 10, $pagesPrefix . $instance->getAliasNumPage() . "/" . $instance->getAliasNbPages() . $pagesPostfix, 0, false, $pagesAlign);
            }
        });
    }

    /**
     * Set font.
     *
     * @param string   $family
     * @param string   $style
     * @param int|null $size
     */
    public function setFont(string $family, string $style = "N", ?int $size = null) {
        $this->pdfGenerator->SetFont($family, $style, $size);
    }

    /**
     * Save PDF document in specified path.
     *
     * @param string $path
     * @return string
     */
    public function save(string $path) {
        if (!preg_match("/\.pdf$/", $path)) {
            $path = $path . ".pdf";
        }

        return $this->pdfGenerator->Output($path, "F");
    }

    /**
     * Open in Browser generated PDF document.
     *
     * @param string $name
     * @return Response
     */
    public function output(string $name) {
        if (!preg_match("/\.pdf$/", $name)) {
            $name = $name . ".pdf";
        }

        $response = new Response(
            $this->pdfGenerator->Output($name)
        );

        $response->headers->set("Content-Type", "application/pdf");
        $response->headers->set("Content-Disposition", "attachment; filename=\"" . $name . "\"");

        return $response;
    }

    /**
     * Get document data.
     *
     * @return string
     */
    public function getPDFData() {
        return $this->pdfGenerator->getPDFData();
    }

    /**
     * @return string
     */
    public function getCreator() {
        return $this->creator;
    }

    /**
     * @param string $creator
     */
    public function setCreator(string $creator) {
        $this->creator = $creator;
        $this->pdfGenerator->SetCreator($creator);
    }

    /**
     * @return string
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author) {
        $this->author = $author;
        $this->pdfGenerator->SetAuthor($author);
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title) {
        $this->title = $title;
        $this->pdfGenerator->SetTitle($title);
    }

    /**
     * @return string
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject) {
        $this->subject = $subject;
        $this->pdfGenerator->SetSubject($subject);
    }

    /**
     * @return array
     */
    public function getKeywords() {
        return $this->keywords;
    }

    /**
     * @param array $keywords
     */
    public function setKeywords(array $keywords) {
        $this->keywords = $keywords;
        $this->pdfGenerator->SetKeywords(implode(", ", $keywords));
    }

    /**
     * @param mixed $keyword
     */
    public function addKeyword($keyword) {
        $this->keywords[] = $keyword;
        $this->pdfGenerator->SetKeywords(implode(", ", $this->keywords));
    }

    /**
     * Register new custom fonts.
     */
    protected function registerCustomFont() {

        // Define custom font paths
        $customFontPath = $this->directoryHelper->generatePath([
            $this->directoryHelper->getLibrariesDirectory(), "pdf-generator", "fonts", "Lato2OFL"
        ], false);

        // Register new font with TCPDF Font tool
        TCPDF_FONTS::addTTFfont($customFontPath . "Lato-Regular.ttf", "TrueTypeUnicode", "");
        TCPDF_FONTS::addTTFfont($customFontPath . "Lato-Bold.ttf", "TrueTypeUnicode", "");
        TCPDF_FONTS::addTTFfont($customFontPath . "Lato-BoldItalic.ttf", "TrueTypeUnicode", "");
        TCPDF_FONTS::addTTFfont($customFontPath . "Lato-Italic.ttf", "TrueTypeUnicode", "");
    }

    /**
     * Return name of custom default font.
     *
     * @return string
     */
    protected function getDefaultFont() {
        return "Lato";
    }

    /**
     * Include custom Config file
     */
    protected function includeConfig() {

        // Overwrite definitions
        if (!defined("K_TCPDF_EXTERNAL_CONFIG")) {
            define("K_TCPDF_EXTERNAL_CONFIG", true);
        }

        /**
         * Installation path (/var/www/tcpdf/).
         * By default it is automatically calculated but you can also set it as a fixed string to improve performances.
         */
        // define ("K_PATH_MAIN", "");

        /**
         * URL path to tcpdf installation folder (http://localhost/tcpdf/).
         * By default it is automatically set but you can also set it as a fixed string to improve performances.
         */
        // define ("K_PATH_URL", "");

        /**
         * Path for PDF fonts.
         * By default it is automatically set but you can also set it as a fixed string to improve performances.
         */
        if (!defined("K_PATH_FONTS")) {
            define("K_PATH_FONTS", $this->directoryHelper->generatePath([
                $this->directoryHelper->getLibrariesDirectory(), "pdf-generator", "fonts"
            ]));
        }

        /**
         * Default images directory.
         * By default it is automatically set but you can also set it as a fixed string to improve performances.
         */
        // define ("K_PATH_IMAGES", "");

        /**
         * Default image logo used be the default Header() method.
         * Please set here your own logo or an empty string to disable it.
         */
        // define ("PDF_HEADER_LOGO", "");

        /**
         * Header logo image width in user units.
         */
        // define ("PDF_HEADER_LOGO_WIDTH", 0);

        /**
         * Cache directory for temporary files (full path).
         */
        // define ("K_PATH_CACHE", "/tmp/");

        /**
         * Generic name for a blank image.
         */
        if (!defined("K_BLANK_IMAGE")) {
            define("K_BLANK_IMAGE", "_blank.png");
        }

        /**
         * Page format.
         */
        if (!defined("PDF_PAGE_FORMAT")) {
            define("PDF_PAGE_FORMAT", "A4");
        }

        /**
         * Page orientation (P=portrait, L=landscape).
         */
        if (!defined("PDF_PAGE_ORIENTATION")) {
            define("PDF_PAGE_ORIENTATION", "P");
        }

        /**
         * Document creator.
         */
        if (!defined("PDF_CREATOR")) {
            define("PDF_CREATOR", "");
        }

        /**
         * Document author.
         */
        if (!defined("PDF_AUTHOR")) {
            define("PDF_AUTHOR", "");
        }

        /**
         * Header title.
         */
        if (!defined("PDF_HEADER_TITLE")) {
            define("PDF_HEADER_TITLE", "");
        }

        /**
         * Header description string.
         */
        if (!defined("PDF_HEADER_STRING")) {
            define("PDF_HEADER_STRING", "");
        }

        /**
         * Document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch].
         */
        if (!defined("PDF_UNIT")) {
            define("PDF_UNIT", "mm");
        }

        /**
         * Header margin.
         */
        if (!defined("PDF_MARGIN_HEADER")) {
            define("PDF_MARGIN_HEADER", 5);
        }

        /**
         * Footer margin.
         */
        if (!defined("PDF_MARGIN_FOOTER")) {
            define("PDF_MARGIN_FOOTER", 10);
        }

        /**
         * Top margin.
         */
        if (!defined("PDF_MARGIN_TOP")) {
            define("PDF_MARGIN_TOP", 27);
        }

        /**
         * Bottom margin.
         */
        if (!defined("PDF_MARGIN_BOTTOM")) {
            define("PDF_MARGIN_BOTTOM", 25);
        }

        /**
         * Left margin.
         */
        if (!defined("PDF_MARGIN_LEFT")) {
            define("PDF_MARGIN_LEFT", 15);
        }

        /**
         * Right margin.
         */
        if (!defined("PDF_MARGIN_RIGHT")) {
            define("PDF_MARGIN_RIGHT", 15);
        }

        /**
         * Default main font name.
         */
        if (!defined("PDF_FONT_NAME_MAIN")) {
            define("PDF_FONT_NAME_MAIN", "Lato");
        }

        /**
         * Default main font size.
         */
        if (!defined("PDF_FONT_SIZE_MAIN")) {
            define("PDF_FONT_SIZE_MAIN", 10);
        }

        /**
         * Default data font name.
         */
        if (!defined("PDF_FONT_NAME_DATA")) {
            define("PDF_FONT_NAME_DATA", "Lato");
        }

        /**
         * Default data font size.
         */
        if (!defined("PDF_FONT_SIZE_DATA")) {
            define("PDF_FONT_SIZE_DATA", 8);
        }

        /**
         * Default monospaced font name.
         */
        if (!defined("PDF_FONT_MONOSPACED")) {
            define("PDF_FONT_MONOSPACED", "Courier");
        }

        /**
         * Ratio used to adjust the conversion of pixels to user units.
         */
        if (!defined("PDF_IMAGE_SCALE_RATIO")) {
            define("PDF_IMAGE_SCALE_RATIO", 1.25);
        }

        /**
         * Magnification factor for titles.
         */
        if (!defined("HEAD_MAGNIFICATION")) {
            define("HEAD_MAGNIFICATION", 1.1);
        }

        /**
         * Height of cell respect font height.
         */
        if (!defined("K_CELL_HEIGHT_RATIO")) {
            define("K_CELL_HEIGHT_RATIO", 1.25);
        }

        /**
         * Title magnification respect main font size.
         */
        if (!defined("K_TITLE_MAGNIFICATION")) {
            define("K_TITLE_MAGNIFICATION", 1.3);
        }

        /**
         * Reduction factor for small font.
         */
        if (!defined("K_SMALL_RATIO")) {
            define("K_SMALL_RATIO", 2 / 3);
        }

        /**
         * Set to true to enable the special procedure used to avoid the overlappind of symbols on Thai language.
         */
        if (!defined("K_THAI_TOPCHARS")) {
            define("K_THAI_TOPCHARS", true);
        }

        /**
         * If true allows to call TCPDF methods using HTML syntax
         * IMPORTANT: For security reason, disable this feature if you are printing user HTML content.
         */
        if (!defined("K_TCPDF_CALLS_IN_HTML")) {
            define("K_TCPDF_CALLS_IN_HTML", true);
        }

        /**
         * If true and PHP version is greater than 5, then the Error() method throw new exception instead of terminating the execution.
         */
        if (!defined("K_TCPDF_THROW_EXCEPTION_ERROR")) {
            define("K_TCPDF_THROW_EXCEPTION_ERROR", false);
        }

        /**
         * Default timezone for datetime functions
         */
        if (!defined("K_TIMEZONE")) {
            define("K_TIMEZONE", "UTC");
        }
    }
}
