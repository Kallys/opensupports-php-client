<?php

namespace OpenSupports\Lib;

abstract class Session {
    public static function getLoggedUser() {
        $session = \OpenSupports\Models\Session::getInstance();

        if ($session->isStaffLogged()) {
            return Staff::getUser($session->getUserId());
        } else {
            return User::getUser($session->getUserId());
        }
    }

    public static function isUserLogged() {
    	$session = \OpenSupports\Models\Session::getInstance();

        return $session->checkAuthentication(array(
            'userId' => Controller::request('csrf_userid'),
            'token' => Controller::request('csrf_token')
        ));
    }
}