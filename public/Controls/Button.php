<?php
/**
 * Created by PhpStorm.
 * User: Aleph
 * Date: 16-May-17
 * Time: 16:50
 */

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