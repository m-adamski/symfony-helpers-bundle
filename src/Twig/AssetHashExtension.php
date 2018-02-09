<?php

namespace Adamski\Symfony\HelpersBundle\Twig;

use Adamski\Symfony\HelpersBundle\Helper\DirectoryHelper;
use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetHashExtension extends AbstractExtension {

    /**
     * @var Packages
     */
    protected $packages;

    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;

    /**
     * AssetHashExtension constructor.
     *
     * @param Packages        $packages
     * @param DirectoryHelper $directoryHelper
     */
    public function __construct(Packages $packages, DirectoryHelper $directoryHelper) {
        $this->packages = $packages;
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
                return sprintf($format, $this->packages->getUrl($value), md5_file($filePath));
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
            ], true)
        );
    }
}
