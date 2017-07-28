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

    header('Content-Type: application/json');
    if (TawSlack::isUserAdmin($user_id))
    {
        $userTarget = explode(' ', $text)[0]; //including @ cause it needs it
        $actualText = substr($text, strlen($userTarget));

        TawSlack::log("User (".$user_name.";".$user_id.") wrote personal message as TawBot. Input: ". $text, 'BotRedirectDM', 'logBotRedirect.txt');
        $result = TawSlack::sendMessageToChannel($actualText, $userTarget);
        if ($result['ok'] == 'true')
            echo 'Done.';
        else
            echo 'Could not. Reason: ' . $result['error'];
    }
    else
    {
        echo "Sorry, you're not an admin. :stuck_out_tongue_closed_eyes: But good job finding this, never thought anyone would find it by itself :sweat_smile:";
    }
}
