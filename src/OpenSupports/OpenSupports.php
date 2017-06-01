<?php

namespace OpenSupports;

use RedBeanPHP\Facade as RedBean;

class OpenSupports
{
	private static $instance = null;

	private function __construct(string $uploadPath, string $mysqlHost = null, string $mysqlName = null, string $mysqlUser = null, string $mysqlPassword = null) {
		// Set path to OpenSupport files directory
		\FileUploader::getInstance()->setLocalPath($uploadPath);
		\FileDownloader::getInstance()->setLocalPath($uploadPath);

		// RedBean can be setup before setting up OpenSupports
		if(is_null(RedBean::getToolBox())) {
			RedBean::setup('mysql:host=' . $mysqlHost. ';dbname=' . $mysqlName, $mysqlUser, $mysqlPassword);
			RedBean::setAutoResolve(true);
		}
	}

	/**
	 * Setup OpenSupports integration
	 * Configure informations to interact with an installed OpenSupports instance
	 *
	 * @param string $uploadPath Path to installed OpenSupports upload directory (where attachments are saved)
	 * @param string $mysqlHost Hostname of installed OpenSupports mysql database
	 * @param string $mysqlName Database name of installed OpenSupports mysql database
	 * @param string $mysqlUser Username of installed OpenSupports mysql database
	 * @param string $mysqlPassword Password of installed OpenSupports mysql database
	 * @return OpenSupports
	 * @throws \Exception
	 */
	public static function setup(string $uploadPath, string $mysqlHost = null, string $mysqlName = null, string $mysqlUser = null, string $mysqlPassword = null) {
		if(!is_null(self::$instance)) {
			throw new \Exception('Already set up');
		}

		self::$instance = new self($uploadPath, $mysqlHost, $mysqlName, $mysqlUser, $mysqlPassword);
		return self::$instance;
	}

	public static function getInstance() {
		if(is_null(self::$instance)) {
			throw new \Exception('Not yet set up');
		}

		return self::$instance;
	}

	/**
	 * Login as existing user or staff
	 * All further operations will be executed as this logged user
	 *
	 * @see \LoginController
	 *
	 * @param string $email User email
	 * @param string $password User password
	 * @param bool $staff Login a staff user
	 */
	public static function login(string $email, string $password, bool $staff=false) {
		$controller = new \LoginController;
		$response = $controller->call([
			'email' => $email,
			'password' => $password,
			'staff' => $staff,
			'remember' => false
		]);
	}

	/**
	 * Check if a user is currently logged in
	 * @return boolean
	 */
	public static function isUserLogged() {
		return \Controller::isUserLogged();
	}

	/**
	 * Check if a staff member is currently logged in
	 *
	 * @param number $level Staff member level
	 * @return boolean
	 */
	public static function isStaffLogged($level = 1) {
		return \Controller::isStaffLogged($level);
	}

	/**
	 * Logout current logged in user
	 *
	 * @see \LogoutController
	 */
	public static function logout() {
		(new \LogoutController)->call();
	}

	/**
	 * Returns logged in user (User or Staff)
	 *
	 * @see \OpenSupports\Staff::getLoggedUser()
	 * @see \OpenSupports\User::getLoggedUser()
	 *
	 * @return \OpenSupports\Staff|\OpenSupports\User
	 */
	public static function getLoggedUser() {
		if(\Session::getInstance()->isStaffLogged()) {
			return Staff::getLoggedUser();
		} else {
			return User::getLoggedUser();
		}
	}
}