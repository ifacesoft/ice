<?php

namespace Ice\Helper;

class Data_Xml
{
    public static function getArray(\SimpleXMLElement $simpleXMLElement) {
        return Json::decode(Json::encode($simpleXMLElement));
    }
}