<?php

require_once "config.php";
require_once "TawSlack.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $input = file_get_contents('php://input');
    TawSlack::log('input: ' . $input, 'BotRedirect');

    $user_name = $_POST['user_name'];
    $user_id = $_POST['user_id'];
    $channel_id = $_POST['channel_id'];
    $text = $_POST['text'];
    $responseUrl = $_POST['response_url'];

    header('Content-Type: application/json');
    if (TawSlack::isUserAdmin($user_id))
    {
        if (TawSlack::getChannelInfo($channel_id) != false)
        {
            TawSlack::log("User (".$user_name.";".$user_id.") wrote message as TawBot. Channel: ". $channel_id . "; Message: " . $text, 'BotRedirect', 'logBotRedirect.txt');
            $result = TawSlack::sendMessageToChannel($text, $channel_id);
            if ($result['ok'] == 'true')
                echo 'Done.';
            else
                echo 'Could not. Reason: ' . $result['error'];
        }
        else
        {
            echo "You must be into a public channel to use this. _(or a channel where AlephRo also happens to be)_";
        }
    }
    else
    {
        echo "Sorry, you're not an admin. :stuck_out_tongue_closed_eyes: But good job finding this, never thought anyone would find it by itself :sweat_smile:";
    }

}
