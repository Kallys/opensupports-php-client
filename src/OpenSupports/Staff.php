<?php

namespace OpenSupports;

class Staff extends \OpenSupports\Lib\Object {
	/**
	 * Return logged in Staff user
	 *
	 * @see \GetStaffController
	 *
	 * @return \OpenSupports\User
	 */
	public static function getLoggedUser() {
		$staffId = \Session::getInstance()->getUserId();
		$controller = new \GetStaffController;
		$response = $controller->call([
			'staffId' => $staffId
		]);

		return new self(\Staff::getUser($staffId));
	}
}
