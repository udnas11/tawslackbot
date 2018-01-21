<?php

require_once "config.php";
require_once "TawSlack.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $input = file_get_contents('php://input');
    TawSlack::log('input: ' . $input, 'BotRedirect');

    $user_name = $_POST['user_name'];
    $user_id = $_POST['user_id'];
    $text = $_POST['text'];

    header('Content-Type: application/json');
    if (TawSlack::isUserAdmin($user_id))
    {
        TawSlack::log("User (".$user_name.";".$user_id.") wrote personal messages to ALL users as TawBot. Text: ". $text, 'BotRedirectDMall', 'logBotRedirect.txt');
        $users = TawSlack::getUserList();
        if ($users == false)
        {
            echo "Something went wrong. Could not fetch user list.";
        }
        else
        {
            TawSlack::sendMessageToChannel("Starting mass broadcast of your message to all users. Ignore the timeout you got a few secs after invoking the command. Will let you know when process is completed.", $user_id);
            $success = 0;
            $failedUsernames = array();
            foreach ($users as $userInfo)
            {
                if ($userInfo['deleted'] == false)
                {
                    $userTarget = "@" . $userInfo['name'];
                    $result = TawSlack::sendMessageToChannel($text, $userTarget);
                    if ($result['ok'] == 'true')
                    {
                        $success++;
                    }
                    else
                    {
                        $failedUsernames[] = $userTarget;
                    }
                }
            }
            TawSlack::sendMessageToChannel("Successfully sent message to " . $success . " users.", $user_id);
            if (count($failedUsernames) > 0)
            {
                $failedFull = "Failed are: ";
                foreach ($failedUsernames as $failedUsername)
                    $failedFull .= $failedUsername . " ";
                TawSlack::sendMessageToChannel($failedFull, $user_id);
            }
        }
    }
    else
    {
        echo "Sorry, you're not an admin. :stuck_out_tongue_closed_eyes: But good job finding this, never thought anyone would find it by itself :sweat_smile:";
    }
}
