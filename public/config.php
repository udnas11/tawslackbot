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
        'newUserMessageTemplate' => "Welcome,  <@%s>! I hope you'll find lots of fun times playing with us!\nBut please, check <https://docs.google.com/document/d/1KNM5OzEwtb7Dkgpsq-Hse4tTMcP0KMVFY1xk71s3prA|this document> first, in order to setup your profile accordingly to our standarts!\nHave fun!",
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
        ['name' => 'GoTawNet', 'text' => 'TAW', 'url' => 'http://www.taw.net'],
        ['name' => 'GoTawDcsOrg', 'text' => 'TAWDCS', 'url' => 'https://tawdcs.org/'],
        ['name' => 'GoKO', 'text' => 'Kaucasus Offensive', 'url' => 'http://ko.tawdcs.org/'],
        ['name' => 'GoMembersSection', 'text' => 'Members Section', 'url' => 'https://tawdcs.org/sop/'],
        ['name' => 'GoSrsFreqs', 'text' => 'SRS Frequencies', 'url' => 'https://docs.google.com/document/d/1U4fe7EhdJ73F2ojMsXt7yOFxAoHd8OIH1Iv6OuPTSuc/edit']
    ];
    static $actionsForRoles = [
        // ----------------------
        'SL' => [
            ['name' => 'EventReporting', 'text' => 'Event Reporting', 'url' => 'http://taw.net/event/ReportEvent.aspx']
        ],
        'SL-EU' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit']
        ],

        // ----------------------
        'PLF-EU' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit']
        ],
        'PLS-EU' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit']
        ],
        'PLR-EU' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit']
        ],

        // ----------------------
        'FS-EU' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit']
        ],

        // ----------------------
        'XO-EU' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit']
        ],

        // ----------------------
        'CO-EU' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit']
        ],

        // ----------------------
        'DC' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit']
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
