<?php

namespace OpenSupports;

class User extends \OpenSupports\Lib\Object {
    /**
     * Return an existing user by its ID.
     *
     * @see \GetUserByIdController
     *
     * @param int $userId user ID to get
     * @return \OpenSupports\User
     */
    public static function get(int $userId) {
    	$controller = new \GetUserByIdController;
		$response = $controller->call([
			'userId' => $userId
		]);

		return new self(\User::getDataStore($userId));
    }

    /**
     * Return logged in User
     *
     * @see \GetUserController
     *
     * @return \OpenSupports\User
     */
    public static function getLoggedUser() {
    	$controller = new \GetUserController;
    	$response = $controller->call();

    	return new self(\User::getUser(\Session::getInstance()->getUserId()));
    }
}
