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
        $userId = $userInfo['id'];

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
        elseif ($actionName == 'inviteAllToChannel')
        {
            $channelInfo = $input['channel'];
            $channelID = $channelInfo['id'];
            $channelInfoCheck = TawSlack::getChannelInfo($channelID);
            if ($channelInfoCheck == false)
            {
                echo "I'm sorry, " . $userName . ". I'm afraid I can't do that. This is not a public channel.";
                return;
            }
            TawSlack::sendMessageToChannel('Inviting all users to channel <#' . $channelInfo['id'] . '> as requested by <@'.$userId.'>' , Config::$channelIds['bot_channel']);
            TawSlack::sendMessageToChannel('Inviting all users to this channel as requested by <@'.$userId.'>', $channelID);
            $userList = TawSlack::getUserList();
            $invitedCount = 0;
            $failedCount = 0;
            foreach ($userList as $userInfoList)
            {
                if ($userInfoList['deleted'] == false && $userInfoList['id'] != 'USLACKBOT')
                {
                    $response = TawSlack::inviteUserToChannel($userInfoList['id'], $channelID);
                    if ($response['ok'] == true)
                        $invitedCount++;
                    else if (in_array($response['error'], ['cant_invite_self', 'already_in_channel']) == false)
                        $failedCount++;
                }
            }
            TawSlack::sendMessageToChannel('Done inviting ' . $invitedCount . ' users to this channel.', $channelID);
            TawSlack::sendMessageToChannel('Done inviting all users to channel. Success/fails: ' . $invitedCount . '/' . $failedCount , Config::$channelIds['bot_channel']);
            echo 'Done mass-invite';
        }
    }
    elseif ($callback_id == 'collapse')
    {
        echo 'Bye';
    }
}