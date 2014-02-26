<?php
/**
 * @file
 * Directory index file
 *
 * Run and flush ice application
 *
 * @author dp
 */

require_once './Ice.php';

ice\Ice::get(basename(__DIR__))->run()->flush();
