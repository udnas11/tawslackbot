<?php

require_once "config.php";
require_once "TawSlack.php";


if($_SERVER['REQUEST_METHOD'] == "POST")
{
	http_response_code(200);

	$jsonInput = file_get_contents('php://input');
    //TawSlack::log($jsonInput, 'Event');

    $input = json_decode($jsonInput, true);
/*
    $output['challenge'] = $input['challenge'];
    echo json_encode($output);
//*/

//*
	$event = $input['event'];
	$eventTime = $input['event_time'];
	$type = $event['type'];
	$subtype = $event['subtype'];
	$channel = $event['channel'];
	$user = $event['user'];
	$text = $event['text'];
	$timeStamp = $event['ts'];
	
	if (isset($subtype))
	{
		if ($subtype == "channel_join")
        {
			if ($channel == Config::$channelIds['welcome']) //welcome
			{
                TawSlack::log(sprintf('new user %s', $user));
                TawSlack::sendWelcomeMessage($user);
			}
		}
		elseif ($subtype == "channel_leave")
        {
            if (in_array($channel, Config::$channelNoLeaveIds)) //no leave channel
            {
                if ($user == Config::GetConfig()->botKickUser)
                {
                    Config::GetConfig()->botKickUser = 'none';
                    Config::FlushConfig();
                    TawSlack::log('User successfully kicked from No-Leave channel');
                }
                else
                {
                    TawSlack::log(sprintf('User %s attempted to leave no-leave channel %s', $user, $channel));
                    TawSlack::sendMessageToChannel(sprintf("Only admins can remove people from this channel.\nI'm sorry, <@%s>. I can't let you leave.", $user), $channel);
                    TawSlack::inviteUserToChannel($user, $channel);
                    TawSlack::sendMessageToChannel(sprintf("User <@%s> attempted to leave no-leave channel <#%s>. Adding him back in there.\nIf you're an admin and want to remove someone from a no-leave channel use command `/botKick @user`", $user, $channel), Config::$channelIds['bot_channel']);
                }
            }
        }
	}
	elseif (isset($user))
    {
        if (in_array($channel, Config::$channelAdminIds) && isset($event['thread_ts']) == false) // post to admin-only channel
        {
            //check if user is admin
            $isAdmin = TawSlack::isUserAdminOrBot($user);
            if ($isAdmin == false)
            {
                TawSlack::deleteMessage($timeStamp, $user, $channel);

                $channelInfo = TawSlack::getChannelInfo($channel);
                if ($channelInfo == false)
                    $channelInfo = TawSlack::getGroupInfo($channel);

                if ($channelInfo != false)
                {
                    $channel = $channelInfo['id'];
                    if (in_array($channel, Config::$channelAdminIdsSilent) == false)
                        TawSlack::sendWarnMessageAttemptAnnounce($user, $text, $channel, $eventTime);
                    TawSlack::sendMessageToChannel(sprintf(Config::$messageTemplates['warnMessageToAnnouncePrivate'], $channel, $text), $user);
                }
            }
        }
    }
 //*/
}