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

        if ($actionName == 'setCleanupFiles')
        {
            $newVal = $action['value'] === 'true';
            TawSlack::log('newval: ' . $newVal);
            Config::GetConfig()->cleanUpFiles = $newVal;
            Config::FlushConfig();
            echo $newVal ? 'Enabled' : 'Disabled'; // send the response
        }
    }
}