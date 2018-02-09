<?php

namespace Adamski\Symfony\HelpersBundle\Helper;

use Cake\Chronos\Chronos;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

class DirectoryHelper extends Filesystem {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @var string
     */
    protected $projectDirectory;

    /**
     * @var string
     */
    protected $sourceDirectory;

    /**
     * @var string
     */
    protected $cacheDirectory;

    /**
     * @var string
     */
    protected $logsDirectory;

    /**
     * @var string
     */
    protected $librariesDirectory;

    /**
     * @var string
     */
    protected $temporaryDirectory;

    /**
     * @var string
     */
    protected $publicDirectory;

    /**
     * @var string
     */
    protected $librariesDirectoryName = "libraries";

    /**
     * @var string
     */
    protected $temporaryDirectoryName = "temp";

    /**
     * @var string
     */
    protected $publicDirectoryName = "public";

    /**
     * DirectoryHelper constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->finder = new Finder();

        $this->projectDirectory = $this->container->getParameter("kernel.project_dir");
        $this->sourceDirectory = $this->container->getParameter("kernel.root_dir");
        $this->cacheDirectory = $this->container->getParameter("kernel.cache_dir");
        $this->logsDirectory = $this->container->getParameter("kernel.logs_dir");
        $this->librariesDirectory = $this->projectDirectory . DIRECTORY_SEPARATOR . $this->librariesDirectoryName;
        $this->temporaryDirectory = $this->projectDirectory . DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . $this->temporaryDirectoryName;
        $this->publicDirectory = $this->projectDirectory . DIRECTORY_SEPARATOR . $this->publicDirectoryName;
    }

    /**
     * Get Kernel Project directory.
     *
     * @return string
     */
    public function getProjectDirectory() {
        return $this->projectDirectory;
    }

    /**
     * Get Kernel Source directory.
     *
     * @return string
     */
    public function getSourceDirectory() {
        return $this->sourceDirectory;
    }

    /**
     * Get Kernel Cache directory.
     *
     * @return string
     */
    public function getCacheDirectory() {
        return $this->cacheDirectory;
    }

    /**
     * Get Kernel Logs directory.
     *
     * @return string
     */
    public function getLogsDirectory() {
        return $this->logsDirectory;
    }

    /**
     * @return string
     */
    public function getLibrariesDirectory() {
        return $this->librariesDirectory;
    }

    /**
     * @return string
     */
    public function getPublicDirectory() {
        return $this->publicDirectory;
    }

    /**
     * @return Finder
     */
    public function getFinder() {
        return $this->finder;
    }

