<?php

require_once "Controls/Button.php";
require_once "config.php";
require_once "TawSlack.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$jsonInput = file_get_contents('php://input');
	TawSlack::log('input: ' . $jsonInput, 'BotHelp');
	
	$user_name = $_POST['user_name'];
	$user_id = $_POST['user_id'];
	$channel_id = $_POST['channel_id'];
	
	if (isset($user_name))
	{	
		header('Content-Type: application/json');
		$response['text'] = "Hi there, " . $user_name . ". How can I help you?";

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

        // ADMIN STUFF
		if ($userInfo['is_admin'])
        {
            $cleanupEnabled = Config::GetConfig()->cleanUpFiles;

            $attAdmin['text'] = "Admin:\nFile scheduled clean-up is " . (($cleanupEnabled ? "enabled." : "disabled.") . "\nIf you run forced clean-up, it will most likely answer you directly with an error. Ignore that. Look in #slackadmins channel instead for the result.");
            $attAdmin['color'] = "#3AA3E3";
            $attAdmin['attachment_type'] = "default";
            $attAdmin['callback_id'] = "admin";

            $actions = array();
            if ($cleanupEnabled)
                $button = new Button('setCleanupFiles', 'Disable Cleanup', 'false');
            else
                $button = new Button('setCleanupFiles', 'Enable Cleanup', 'true');
            $actions[] = (array)$button;

            $button = new Button('runCleanupNow', 'Run Cleanup Now', '1');
            $actions[] = (array)$button;

            $attAdmin['actions'] = $actions;
            $response['attachments'][] = $attAdmin;
        }

        TawSlack::log('send: ' . json_encode($response), 'BotHelp');
		echo json_encode($response);
	}
}
