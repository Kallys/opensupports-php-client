<?php

namespace OpenSupports\Tests;

use OpenSupports\OpenSupports;
use OpenSupports\User;

class UserTest extends \OpenSupports\Tests\Lib\DbTestCase
{
	public function testGetUserWithUserSystemEnabled() {
		// Enable user system
		$this->setUserSystemEnabled(true);

		// Login as staff
		$this->login(true);

		// Get a valid user
		$user = User::get(1);
		$this->assertEquals('Customer', $user->name);

		// Get an invalid user
		$this->expectExceptionMessage(\ERRORS::INVALID_USER);
		User::get(2);
	}

	public function testGetUserNotPermittedWithUserSystemEnabled() {
		// Enable user system
		$this->setUserSystemEnabled(true);

		// Login as user
		$this->login();

		// Get a valid user
		$this->expectExceptionMessage(\ERRORS::NO_PERMISSION);
		$user = User::get(1);
	}

	public function testLoginWithUserSystemDisabled() {
		// Disable user system
		$this->setUserSystemEnabled(false);

		// Login as staff
		$this->login(true);

		// Get a valid user
		$this->expectExceptionMessage(\ERRORS::USER_SYSTEM_DISABLED);
		$user = User::get(1);
	}

	public function testGetLoggedUserWithUserSystemEnabled() {
		// Enable user system
		$this->setUserSystemEnabled(true);

		// Login as user
		$this->login();

		// Get logged in user
		$this->assertEquals('Customer', User::getLoggedUser()->name);

		// Assert no user is logged in
		$this->logout();

		// Get logged in user
		$this->expectExceptionMessage(\ERRORS::NO_PERMISSION);
		User::getLoggedUser();
	}

	public function testGetLoggedUserAsStaffWithUserSystemEnabled() {
		// Enable user system
		$this->setUserSystemEnabled(true);

		// Login as staff
		$this->login(true);

		// Get logged in user
		$this->expectExceptionMessage(\ERRORS::INVALID_CREDENTIALS);
		User::getLoggedUser();
	}

	public function testGetLoggedUserAsStaffWithUserSystemDisabled() {
		// Disable user system
		$this->setUserSystemEnabled(false);

		// Login as staff
		$this->login(true);

		// Get logged in user
		$this->expectExceptionMessage(\ERRORS::INVALID_CREDENTIALS);
		User::getLoggedUser();
	}
}
