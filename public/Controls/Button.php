<?php
/**
 * Created by PhpStorm.
 * User: Aleph
 * Date: 16-May-17
 * Time: 16:50
 */
require_once "Base.php";

/*		$button["name"] = "GoTawNet";
		$button["text"] = "TAW Page";
		$button["type"] = "button";
		$button["value"] = "http://www.taw.net";
		$att["actions"][] = $button;*/

class Button
{
    public $name;
    public $text;
    public $value;
    public $type = 'button';


    public function __construct($name, $text, $value)
    {
        $this->name = $name;
        $this->text = $text;
        $this->value = $value;
    }

    public function ToArray()
    {
        $obj['name'] = $this->name;
        $obj['text'] = $this->text;
        $obj['value'] = $this->value;
        $obj['type'] = 'button';
        return $obj;
    }
}