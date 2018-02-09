<?php

namespace Adamski\Symfony\HelpersBundle\Twig;

use Adamski\Symfony\HelpersBundle\Helper\DirectoryHelper;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetHashExtension extends AbstractExtension {

    /**
     * @var AssetsHelper
     */
    protected $assetsHelper;

    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;

    /**
     * AssetHashExtension constructor.
     *
     * @param AssetsHelper    $assetsHelper
     * @param DirectoryHelper $directoryHelper
     */
    public function __construct(AssetsHelper $assetsHelper, DirectoryHelper $directoryHelper) {
        $this->assetsHelper = $assetsHelper;
        $this->directoryHelper = $directoryHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return [
            new TwigFunction("asset_hash", [$this, "assetHash"])
        ];
    }

    /**
     * Generate asset URL with file MD5 hash.
     *
     * @param string $value
     * @param string $format
     * @return string
     */
    public function assetHash(string $value, string $format = "%s?%s") {

        if (!$this->isURL($value)) {
            if ($filePath = $this->getRealPath($value)) {
                return sprintf($format, $this->assetsHelper->getUrl($value), md5_file($filePath));
            }
        }

        return $value;
    }

    /**
     * @param string $value
     * @return bool
     */
    private function isURL(string $value) {
        return (bool)preg_match("/^(http(s)?|ftp)/", $value);
    }

    /**
     * @param string $value
     * @return bool|string
     */
    private function getRealPath(string $value) {
        return $this->directoryHelper->getRealpath(
            $this->directoryHelper->generatePath([
                $this->directoryHelper->getPublicDirectory(), $value
            ])
        );
    }
}
