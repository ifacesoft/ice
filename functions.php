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

function getDataDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::DATA_DIR);
}

function getTempDir($moduleName = null)
{
    return Module::getInstance($moduleName)->getPath(Module::TEMP_DIR);
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
