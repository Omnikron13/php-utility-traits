<?php

/*
 Copyright (c) 2014 Joey Sabey

 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:

 The above copyright notice and this permission notice shall be included in all
 copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 SOFTWARE.
*/

//Trait providing a flexible implementation of the JsonSerializable interface
trait TJsonFields {
    protected static $json_fields = [];

    public function jsonSerialize() {
        return array_map(function($callback) {
            $callback = $callback->bindTo($this, $this);
            return $callback();
        }, static::get_json_fields());
    }

    public static function add_json_field($name, $callback) {
        static::$json_fields[get_called_class()][$name] = $callback;
    }

    protected static function get_json_fields() {
        $class  = get_called_class();
        $fields = [];
        $parent = get_parent_class($class);
        if($parent) $fields = array_merge($fields, $parent::get_json_fields());
        if(array_key_exists($class, static::$json_fields))
            $fields = array_merge($fields, static::$json_fields[$class]);
        return $fields;
    }
}

?>
