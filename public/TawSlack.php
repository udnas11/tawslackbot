<?php
require_once "config.php";

class TawSlack
{
    public function __construct() {}

    static public function log($str, $what = 'INFO', $filename = 'log.txt')
    {
        $f = fopen("logs/".$filename, "a");
        fwrite($f, sprintf("[%s] %s\n", $what, $str));
        fclose($f);
    }

    static public function execUrl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $data = curl_exec($ch);
        self::log( curl_errno($ch) . " " . curl_error($ch), 'CURL_ERR');
        if (curl_errno($ch) == 0)
        {
            curl_close($ch);
            return $data;
        }
        return false;
    }

    static public function callSlackMethod($cmd, $params = array())
    {
        $params['token'] = Config::getToken();
        $urlParams = http_build_query($params);
        $url = sprintf("%s/%s?%s", Config::$apiUrl, $cmd, $urlParams);
        self::log($url, 'API');
        if ($response = self::execUrl($url))
        {
            $data = json_decode($response, true);
            return $data;
        }
        return false;
    }

    static public function callTawApi() {}

    static public function sendMessageToChannel($message, $channel)
    {
        return self::callSlackMethod('chat.postMessage', [
            'text' => $message,
            'channel' => $channel,
            'as_user' => false
        ]);
    }

    static public function sendWelcomeMessage($username)
    {
        $message = sprintf(Config::$messageTemplates['newUserMessageTemplate'], $username );
        return self::sendMessageToChannel($message, Config::$channelIds['welcome']);
    }

    static public function sendWarnMessageAttemptAnnounce($user, $message, $timestamp)
    {
        $msg = sprintf(Config::$messageTemplates['warnMessageToAnnounce'], $user, $timestamp, $message);
        TawSlack::log($msg, "AnnounceRestriction");
        return self::sendMessageToChannel($msg, Config::$channelIds['bot_channel']);
    }

    static public function sendDisgustMessage($title, $user)
    {
        $config = Config::GetConfig();
        do
        {
            $index = array_rand(Config::$messageTemplatesDisgust);
        }
        while ($index == $config->lastDisgustIndex);
        $config->lastDisgustIndex = $index;
        Config::FlushConfig();

        $msg = sprintf(Config::$messageTemplatesDisgust[$index], $title, $user);
        return self::sendMessageToChannel($msg, Config::$channelIds['general']);
    }

    static public function getUserInfo($userId)
    {
        $userInfo = self::callSlackMethod('users.info', ['user' => $userId]);
        if ($userInfo['ok'])
        {
            $userData = $userInfo['user'];
            return $userData;
        }
        else
        {
            return false;
        }
    }

    static public function isUserAdmin($userId)
    {
        $userInfo = self::getUserInfo($userId);
        if (!$userInfo)
            return false;
        return $userInfo['is_admin'];
    }

    static public function deleteMessage($ts, $author, $channel)
    {
        self::callSlackMethod('chat.delete', ['ts' => $ts, 'author' => $author, 'channel' => $channel]);
    }

    static public function getFileList()
    {
        $response = self::callSlackMethod('files.list');
        if ($response != false && $response['ok'] == true)
        {
            TawSlack::log('Found ' . count($response['files']), 'GetFileList');
            return $response['files'];
        }

        return false;
    }

    static public function deleteFile($file)
    {
        return self::callSlackMethod('files.delete', ['file' => $file]);
    }

    static public function deleteOldFiles($minimumTimestampDelta)
    {
        TawSlack::log('Attempt delete files older than ' . $minimumTimestampDelta . ' seconds', 'FileDelete', 'log_fileDelete.txt');
        TawSlack::sendMessageToChannel('Running scheduled file clean-up.', Config::$channelIds['bot_channel']);
        $fileList = self::getFileList();
        if ($fileList != false)
        {
            $fileCountTotal = count($fileList);
            $currentTimeStamp = time();
            $filesToDelete = array();

            foreach ($fileList as $fileInfo)
            {
                $fileId = $fileInfo['id'];
                $fileTimestamp = $fileInfo['timestamp'];
                if ($currentTimeStamp - $fileTimestamp > $minimumTimestampDelta)
                    $filesToDelete[] = $fileId;
            }

            $fileCountToDelete = count($filesToDelete);
            TawSlack::sendMessageToChannel('Files to delete count / Total files count: ' . $fileCountToDelete . '/' . $fileCountTotal, Config::$channelIds['bot_channel']);
            TawSlack::log('Files to delete: ' . $fileCountToDelete . '/' . $fileCountTotal, 'FileDelete', 'log_fileDelete.txt');
            foreach ($filesToDelete as $key => $fileId)
            {
                TawSlack::log('Deletion in progress: ' . $key . '/' . $fileCountToDelete, 'FileDelete', 'log_fileDelete.txt');
                $response = TawSlack::deleteFile($fileId);
                TawSlack::log('Response: '. json_encode($response), 'FileDelete', 'log_fileDelete.txt');
            }
            TawSlack::log('Done', 'FileDelete', 'log_fileDelete.txt');
            TawSlack::sendMessageToChannel('Done.', Config::$channelIds['bot_channel']);
            return true;
        }
        TawSlack::log('No file list to delete.', 'FileDelete', 'log_fileDelete.txt');
        TawSlack::sendMessageToChannel('Could not get file list for deletion.', Config::$channelIds['bot_channel']);
        return false;
    }
}

