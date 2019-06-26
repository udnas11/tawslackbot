<?php

require_once "Controls/Button.php";
require_once "Controls/NavUrl.php";
require_once "config.php";
require_once "TawSlack.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$jsonInput = file_get_contents('php://input');
	TawSlack::log('input: ' . $jsonInput, 'BotHelp');
	
	$user_name = $_POST['user_name'];
	$user_id = $_POST['user_id'];
	$channel_id = $_POST['channel_id'];
    $userInfo = TawSlack::getUserInfo($user_id);
    $userFirstName = $userInfo['profile']['first_name'];
    $userLastName = $userInfo['profile']['last_name'];
	
	if (isset($user_name))
	{	
		header('Content-Type: application/json');
		$responseText = "Hi there, " . $userLastName . "!\nThere are a couple of links that you might find useful:\n";
		$responseText .= GetNavUrlsGeneral();
        $specialRole = false;
        if ($userInfo['is_admin'])
        {
            foreach (Config::$actionsPositions as $role => $actionKey)
            {
                if (stripos($userFirstName, $role) !== false)
                {
                    if ($specialRole == false)
                    {
                        $responseText .= "Also here are a few links specifically for your position in TAW: \n";
                        $specialRole = true;
                    }
                    $responseText .= GetNavUrlsForPosition($role);
                }
            }
            $responseText .= "Want something more to be included? Contact AlephRo for that.";
        }

		$response['text'] = $responseText;

		/*
		// NAVIGATIION: default user
        $att = array();
		$att['text'] = "Navigation";
		$att['color'] = "#3AA3E3";
		$att['attachment_type'] = "default";
		$att['callback_id'] = "navigation";
		$actions = array();

        foreach (Config::$actionsDefault as $roleAction)
        {
            $button = new Button($roleAction['name'], $roleAction['text'], $roleAction['url']);
            $actions[] = (array)$button;//->ToArray();
		}

        $att['actions'] = $actions;
        $response['attachments'][] = $att;

		// NAVIGATION: position-specific
        $att = array();
        $att['text'] = "Position-specific navigation: ";
        $att['color'] = "#3AA3E3";
        $att['attachment_type'] = "default";
        $att['callback_id'] = "navigation";
        $actions = array();

		$userInfo = TawSlack::getUserInfo($user_id);
        $userFirstName = $userInfo['profile']['first_name'];
        foreach (Config::$actionsForRoles as $role => $roleActions)
        {
            if (stripos($userFirstName, $role) !== false)
            {
                $att['text'] .= $role.'; ';
                foreach ($roleActions as $roleAction)
                {
                    $button = new Button($roleAction['name'], $roleAction['text'], $roleAction['url']);
                    $actions[] = (array)$button;//->ToArray();
                }
            }
        }
        $att['actions'] = $actions;
        if (count($actions) > 0)
		    $response['attachments'][] = $att;
		*/

        // ADMIN STUFF
		if ($userInfo['is_admin'])
        {
            $attAdmin['text'] = "Admin:\nFile cleanup and mass user invite will most likely yield you an timeout error. Ignore that.";
            $attAdmin['color'] = "#3AA3E3";
            $attAdmin['attachment_type'] = "default";
            $attAdmin['callback_id'] = "admin";

            $actions = array();
            $button = new Button('runCleanupNow', 'Run Cleanup Now', '1');
            $actions[] = (array)$button;

            $button = new Button('getChannelGroupID', 'Get Channel ID', '1');
            $actions[] = (array)$button;

            $button = new Button('inviteAllToChannel', 'Invite All To Channel', '1');
            $actions[] = (array)$button;

            $button = new Button('inviteAllEUToChannel', 'Invite All EU To Channel', '1');
            $actions[] = (array)$button;

            $attAdmin['actions'] = $actions;
            $response['attachments'][] = $attAdmin;
        }

        $attHide['text'] = "Hide this message:";
        $attHide['color'] = "#3AA3E3";
        $attHide['attachment_type'] = "default";
        $attHide['callback_id'] = "collapse";

        $actions = array();
        $button = new Button('collapseMessage', 'Collapse', '1');
        $actions[] = (array)$button;
        $attHide['actions'] = $actions;
        $response['attachments'][] = $attHide;

        TawSlack::log('send: ' . json_encode($response), 'BotHelp');
		echo json_encode($response);
	}
}
