<?php

require_once "config.php";
require_once "TawSlack.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$payload = $_POST['payload'];
	
	$input = json_decode($payload, true);
	$callback_id = $input["callback_id"];
	
	if ($callback_id == "navigation")
	{
		$action = $input["actions"][0];
		$url = $action["value"];
		echo $url;
	}
	elseif ($callback_id == 'admin')
    {
        $action = $input['actions'][0];
        $actionName = $action['name'];

        $userInfo = $input['user'];
        $userName = $userInfo['name'];

        if ($actionName == 'setDisgustResponses')
        {
            $newVal = $action['value'] === 'true';
            TawSlack::log('newval: ' . $newVal);
            Config::GetConfig()->disgustResponsesEnabled = $newVal;
            Config::FlushConfig();
            echo $newVal ? 'Enabled' : 'Disabled'; // send the response
        }
        elseif ($actionName == 'runCleanupNow')
        {
            TawSlack::log('Running forced clean-up! caller: '.$userName, 'FileDelete', 'log_fileDelete.txt');
            TawSlack::sendMessageToChannel('Running forced file clean-up. User: '.$userName, Config::$channelIds['bot_channel']);
            TawSlack::deleteOldFiles(60*60*24);
            echo 'Done clean-up';
        }
        elseif ($actionName == 'getChannelGroupID')
        {
            $channelInfo = $input['channel'];
            $channelID = $channelInfo['id'];
            echo $channelID;
        }
    }
    elseif ($callback_id == 'collapse')
    {
        echo 'Bye';
    }
}