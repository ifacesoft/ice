<?php
use Ice\Core\Module;
use Ice\Helper\Directory;

function getTempDir()
{
    return Directory::get(Module::getInstance()->get(Module::TEMP_DIR));
}

function getLogDir()
{
    return Directory::get(Module::getInstance()->get(Module::LOG_DIR));
}

function getRunDir()
{
    return Directory::get(Module::getInstance()->get(Module::RUN_DIR));
}

function getUploadDir()
{
    return Directory::get(Module::getInstance()->get(Module::UPLOAD_DIR));
}

function getDownloadDir()
{
    return Directory::get(Module::getInstance()->get(Module::DOWNLOAD_DIR));
}