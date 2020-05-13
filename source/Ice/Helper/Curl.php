<?php

namespace Ice\Helper;

class Curl
{
    public static function exec($curl, $debug = false)
    {
        if ($debug) {
            $handle = \fopen(__DIR__ . '/errorlog.txt', 'w');
            \curl_setopt($curl, CURLOPT_VERBOSE, 1);
            \curl_setopt($curl, CURLOPT_STDERR, $handle);
        }

        $content = \curl_exec($curl);

        \curl_close($curl);

        if ($debug) {
            \fclose($handle);
        }

        return $content;
    }
}