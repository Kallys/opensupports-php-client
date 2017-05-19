<?php

abstract class Controller {
    private static $requestData;

    /**
     * Instance-related stuff
    */
    abstract public function handler();
    abstract public function validations();

    public function call(array $data=null) {
    	self::$requestData = $data;
    	$this->validate();
    	$this->handler();
    	return Response::getResponse();
    }

    public function validate() {
        $validator = new Validator();
        $validator->validate($this->validations());
    }

    public static function request($key, $secure = false) {
    	return array_key_exists($key, self::$requestData) ? self::$requestData[$key] : null;
    }

    public static function getLoggedUser() {
        $session = Session::getInstance();

        if ($session->isStaffLogged()) {
            return Staff::getUser($session->getUserId());
        } else {
            return User::getUser($session->getUserId());
        }
    }

    public static function isUserLogged() {
        return Session::getInstance()->sessionExists();
    }

    public static function isStaffLogged($level = 1) {
        return Controller::isUserLogged() && (Controller::getLoggedUser()->level >= $level);
    }

    public function uploadFile($forceUpload = false) {
        $allowAttachments = Setting::getSetting('allow-attachments')->getValue();

        if(!isset($_FILES['file']) || (!$allowAttachments && !$forceUpload)) return '';

        $maxSize = Setting::getSetting('max-size')->getValue();
        $fileGap = Setting::getSetting('file-gap')->getValue();
        $fileFirst = Setting::getSetting('file-first-number')->getValue();
        $fileQuantity = Setting::getSetting('file-quantity');

        $fileUploader = FileUploader::getInstance();
        $fileUploader->setMaxSize($maxSize);
        $fileUploader->setGeneratorValues($fileGap, $fileFirst, $fileQuantity->getValue());

        if($fileUploader->upload($_FILES['file'])) {
            $fileQuantity->value++;
            $fileQuantity->store();

            return $fileUploader;
        } else {
            throw new Exception(ERRORS::INVALID_FILE);
        }
    }

    public static function isUserSystemEnabled() {
        return Setting::getSetting('user-system-enabled')->getValue();
    }
}