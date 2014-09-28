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

trait TJsonDecode {
    protected static $json_decode_fields = [];

    protected function decode_json_fields($json) {
        $json = static::process_json($json);
        foreach(static::get_json_decode_fields() as $k => $v) {
            if(!property_exists($json, $k)) continue;
            $v = $v->bindTo($this, $this);
            $v($json->$k);
        }
        return $json;
    }

    public static function add_json_decode_field($name, $callback) {
        static::$json_decode_fields[get_called_class()][$name] = $callback;
    }

    protected static function get_json_decode_fields() {
        $class  = get_called_class();
        $fields = [];
        $parent = get_parent_class($class);
        if($parent) $fields = array_merge($fields, $parent::get_json_decode_fields());
        if(array_key_exists($class, static::$json_decode_fields))
            $fields = array_merge($fields, static::$json_decode_fields[$class]);
        return $fields;
    }

    protected static function process_json($json) {
        //Treat objects as decoded JSON & just return
        if(is_object($json))
            return $json;
        //Can't decode anything but a string, so throw on non-string
        if(!is_string($json))
            throw new InvalidArgumentException('decode_json_fields() expects a literal JSON string, a path to a JSON file or an already decoded object');
        //If the string seems to be a file path, load the file
        if(file_exists($json))
            $json = file_get_contents($json);
        //Attempt to decode the JSON string & throw on any errors
        $json = json_decode($json);
        if(json_last_error() != JSON_ERROR_NONE)
            throw new Exception('Failed to decode JSON with error: '.json_last_error_msg());
        //Everything seemed to go ok; return decoded object
        return $json;
    }
}

?>
