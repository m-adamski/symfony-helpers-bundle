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
     * Register new custom font and return it name.
     *
     * @return string
     */
    protected function getDefaultFont() {

        // Define custom font paths
        $customFontPath = $this->directoryHelper->generatePath([
            $this->directoryHelper->getLibrariesDirectory(), "pdf-generator", "fonts", "Lato2OFL", "LatoRegular.ttf"
        ], true);

        // Register new font with TCPDF Font tool
        return TCPDF_FONTS::addTTFfont($customFontPath, "TrueTypeUnicode", "");
    }

    /**
     * Include custom Config file
     */
    protected function includeConfig() {

        // Overwrite definitions
        define("K_TCPDF_EXTERNAL_CONFIG", true);

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
        define("K_PATH_FONTS", $this->directoryHelper->generatePath([
            $this->directoryHelper->getLibrariesDirectory(), "pdf-generator", "fonts"
        ]));

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
        define("K_BLANK_IMAGE", "_blank.png");

        /**
         * Page format.
         */
        define("PDF_PAGE_FORMAT", "A4");

        /**
         * Page orientation (P=portrait, L=landscape).
         */
        define("PDF_PAGE_ORIENTATION", "P");

        /**
         * Document creator.
         */
        define("PDF_CREATOR", "");

        /**
         * Document author.
         */
        define("PDF_AUTHOR", "");

        /**
         * Header title.
         */
        define("PDF_HEADER_TITLE", "");

        /**
         * Header description string.
         */
        define("PDF_HEADER_STRING", "");

        /**
         * Document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch].
         */
        define("PDF_UNIT", "mm");

        /**
         * Header margin.
         */
        define("PDF_MARGIN_HEADER", 5);

        /**
         * Footer margin.
         */
        define("PDF_MARGIN_FOOTER", 10);

        /**
         * Top margin.
         */
        define("PDF_MARGIN_TOP", 27);

        /**
         * Bottom margin.
         */
        define("PDF_MARGIN_BOTTOM", 25);

        /**
         * Left margin.
         */
        define("PDF_MARGIN_LEFT", 15);

        /**
         * Right margin.
         */
        define("PDF_MARGIN_RIGHT", 15);

        /**
         * Default main font name.
         */
        define("PDF_FONT_NAME_MAIN", "lato");

        /**
         * Default main font size.
         */
        define("PDF_FONT_SIZE_MAIN", 10);

        /**
         * Default data font name.
         */
        define("PDF_FONT_NAME_DATA", "lato");

        /**
         * Default data font size.
         */
        define("PDF_FONT_SIZE_DATA", 8);

        /**
         * Default monospaced font name.
         */
        define("PDF_FONT_MONOSPACED", "courier");

        /**
         * Ratio used to adjust the conversion of pixels to user units.
         */
        define("PDF_IMAGE_SCALE_RATIO", 1.25);

        /**
         * Magnification factor for titles.
         */
        define("HEAD_MAGNIFICATION", 1.1);

        /**
         * Height of cell respect font height.
         */
        define("K_CELL_HEIGHT_RATIO", 1.25);

        /**
         * Title magnification respect main font size.
         */
        define("K_TITLE_MAGNIFICATION", 1.3);

        /**
         * Reduction factor for small font.
         */
        define("K_SMALL_RATIO", 2 / 3);

        /**
         * Set to true to enable the special procedure used to avoid the overlappind of symbols on Thai language.
         */
        define("K_THAI_TOPCHARS", true);

        /**
         * If true allows to call TCPDF methods using HTML syntax
         * IMPORTANT: For security reason, disable this feature if you are printing user HTML content.
         */
        define("K_TCPDF_CALLS_IN_HTML", true);

        /**
         * If true and PHP version is greater than 5, then the Error() method throw new exception instead of terminating the execution.
         */
        define("K_TCPDF_THROW_EXCEPTION_ERROR", false);

        /**
         * Default timezone for datetime functions
         */
        define("K_TIMEZONE", "UTC");
    }
}
