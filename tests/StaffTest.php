<?php

namespace OpenSupports\Tests;

use OpenSupports\OpenSupports;
use OpenSupports\Staff;

class StaffTest extends \OpenSupports\Tests\Lib\DbTestCase
{
	public function testGetLoggedUserWithUserSystemEnabled() {
		// Enable user system
		$this->setUserSystemEnabled(true);

		// Login as user
		$this->login(true);

		// Get logged in user
		$this->assertEquals('Administrator', Staff::getLoggedUser()->name);

		// Assert no user is logged in
		$this->logout();

		// Get logged in user
		$this->expectExceptionMessage(\ERRORS::NO_PERMISSION);
		Staff::getLoggedUser();
	}

	public function testGetLoggedUserAsUserWithUserSystemEnabled() {
		// Enable user system
		$this->setUserSystemEnabled(true);

		// Login as user
		$this->login();

		// Get logged in user
		$this->expectExceptionMessage(\ERRORS::NO_PERMISSION);
		Staff::getLoggedUser();
	}

	public function testGetLoggedUserAsStaffWithUserSystemDisabled() {
		// Disable user system
		$this->setUserSystemEnabled(false);

		// Login as staff
		$this->login(true);

		// Get logged in user
		$this->assertEquals('Administrator', Staff::getLoggedUser()->name);
	}
}
