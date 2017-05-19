<?php

namespace OpenSupports\Tests;

use OpenSupports\OpenSupports;

class OpenSupportsTest extends \OpenSupports\Tests\Lib\DbTestCase
{
	public function testLoginWithUserSystemEnabled() {
		// Enable user system
		$this->setUserSystemEnabled(true);
		$os = OpenSupports::getInstance();

		// By default, no user is logged
		$this->assertFalse($os->isUserLogged());
		$this->assertFalse($os->isStaffLogged());

		// Login as user
		$os->login('customer@opensupports.com', 'customer@opensupports.com');
		$this->assertTrue($os->isUserLogged());
		$this->assertFalse($os->isStaffLogged());

		// Logout
		$os->logout();
		$this->assertFalse($os->isUserLogged());
		$this->assertFalse($os->isStaffLogged());

		// Login as staff
		$os->login('admin@opensupports.com', 'admin@opensupports.com', true);
		$this->assertTrue($os->isUserLogged());
		$this->assertTrue($os->isStaffLogged());
		$os->logout();

		// Login staff as user
		$this->expectExceptionMessage(\ERRORS::INVALID_CREDENTIALS);
		$os->login('admin@opensupports.com', 'admin@opensupports.com');
	}

	public function testLoginUserAsStaffWithUserSystemEnabled() {
		// Enable user system
		$this->setUserSystemEnabled(true);
		$os = OpenSupports::getInstance();

		// Login user as staff
		$this->expectExceptionMessage(\ERRORS::INVALID_CREDENTIALS);
		$os->login('customer@opensupports.com', 'customer@opensupports.com', true);
	}

	public function testUserLoginWithUserSystemDisabled() {
		// Disable user system
		$this->setUserSystemEnabled(false);
		$os = OpenSupports::getInstance();

		// By default, no user is logged
		$this->assertFalse($os->isUserLogged());
		$this->assertFalse($os->isStaffLogged());

		// Login as user
		$this->expectExceptionMessage(\ERRORS::USER_SYSTEM_DISABLED);
		$os->login('customer@opensupports.com', 'customer@opensupports.com');
	}

	public function testStaffLoginWithUserSystemDisabled() {
		// Disable user system
		$this->setUserSystemEnabled(false);
		$os = OpenSupports::getInstance();

		// Login as staff
		$os->login('admin@opensupports.com', 'admin@opensupports.com', true);
		$this->assertTrue($os->isUserLogged());
		$this->assertTrue($os->isStaffLogged());
		$os->logout();
	}

	public function testMultiLogin() {
		// Enable user system
		$this->setUserSystemEnabled(true);
		$os = OpenSupports::getInstance();

		$os->login('admin@opensupports.com', 'admin@opensupports.com', true);

		$this->expectExceptionMessage(\ERRORS::SESSION_EXISTS);
		$os->login('admin@opensupports.com', 'admin@opensupports.com', true);
	}
}
