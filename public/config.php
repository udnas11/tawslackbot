<?php

class CachedConfig
{
    public $botKickUser = 'none';
}

class Config
{
    private static $cache_config;

    static $tokenKey = 'TAWSLACKBOT_TOKEN';
    static $apiUrl = 'https://slack.com/api';
    static $channelIds = [
        'announce' => 'C208Z2N4F', // production
        'general' => 'C1SHP4Y4A', // production
        'welcome' => 'C2021K9FA', // production
        'bot_channel' => 'G20JEKWJU' // production
    ];
    static $channelAdminIds = [
        'announce' => 'C208Z2N4F', // production
        '88brefing' => 'GEP2Z0PU2',
        '88announce' => 'GFMEPGWTC',
        'reaper_annon' => "GE6R3J1JS",
        'EUannounce' => 'CKKNFDH4J',
        'NAannounce' => 'GNG6R55TK'
    ];
    static $channelAdminIdsSilent = [
        'reaper_annon' => "GE6R3J1JS"
    ];
    static $channelNoLeaveIds = [
        'announce' => 'C208Z2N4F',
        'EUannounce' => 'CKKNFDH4J',
        'EUgeneral' => 'CKKNHNDC2',
        '88brefing' => 'GEP2Z0PU2',
        '88announce' => 'GFMEPGWTC',
        'NAannounce' => 'GNG6R55TK'
    ];
    static $messageTemplates = [
        //'newUserMessageTemplate' => "Welcome, <@%s>! I hope you'll have lots of fun playing with us!\nBut first, *it is important* that you read <https://docs.google.com/document/d/1KNM5OzEwtb7Dkgpsq-Hse4tTMcP0KMVFY1xk71s3prA|THIS DOCUMENT> first and setup your profile according to our standards!\nAlso, to get access to our super top secret files (documents, skins, etc), register on our specialised website <https://docs.google.com/document/d/1RczQPM9tfxhpm724GxgdYEzqRGBFvnmp_d9fxtrq2PQ/edit?usp=sharing|RIGHT HERE>.\nHave fun!",
        'newUserMessageTemplate' => "Welcome, <@%s>! I hope you'll have lots of fun playing with us!\nBut first, *it is important* that you read <https://docs.google.com/document/d/1KNM5OzEwtb7Dkgpsq-Hse4tTMcP0KMVFY1xk71s3prA|THIS DOCUMENT> first and setup your profile according to our standards!\nHave fun!",
        'warnMessageToAnnounce' => "Non-admin user <@%s> attempted to write a message in <#%s>. Time: <!date^%s^{date_num} {time_secs}|Could Not Parse>. Message: \n>>> %s",
        'warnMessageToAnnouncePrivate' => ":no_entry_sign: Sorry, you have no rights to post in <#%s>!\nIf you want to leave a comment - start a sub-thread.\nYour message was:\n>>> %s"
    ];

    static $actionsDefault = [
        ['name' => 'GoTawNet', 'text' => 'TAW', 'url' => 'http://www.taw.net'],
        ['name' => 'GoTawDcsOrg', 'text' => 'TAWDCS', 'url' => 'https://tawdcs.org/'],
        ['name' => 'GoKO', 'text' => 'Kaucasus Offensive', 'url' => 'http://ko.tawdcs.org/'],
        ['name' => 'GoMembersSection', 'text' => 'Members Section', 'url' => 'https://tawdcs.org/sop/'],
        ['name' => 'GoSrsFreqs', 'text' => 'SRS Frequencies', 'url' => 'https://docs.google.com/document/d/1U4fe7EhdJ73F2ojMsXt7yOFxAoHd8OIH1Iv6OuPTSuc/edit'],
        ['name' => 'MandatoryExcuse', 'text' => 'Excuse from Mandatory', 'url' => 'http://taw.net/events/default.aspx?u=3046&c=1'],
        ['name' => 'EventReporting', 'text' => 'Report an Event', 'url' => 'http://taw.net/event/ReportEvent.aspx']
    ];
    static $actionsDictionary = [
        'TawDocs' => [
            'text' => 'Taw GDrive General Folder',
            'url' => 'https://drive.google.com/drive/u/1/folders/0BwdUUxV9p95AfjBrT0ExWTFsZjdCTl9oZW5yUmVweTJlNUg4MlVoZDQzX0ExTkt3eVdYd1E'],
        'SlEuDoc' => [
            'text' => 'SL EU Meeting Doc',
            'url' => 'https://docs.google.com/document/d/1mxKJ92hCBL6ynj_Nx9ayXb9ipcEYpAqq17lfsPf71Ow/edit'],
        'MissionDesignDoc' => [
            'text' => 'TAW Mission Design Doc',
            'url' => 'https://drive.google.com/file/d/0B4-zjL-PCMuvc1lHUjhLZWxLRmM/view?usp=sharing'],
        'DcsScriptingWiki' => [
            'text' => 'DCS Scripting Wiki',
            'url' => 'http://wiki.hoggit.us/view/Simulator_Scripting_Engine_Documentation'],
        'MooseDocumentation' => [
            'text' => 'MOOSE Documentation',
            'url' => 'http://flightcontrol-master.github.io/MOOSE/Documentation/index.html'],
        'EuRoster' => [
            'text' => 'EU Roster Doc',
            'url' => 'https://docs.google.com/spreadsheets/d/1CqJkhUcTiI8Lljt6-nppnnn1916UCwyV6qPOYw-AVfE/edit#gid=0'],
        'SlOps' => [
            'text' => 'Squadron Leader Ops',
            'url' => 'https://docs.google.com/spreadsheets/d/1uyLc6sVwcJnxF9ENj0EjazpsGw8ApBwaDhOF3l6b-WU/edit#gid=459234376'],
        'TawAcademyDrive' => [
            'text' => 'Taw Academy Docs',
            'url' => 'https://drive.google.com/drive/folders/0B9KA0xZYKRz7NmFYOVhMekh3U0E'],
        'BadgeDocTable' => [
            'text' => 'Badge Doc Table',
            'url' => 'https://docs.google.com/spreadsheets/d/1T42d8ktyZ9EM6guqv5oPshuBBLMLRh66UCCAfLazs1U/edit#gid=0'],
        'TawDcsCommandMeeting' => [
            'text' => 'TAW DCS Command Meeting',
            'url' => 'https://docs.google.com/document/d/126oo1AI3i6-jyJQ2M6bvT41o8Z-JzI2TiloqwwQZKEQ/edit']
    ];
    static $actionsPositions = [ //name from $actionsDictionary
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

        'SO' => ['TawDocs'],
        'SO-EU' => ['SlEuDoc', 'EuRoster', 'SlOps'],

        'XO' => ['TawDocs'],
        'XO-EU' => ['SlEuDoc', 'EuRoster', 'SlOps'],

        'CO' => ['TawDocs', 'TawAcademyDrive', 'TawDcsCommandMeeting'],
        'CO-EU' => ['SlEuDoc', 'EuRoster'],

        'DC' => ['SlEuDoc', 'TawDocs', 'EuRoster', 'SlOps', 'TawAcademyDrive']
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
