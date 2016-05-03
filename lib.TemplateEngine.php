<?php

// credits: Leszek Krupiński @ http://www.programuj.com/artykuly/www/template.php
class Template
{
    var $file;
    var $data;

    function Template($name)
    {
        $this->file = implode('', file($name));
        $this->data = Array();
    }

    function add($name, $value = '')
    {
        if (is_array($name)) {
            $this->data = array_merge($this->data, $name);
        } else {
            $this->data[$name] = $value;
        }
    }

    function execute()
    {
        return preg_replace_callback('/{([^}]+)}/', function ($matches) {
            return $this->data[$matches[1]];
        }, $this->file);
    }
}