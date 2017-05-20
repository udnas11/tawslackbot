<?php

class CachedConfig
{
    public $lastDisgustTS = 0;
    public $lastDisgustIndex = -1;
    public $cleanUpFiles = true;
}

class Config
{
    private static $cache_config;

    static $tokenKey = 'TAWSLACKBOT_TOKEN';
    static $apiUrl = 'https://slack.com/api';
    static $channelIds = [
        'announce' => 'C5E4ZRYJZ',
        'general' => 'C4PF84T6F',
        'welcome' => 'C4Q962Q31', 
        'bot_channel' => 'C5ESAU8CF'
    ];
    static $messageTemplates = [
        'newUserMessageTemplate' => "Welcome,  <@%s>! I hope you'll find lots of fun times playing with us!\nBut please, check <https://docs.google.com/document/d/1bfEh4NK2_rFzHyyOOVYdtF3regJMhcmlSaaKHoCLm98/edit|this document> first, in order to setup your profile accordingly to our standarts!\nHave fun!",
        'warnMessageToAnnounce' => "Non-admin user <@%s> attempted to write a message in #announcements. Time: <!date^%s^{date_num} {time_secs}|Could Not Parse>. Message: \n>>> %s",
        'warnMessageToAnnouncePrivate' => ":no_entry_sign: Sorry, you have no rights to post in #announcements!\nIf you want to comment somehow, either start a sub-thread, or write in #general.\nYour message was:\n>>> %s"
    ];
    static $disgustInterval = 60 * 60; // 60m = 60s * 60;
    static $disgustTitles = ['War Thunder', ' BMS', 'Ace Combat', 'WarThunder', 'World of Warplanes'];
    static $messageTemplatesDisgust = [
        'I am disgusted! How dare you mention %1$s?!?',
        'You have sinned, <@%2$s>! %1$s is Devil\'s work! Go play DCS for at least 2 hours to purge your soul!',
        '%1$s sucks, DCS da best!',
        'No, <@%2$s>, no.. now that\'s a bad word! We don\'t use %1$s in this house!',
        //todo "At least BMS has good missiles" for bms only
    ];

    /*
     * $button["name"] = "GoTawReportEvent";
		$button["text"] = "Report Event";
		$button["type"] = "button";
		$button["value"] = "http://taw.net/event/ReportEvent.aspx";
		$att["actions"][] = $button;
     */

    static $actionsDefault = [
        ['name' => 'GoToTawNet', 'text' => 'TAW Page', 'url' => 'http://www.taw.net']
    ];
    static $actionsForRoles = [
        'SL' => [
            ['name' => 'SL1', 'text' => 'sl 1', 'url' => 'http://www.sl1.net'],
            ['name' => 'SL2', 'text' => 'sl 2', 'url' => 'http://www.2l2.net']
            ],
        'DI' => [
            ['name' => 'di', 'text' => 'di', 'url' => 'http://www.di.net']
            ],
        'XO' => [
            ['name' => 'xo', 'text' => 'xo stuff', 'url' => 'http://www.xoStuph.net']
        ]
    ];

    public static function getToken()
    {
        return getenv(self::$tokenKey);
    }

    public static function GetConfig()
    {
        if (isset(self::$cache_config) == false)
        {
            if (file_exists('config.json'))
                $fileContents = file_get_contents('config.json');
            else
                $fileContents = false;

            if ($fileContents == false)
            {
                TawSlack::log("no file. creating new.", 'Config');
                self::$cache_config = new CachedConfig();
                file_put_contents('config.json', json_encode(self::$cache_config));
            }
            else
            {
                TawSlack::log("file found, loading", 'Config');
                self::$cache_config = json_decode($fileContents);
            }
        }
        return self::$cache_config;
    }

    public static function FlushConfig()
    {
        if (isset(self::$cache_config) == true)
        {
            file_put_contents('config.json', json_encode(self::$cache_config));
            TawSlack::log("flush success", 'Config');
        }
        else
            TawSlack::log("flush unnecessary, Config never used", 'Config');
    }


}
