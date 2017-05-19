<?php

abstract class Response {
	private static $data = null;

    public static function respondError($errorMsg, $data = null) {
    	self::$data = null;
    	throw new \Exception($errorMsg);
    }

    public static function respondSuccess($data = null) {
    	self::$data = $data;
    }

    public static function getResponse() {
    	return self::$data;
    }
}
