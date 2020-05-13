<?php

use Ice\Core\Module;

function getSourceDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::SOURCE_DIR);
}

function getConfigDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::CONFIG_DIR);
}

function getVarDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::VAR_DIR);
}

function getDataDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::DATA_DIR);
}

function getTempDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::TEMP_DIR);
}

function getBackupDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::BACKUP_DIR);
}

function getCacheDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::CACHE_DIR);
}

function getLogDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::LOG_DIR);
}

function getRunDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::RUN_DIR);
}

function getPublicDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::PUBLIC_DIR);
}

function getUploadDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::UPLOAD_DIR);
}

function getDownloadDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::DOWNLOAD_DIR);
}

function getPrivateDownloadDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::PRIVATE_DOWNLOAD_DIR);
}

function getResourceDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::RESOURCE_DIR);
}

function getCompiledResourceDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::COMPILED_RESOURCE_DIR);
}

function getModuleDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath();
}

function getServerTimeZone()
{
    $timezone = 'Europe/Moscow'; //'UTC';

    return $timezone;

    if (is_link('/etc/localtime')) {
        // Mac OS X (and older Linuxes)
        // /etc/localtime is a symlink to the
        // timezone in /usr/share/zoneinfo.
        $filename = readlink('/etc/localtime');
        $pos = strpos($filename, '/usr/share/zoneinfo/');
        if ($pos !== false) {
            $timezone = substr($filename, $pos + 20);
        }
    } elseif (is_file('/etc/timezone')) {
        // Ubuntu / Debian.
        $data = file_get_contents('/etc/timezone');
        if ($data) {
            $timezone = $data;
        }
    } elseif (is_file('/etc/sysconfig/clock')) {
        // RHEL / CentOS
        $data = parse_ini_file('/etc/sysconfig/clock');
        if (!empty($data['ZONE'])) {
            $timezone = $data['ZONE'];
        }
    }

    return $timezone;
}
