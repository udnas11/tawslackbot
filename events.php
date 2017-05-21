<?php

require_once "config.php";
require_once "TawSlack.php";


if($_SERVER['REQUEST_METHOD'] == "POST")
{
	http_response_code(200);

	$jsonInput = file_get_contents('php://input');
    TawSlack::log($jsonInput, 'Event');

    $input = json_decode($jsonInput, true);

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
	}
	elseif (isset($user))
	{
		if ($channel == Config::$channelIds['announce'] && isset($event['thread_ts']) == false) //general/announce
		{
		    //check if user is admin
            $isAdmin = TawSlack::isUserAdmin($user);
            if ($isAdmin == false)
            {
                TawSlack::deleteMessage($timeStamp, $user, $channel);
                TawSlack::sendWarnMessageAttemptAnnounce($user, $text, $eventTime);
                TawSlack::sendMessageToChannel(sprintf(Config::$messageTemplates['warnMessageToAnnouncePrivate'], $text), $user);
            }
		}
        if ($channel == Config::$channelIds['general'])
        {
            TawSlack::log("messageTS: " . $timeStamp . "; currentTS: " . time(), 'TIME');
            foreach (Config::$disgustTitles as $title)
            {
                if (stripos($text, $title) !== false)
                {
                    $deltaTimeStamp = $timeStamp - Config::GetConfig()->lastDisgustTS; // in seconds
                    TawSlack::log('disgust delta: ' . $deltaTimeStamp . ' out of ' . Config::$disgustInterval, 'Disgust');
                    if ($deltaTimeStamp > Config::$disgustInterval)
                    {
                        TawSlack::log("should disgust", 'Disgust');
                        Config::GetConfig()->lastDisgustTS = $timeStamp;
                        Config::FlushConfig();
                        TawSlack::sendDisgustMessage($title, $user);
                    }
                    break;
                }
            }
        }
	}
}