    /**
     * Get Kernel Temporary directory.
     *
     * @param string $childDirectory
     * @return string
     */
    public function getTemporaryDirectory(string $childDirectory = "") {

        // Generate temporary directory path
        $directoryPath = !empty($childDirectory) ? $this->temporaryDirectory . DIRECTORY_SEPARATOR . $childDirectory : $this->temporaryDirectory;

        // Create temporary directory if not exist
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0775, true);
        }

        return $directoryPath;
    }

    /**
     * Generate path based on specified parts.
     *
     * @param array  $parts
     * @param bool   $trim
     * @param string $separator
     * @return string
     */
    public function generatePath(array $parts, bool $trim = false, string $separator = DIRECTORY_SEPARATOR) {
        $generatedPath = implode($separator, $parts);

        return $trim ? $generatedPath : $generatedPath . $separator;
    }

    /**
     * Create directory with specified path.
     *
     * @param string $path
     * @param bool   $recursive
     * @return string
     */
    public function createDirectory(string $path, bool $recursive = true) {
        if (!file_exists($path)) {
            mkdir($path, 0775, $recursive);
        }

        return $path;
    }

    /**
     * Generate temporary file with unique name.
     *
     * @param string $childDirectory
     * @param string $prefix
     * @return null|string
     */
    public function createTemporaryFile(string $childDirectory = "", string $prefix = "") {

        // Generate directory
        $fileDirectory = $this->getTemporaryDirectory($childDirectory);

        // Generate unique file name
        do {
            $fileName = uniqid($prefix);
            $filePath = $fileDirectory . DIRECTORY_SEPARATOR . $fileName;
        } while (file_exists($filePath));

        // Create file
        if ($fileHandler = fopen($filePath, "w")) {
            fclose($fileHandler);

            // Return generated file path
            return $filePath;
        }

        // It should never happened, but for sure return null
        return null;
    }

    /**
     * Write content into specified file.
     *
     * @param string $filePath
     * @param string $content
     * @return bool
     */
    public function writeFile(string $filePath, string $content) {
        return file_put_contents($filePath, $content) !== false;
    }

    /**
     * Get array of directory scan result.
     *
     * @param string $directoryPath
     * @param bool   $recursive
     * @param bool   $hideDirectories
     * @param bool   $parseObject
     * @param bool   $separate
     * @return array
     */
    public function scanDirectory(string $directoryPath, bool $recursive = true, bool $hideDirectories = true, bool $parseObject = true, bool $separate = false) {

        // Check if specified directory exist
        if (file_exists($directoryPath) && is_dir($directoryPath)) {

            // Scan specified directory with core function
            $scanResult = scandir($directoryPath);

            // Define response array
            $directoriesArray = [];
            $filesArray = [];

            // Move every file and fill response array
            foreach ($scanResult as $item) {
                if ($item !== "." && $item !== "..") {

                    // Define full path of current item
                    $filePath = $directoryPath . DIRECTORY_SEPARATOR . $item;

                    // If current item is file add it to response array
                    if (is_file($filePath)) {
                        $filesArray[] = $parseObject ? new File($filePath) : $filePath;
                    }

                    // If current item is directory
                    if (is_dir($filePath)) {
                        if (!$hideDirectories) {
                            $directoriesArray[] = $filePath;
                        }

                        if ($recursive) {
                            $tmpArray = $this->scanDirectory($filePath, $recursive, $hideDirectories, $parseObject, true);

                            $directoriesArray = array_merge($directoriesArray, $tmpArray["directories"]);
                            $filesArray = array_merge($filesArray, $tmpArray["files"]);
                        }
                    }
                }
            }

            return $separate ? ["directories" => $directoriesArray, "files" => $filesArray] : array_merge($directoriesArray, $filesArray);
        }

        return [];
    }

    /**
     * Remove files older than specified number of days. Also can remove empty directories.
     *
     * @param int  $olderDays
     * @param bool $cleanEmptyDirectories
     * @return array
     */
    public function cleanTemporaryDirectory(int $olderDays = 10, bool $cleanEmptyDirectories = true) {

        // Define variables
        $removedFilesCount = 0;
        $removedDirectoriesCount = 0;

        // Scan temporary directory
        $temporaryScan = $this->scanDirectory($this->getTemporaryDirectory(), true, false, true, true);

        // Move every found file and check it last modification date
        /** @var File $file */
        foreach ($temporaryScan["files"] as $file) {
            if (Chronos::createFromTimestamp($file->getMTime())->diffInDays() > $olderDays) {
                unlink($file->getRealPath());
                $removedFilesCount++;
            }
        }

        // Check if process need to clean empty directories
        if ($cleanEmptyDirectories) {

            // Scan one more time
            $temporaryScan = $this->scanDirectory($this->getTemporaryDirectory(), true, false, false, true);

            // Reverse array
            $temporaryScan = array_reverse($temporaryScan["directories"]);

            foreach ($temporaryScan as $item) {
                if (count($this->scanDirectory($item, false, false, false, false)) <= 0) {
                    rmdir($item);
                    $removedDirectoriesCount++;
                }
            }
        }

        return [$removedFilesCount, $removedDirectoriesCount];
    }

    /**
     * Get real-path for specified path.
     *
     * @param string $value
     * @return bool|string
     */
    public function getRealpath(string $value) {
        return realpath($value);
    }
}
