<?php
/**
 * Created by PhpStorm.
 * User: Aleph
 * Date: 31-May-17
 * Time: 22:25
 */

function GetNavUrl($key)
{
    $action = Config::$actionsDictionary[$key];
    return sprintf("<%s|%s>", $action['url'], $action['text']);
}

function GetNavUrlsForPosition($position)
{
    $result = '';
    $actionKeys = Config::$actionsPositions[$position];
    foreach ($actionKeys as $key)
    {
        $result .= "- " . GetNavUrl($key) . "\n";
    }
    return $result;
}

function GetNavUrlsGeneral()
{
    $result = '';
    $actionKeys = Config::$actionsDefault;
    foreach ($actionKeys as $action)
    {
        $result .= sprintf("- <%s|%s>\n", $action['url'], $action['text']);
    }
    return $result;
}