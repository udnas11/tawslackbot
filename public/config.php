<?php

class CachedConfig
{
    public $lastDisgustTS = 0;
    public $lastDisgustIndex = -1;
    public $cleanUpFiles = true;
    public $disgustResponsesEnabled = false;
}

class Config
{
    private static $cache_config;

    static $tokenKey = 'TAWSLACKBOT_TOKEN';
    static $apiUrl = 'https://slack.com/api';
    static $channelIds = [
        'announce' => 'C208Z2N4F',

        'general' => 'C1SHP4Y4A', // production
        //'general' => 'C4PF84T6F', // test

        'welcome' => 'C2021K9FA', // production
        //'welcome' => 'C4Q962Q31', //test

        'bot_channel' => 'G20JEKWJU' // production
        //'bot_channel' => 'C5ESAU8CF' // test
    ];
    static $messageTemplates = [
        'newUserMessageTemplate' => "Welcome, <@%s>! I hope you'll have lots of fun playing with us!\nBut please, check <https://docs.google.com/document/d/1KNM5OzEwtb7Dkgpsq-Hse4tTMcP0KMVFY1xk71s3prA|THIS DOCUMENT> first, in order to setup your profile according to our standards!\nAlso, to get access to our super top secret files (documents, skins, etc), register on our specialised website <https://docs.google.com/document/d/1RczQPM9tfxhpm724GxgdYEzqRGBFvnmp_d9fxtrq2PQ/edit?usp=sharing|RIGHT HERE>.\nHave fun!",
        'warnMessageToAnnounce' => "Non-admin user <@%s> attempted to write a message in #announcements. Time: <!date^%s^{date_num} {time_secs}|Could Not Parse>. Message: \n>>> %s",
        'warnMessageToAnnouncePrivate' => ":no_entry_sign: Sorry, you have no rights to post in #announcements!\nIf you want to comment somehow, either start a sub-thread, or write in #general.\nYour message was:\n>>> %s"
    ];
    static $disgustInterval = 60 * 60; // 60m = 60s * 60;
    static $disgustTitles = ['War Thunder', ' BMS', 'Ace Combat', 'WarThunder', 'World of Warplanes', 'HAWX'];
    static $messageTemplatesDisgust = [
        'I am disgusted! How dare you mention %1$s?!?',
        'You have sinned, <@%2$s>! %1$s is Devil\'s work! Go play DCS for at least 2 hours to purge your soul!',
        'No, <@%2$s>, no.. now that\'s a bad word! We don\'t use %1$s in this house!',
        'Well.. at least %1$s doesn\'t crash that often..'
        //todo "At least BMS has good missiles" for bms only
    ];

