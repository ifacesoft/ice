<?php

namespace Ice;

use Ice\Helper\Directory;

trait Path
{
    /**
     * @param $path
     * @param $fileName
     * @param bool $isCreate
     * @return string
     * @throws Core\Exception
     */
    public function getModuleCacheFile($path, $fileName, $isCreate = false)
    {
        return $this->getModuleFilePath(\getCacheDir(), $path, $fileName, $isCreate);
    }

    /**
     * @param $moduleDir
     * @param $path
     * @param $fileName
     * @param $isCreate
     * @return string
     * @throws Core\Exception
     */
    private function getModuleFilePath($moduleDir, $path, $fileName, $isCreate)
    {
        return $this->getModuleDirPath($moduleDir, $path, $isCreate) . trim($fileName, '/');
    }

    /**
     * @param $moduleDir
     * @param $path
     * @param $isCreate
     * @return string
     * @throws Core\Exception
     */
    private function getModuleDirPath($moduleDir, $path, $isCreate)
    {
        $path = $moduleDir . trim(str_replace(['\\', '_'], ['/', '/'], $path), '/');

        return $isCreate ? Directory::get($path) : $path . '/';
    }

    /**
     * @param $path
     * @param $fileName
     * @param bool $isCreate
     * @return string
     * @throws Core\Exception
     */
    public function getModuleTempFile($path, $fileName, $isCreate = false)
    {
        return $this->getModuleFilePath(\getTempDir(), $path, $fileName, $isCreate);
    }

    /**
     * @param $path
     * @param bool $isCreate
     * @return string
     * @throws Core\Exception
     */
    public function getModuleCacheDir($path, $isCreate = false)
    {
        return $this->getModuleDirPath(\getCacheDir(), $path, $isCreate);
    }

    /**
     * @param $path
     * @param bool $isCreate
     * @return string
     * @throws Core\Exception
     */
    public function getModuleTempDir($path, $isCreate = false)
    {
        return $this->getModuleDirPath(\getTempDir(), $path, $isCreate);
    }

    /**
     * @param $path
     * @param $fileName
     * @param bool $isCreate
     * @return string
     * @throws Core\Exception
     */
    public function getModuleUploadFile($path, $fileName, $isCreate = false)
    {
        return $this->getModuleFilePath(\getUploadDir(), $path, $fileName, $isCreate);
    }

    /**
     * @param $path
     * @param bool $isCreate
     * @return string
     * @throws Core\Exception
     */
    public function getModuleUploadDir($path, $isCreate = false)
    {
        return $this->getModuleDirPath(\getUploadDir(), $path, $isCreate);
    }

    /**
     * @param $path
     * @param $fileName
     * @param bool $isCreate
     * @return string
     * @throws Core\Exception
     */
    public function getModuleDataFile($path, $fileName, $isCreate = false)
    {
        return $this->getModuleFilePath(\getDataDir(), $path, $fileName, $isCreate);
    }
}