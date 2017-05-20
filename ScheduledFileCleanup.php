<?php
/**
 * Created by PhpStorm.
 * User: Aleph
 * Date: 15-May-17
 * Time: 22:29
 */

require_once "TawSlack.php";

if (Config::GetConfig()->cleanUpFiles)
    TawSlack::deleteOldFiles(60*60*24); // 60s * 60 = 1h;   1h * 24 = 1day;
else
    TawSlack::log('File Clean-up Disabled', 'FileDelete', 'log_fileDelete.txt');