    static $actionsDefault = [
        ['name' => 'GoTawNet', 'text' => 'TAW', 'url' => 'http://www.taw.net'],
        ['name' => 'GoTawDcsOrg', 'text' => 'TAWDCS', 'url' => 'https://tawdcs.org/'],
        ['name' => 'GoKO', 'text' => 'Kaucasus Offensive', 'url' => 'http://ko.tawdcs.org/'],
        ['name' => 'GoMembersSection', 'text' => 'Members Section', 'url' => 'https://tawdcs.org/sop/'],
        ['name' => 'GoSrsFreqs', 'text' => 'SRS Frequencies', 'url' => 'https://docs.google.com/document/d/1U4fe7EhdJ73F2ojMsXt7yOFxAoHd8OIH1Iv6OuPTSuc/edit'],
        ['name' => 'MandatoryExcuse', 'text' => 'Excuse from Mandatory', 'url' => 'http://taw.net/events/default.aspx?u=3046&c=1']
    ];
    static $actionsDictionary = [
        'EventReporting' => ['text' => 'Event Reporting', 'url' => 'http://taw.net/event/ReportEvent.aspx'],
        'TawDocs' => ['text' => 'Taw GDrive General Folder', 'url' => 'https://drive.google.com/drive/u/1/folders/0BwdUUxV9p95AfjBrT0ExWTFsZjdCTl9oZW5yUmVweTJlNUg4MlVoZDQzX0ExTkt3eVdYd1E'],
        'SlEuDoc' => ['text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit'],
        'MissionDesignDoc' => ['text' => 'TAW Mission Design Doc', 'url' => 'https://drive.google.com/file/d/0B4-zjL-PCMuvc1lHUjhLZWxLRmM/view?usp=sharing'],
        'DcsScriptingWiki' => ['text' => 'DCS Scripting Wiki', 'url' => 'http://wiki.hoggit.us/view/Simulator_Scripting_Engine_Documentation'],
        'MooseDocumentation' => ['text' => 'MOOSE Documentation', 'url' => 'http://flightcontrol-master.github.io/MOOSE/Documentation/index.html'],
        'XoEuRoster' => ['text' => 'XO EU Roster Doc', 'url' => 'https://docs.google.com/spreadsheets/d/14u6v5BJoSroLy0U7QDz2z0IYySbYNT0OpLwe62cteUE/edit#gid=0'],
        'SlBadges' => ['text' => 'SL Badges Doc', 'url' => 'https://docs.google.com/spreadsheets/d/1uyLc6sVwcJnxF9ENj0EjazpsGw8ApBwaDhOF3l6b-WU/edit#gid=459234376'],
        'TawAcademyDrive' => ['text' => 'Taw Academy Docs', 'url' => 'https://drive.google.com/drive/folders/0B9KA0xZYKRz7NmFYOVhMekh3U0E'],
        'BadgeDocTable' => ['text' => 'Badge Doc Table', 'url' => 'https://docs.google.com/spreadsheets/d/1T42d8ktyZ9EM6guqv5oPshuBBLMLRh66UCCAfLazs1U/edit#gid=0']
    ];
    static $actionsPositions = [ //name from $actionsDictionary
        'SL' => ['EventReporting', 'TawDocs'],
        'SL-EU' => ['SlEuDoc'],

        'PLF' => ['TawDocs'],
        'PLF-EU' => ['SlEuDoc'],
        'PLS' => ['TawDocs'],
        'PLS-EU' => ['SlEuDoc'],
        'PLR' => ['TawDocs'],
        'PLR-EU' => ['SlEuDoc'],

        'FS' => ['MissionDesignDoc', 'DcsScriptingWiki', 'MooseDocumentation'],
        'FS-EU' => ['SlEuDoc'],

        'TS' => ['BadgeDocTable'],
        'TS-EU' => ['SlEuDoc'],

        'XO' => ['TawDocs'],
        'XO-EU' => ['SlEuDoc', 'XoEuRoster', 'SlBadges'],

        'CO' => ['TawDocs', 'TawAcademyDrive'],
        'CO-EU' => ['SlEuDoc'],

        'DC' => ['SlEuDoc', 'TawDocs', 'XoEuRoster', 'SlBadges', 'TawAcademyDrive']
    ];
    /*
    static $actionsForRoles = [
        // ----------------------
        'PLF' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit'],
            ['name' => 'TawDocs', 'text' => 'Taw General Folder', 'url' => 'https://drive.google.com/drive/u/1/folders/0BwdUUxV9p95AfjBrT0ExWTFsZjdCTl9oZW5yUmVweTJlNUg4MlVoZDQzX0ExTkt3eVdYd1E']
        ],
        'PLF-EU' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit']
        ],
        'PLS' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit'],
            ['name' => 'TawDocs', 'text' => 'Taw General Folder', 'url' => 'https://drive.google.com/drive/u/1/folders/0BwdUUxV9p95AfjBrT0ExWTFsZjdCTl9oZW5yUmVweTJlNUg4MlVoZDQzX0ExTkt3eVdYd1E']
        ],
        'PLS-EU' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit']
        ],
        'PLR' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit'],
            ['name' => 'TawDocs', 'text' => 'Taw General Folder', 'url' => 'https://drive.google.com/drive/u/1/folders/0BwdUUxV9p95AfjBrT0ExWTFsZjdCTl9oZW5yUmVweTJlNUg4MlVoZDQzX0ExTkt3eVdYd1E']
        ],
        'PLR-EU' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit']
        ],

        // ----------------------
        'FS' => [
            ['name' => 'MissionDesignDoc', 'text' => 'Mission Design Doc', 'url' => 'https://drive.google.com/file/d/0B4-zjL-PCMuvc1lHUjhLZWxLRmM/view?usp=sharing'],
            ['name' => 'DcsScriptingWiki', 'text' => 'DCS Scripting Wiki', 'url' => 'http://wiki.hoggit.us/view/Simulator_Scripting_Engine_Documentation'],
            ['name' => 'MooseDocumentation', 'text' => 'MOOSE Documentation', 'url' => 'http://flightcontrol-master.github.io/MOOSE/Documentation/index.html']
        ],
        'FS-EU' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit']
        ],

        // ----------------------
        'XO' => [
            ['name' => 'TawDocs', 'text' => 'Taw General Folder', 'url' => 'https://drive.google.com/drive/u/1/folders/0BwdUUxV9p95AfjBrT0ExWTFsZjdCTl9oZW5yUmVweTJlNUg4MlVoZDQzX0ExTkt3eVdYd1E']
        ],
        'XO-EU' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit'],
            ['name' => 'XoEuRoster', 'text' => 'XO EU Roster Doc', 'url' => 'https://docs.google.com/spreadsheets/d/14u6v5BJoSroLy0U7QDz2z0IYySbYNT0OpLwe62cteUE/edit#gid=0'],
            ['name' => 'SlBadges', 'text' => 'SL Badges Doc', 'url' => 'https://docs.google.com/spreadsheets/d/1uyLc6sVwcJnxF9ENj0EjazpsGw8ApBwaDhOF3l6b-WU/edit#gid=459234376']
        ],

        // ----------------------
        'CO' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit'],
            ['name' => 'TawDocs', 'text' => 'Taw General Folder', 'url' => 'https://drive.google.com/drive/u/1/folders/0BwdUUxV9p95AfjBrT0ExWTFsZjdCTl9oZW5yUmVweTJlNUg4MlVoZDQzX0ExTkt3eVdYd1E']
        ],
        'CO-EU' => [
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit'],
            ['name' => 'TawAcademyDrive', 'text' => 'Taw Academy Docs', 'url' => 'https://drive.google.com/drive/folders/0B9KA0xZYKRz7NmFYOVhMekh3U0E']
        ],

        // ----------------------
        'DC' => [
            ['name' => 'TawDocs', 'text' => 'Taw General Folder', 'url' => 'https://drive.google.com/drive/u/1/folders/0BwdUUxV9p95AfjBrT0ExWTFsZjdCTl9oZW5yUmVweTJlNUg4MlVoZDQzX0ExTkt3eVdYd1E'],
            ['name' => 'SlEuDoc', 'text' => 'SL EU Meeting Doc', 'url' => 'https://docs.google.com/document/d/1HEQsci_uNBZnQsvNYm8_0VGw6YCOjZcH2V18NNFM_l0/edit'],
            ['name' => 'XoEuRoster', 'text' => 'XO EU Roster Doc', 'url' => 'https://docs.google.com/spreadsheets/d/14u6v5BJoSroLy0U7QDz2z0IYySbYNT0OpLwe62cteUE/edit#gid=0'],
            ['name' => 'SlBadges', 'text' => 'SL Badges Doc', 'url' => 'https://docs.google.com/spreadsheets/d/1uyLc6sVwcJnxF9ENj0EjazpsGw8ApBwaDhOF3l6b-WU/edit#gid=459234376'],
            ['name' => 'TawAcademyDrive', 'text' => 'Taw Academy Docs', 'url' => 'https://drive.google.com/drive/folders/0B9KA0xZYKRz7NmFYOVhMekh3U0E']
        ]
    ];*/

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
                $assoc = json_decode($fileContents, true);
                self::$cache_config = new CachedConfig();
                foreach ($assoc as $key => $value) self::$cache_config->{$key} = $value;
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
