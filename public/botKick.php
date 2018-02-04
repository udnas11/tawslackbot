<?php

require_once "config.php";
require_once "TawSlack.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $input = file_get_contents('php://input');
    TawSlack::log('input: ' . $input, 'BotKick');

    $user_name = $_POST['user_name'];
    $user_id = $_POST['user_id'];
    $channel_id = $_POST['channel_id'];
    $text = $_POST['text'];

    header('Content-Type: application/json');
    if (TawSlack::isUserAdmin($user_id))
    {
        if (in_array($channel_id, Config::$channelNoLeaveIds))
        {
            $userTarget = explode('|', $text)[0];
            $userTarget = substr($userTarget, 2);
            TawSlack::log(sprintf("Admin %s is kicking user %s from channel %s", $user_name, $userTarget, $channel_id), 'BotKick');
            $result = TawSlack::kickUserFromChannel($userTarget, $channel_id);
            if ($result['ok'] == 'true')
            {
                Config::GetConfig()->botKickUser = $userTarget;
                Config::FlushConfig();
                echo 'Done.';
            }
            else
            {
                echo 'Could not. Reason: ' . $result['error'];
            }
        }
        else
            echo "This channel is not marked as No-Leave. You can kick him with regular methods.";
    }
    else
    {
        echo "Sorry, you're not an admin. :stuck_out_tongue_closed_eyes: But good job finding this, never thought anyone would find it by itself :sweat_smile:";
    }
}
