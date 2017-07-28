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
            if ($data['ok'] != 'true')
                self::log('error: ' . $data['error'], 'API');
            return $data;
        }
        return false;
    }

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

    static public function getChannelInfo($channelId)
    {
        $channelInfo = self::callSlackMethod('channels.info', ['channel' => $channelId]);
        if ($channelInfo['ok'])
        {
            $channelData = $channelInfo['channel'];
            return $channelData;
        }
        else
        {
            return false;
        }
    }

    static public function getGroupInfo($channelId)
    {
        $channelInfo = self::callSlackMethod('groups.info', ['channel' => $channelId]);
        if ($channelInfo['ok'])
        {
            $channelData = $channelInfo['group'];
            return $channelData;
        }
        else
        {
            return false;
        }
    }

    static public function deleteMessage($ts, $author, $channel)
    {
        self::callSlackMethod('chat.delete', ['ts' => $ts, 'author' => $author, 'channel' => $channel]);
    }

    static public function getChannelList()
    {
        $response = self::callSlackMethod('channels.list');
        if ($response != false && $response['ok'] == true)
        {
            $channels = $response['channels'];
            $channelIds = array();
            foreach ($channels as $channelInfo)
                $channelIds[] = $channelInfo['id'];
            return $channelIds;
        }
        return false;
    }

    static public function getUserList()
    {
        $response = self::callSlackMethod('users.list');
        if ($response != false && $response['ok'] == true)
        {
            $users = $response['members'];
            return $users;
        }
        return false;
    }

    static public function getFilesListChannel($minimumTimestampDelta, $channelId = null)
    {
        if ($channelId === null)
            $response = self::callSlackMethod('files.list');
        else
            $response = self::callSlackMethod('files.list', ['channel' => $channelId]);

        if ($response != false && $response['ok'] == true)
        {
            $files = $response['files'];
            $fileIds = array();
            $currentTimeStamp = time();
            foreach ($files as $fileInfo)
            {
                $fileTimestamp = $fileInfo['timestamp'];
                if ($currentTimeStamp - $fileTimestamp > $minimumTimestampDelta)
                {
                    $fileIds[] = $fileInfo['id'];
                    TawSlack::log('Adding file to list: ' . $fileInfo['id'] . '; channel: ' . $channelId, 'FileDelete', 'log_fileDelete.txt');
                }
                else
                    TawSlack::log('Timestamp not good file: ' . $fileInfo['id'] . '; channel: ' . $channelId, 'FileDelete', 'log_fileDelete.txt');
            }
            return $fileIds;
        }
        return false;
    }

    static public function getFileListTotal($minimumTimestampDelta)
    {
        $channelIds = self::getChannelList();
        $fileIds = array();

        foreach ($channelIds as $channelId)
        {
            $filesInChannel = self::getFilesListChannel($minimumTimestampDelta, $channelId);
            if ($filesInChannel != false)
                $fileIds = array_merge($fileIds, $filesInChannel);
        }

        $filesInChannel = self::getFilesListChannel($minimumTimestampDelta, null);
        if ($filesInChannel != false)
            $fileIds = array_merge($fileIds, $filesInChannel);

        $fileIds = array_unique($fileIds);

        /*TawSlack::log('Enumerating unique files:', 'FileDelete', 'log_fileDelete.txt');
        foreach ($fileIds as $key => $val)
            TawSlack::log('f: '.$key.'=>'.$val, 'FileDelete', 'log_fileDelete.txt');*/

        return $fileIds;
    }

    static public function deleteFile($file)
    {
        return self::callSlackMethod('files.delete', ['file' => $file]);
    }

    static public function deleteOldFiles($minimumTimestampDelta)
    {
        TawSlack::log('Attempt delete files older than ' . $minimumTimestampDelta . ' seconds', 'FileDelete', 'log_fileDelete.txt');
        TawSlack::sendMessageToChannel('Running scheduled file clean-up.', Config::$channelIds['bot_channel']);

        $fileIdList = self::getFileListTotal($minimumTimestampDelta);
        $fileCountTotal = count($fileIdList);

        TawSlack::sendMessageToChannel('Files to delete count: ' . $fileCountTotal, Config::$channelIds['bot_channel']);
        TawSlack::log('Files to delete count: ' . $fileCountTotal, 'FileDelete', 'log_fileDelete.txt');

        foreach ($fileIdList as $key => $fileId)
        {
            TawSlack::log('Deletion in progress: ' . $key . '/' . $fileCountTotal, 'FileDelete', 'log_fileDelete.txt');
            $response = TawSlack::deleteFile($fileId);
            TawSlack::log('Success: '. json_encode($response), 'FileDelete', 'log_fileDelete.txt');
        }

        TawSlack::log('Done', 'FileDelete', 'log_fileDelete.txt');
        TawSlack::sendMessageToChannel('Done.', Config::$channelIds['bot_channel']);
    }
}

