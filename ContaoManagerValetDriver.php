<?php
namespace Valet\Drivers;

use Valet\Drivers\ValetDriver;

class ContaoManagerValetDriver extends ValetDriver
{

    /**
     * Contao's document root (public/web)
     *
     * @var string
     */
    public $docRootPath = '';

    /**
     * Determine if the driver serves the request.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return bool
     */
    public function serves($sitePath, $siteName, $uri): bool
    {
        if (file_exists($sitePath.'/public/contao-manager.phar.php')) {
            $this->docRootPath = '/public';
        }
        if (file_exists($sitePath.'/web/contao-manager.phar.php')) {
            $this->docRootPath = '/web';
        }
        return $this->docRootPath != '';
    }

    /**
     * Determine if the incoming request is for a static file.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string|false
     */
    public function isStaticFile($sitePath, $siteName, $uri)/*: string|false */
    {
        if ($this->isActualFile($staticFilePath = $sitePath.'/web/'.$uri)) {
            return $staticFilePath;
        } elseif ($this->isActualFile($staticFilePath = $sitePath.'/public/'.$uri)) {
            return $staticFilePath;
        }

        return false;
    }

    /**
     * Get the fully resolved path to the application's front controller.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string
     */
    public function frontControllerPath($sitePath, $siteName, $uri): string
    {
        if (preg_match('/(^\/(contao-manager\.phar|preview)\.php)/', $uri, $match) === 1) {
            $frontControllerPath = $sitePath.$this->docRootPath.$match[1];
        } else {
            $frontControllerPath = $sitePath.$this->docRootPath.'/index.php';
        }

        $_SERVER['SCRIPT_FILENAME'] = $frontControllerPath;

        return $frontControllerPath;
    }
}
