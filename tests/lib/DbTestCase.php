<?php

namespace OpenSupports\Tests\Lib;

use RedBeanPHP\Facade as RedBean;
use OpenSupports\OpenSupports;

abstract class DbTestCase extends \PHPUnit\DbUnit\TestCase
{
	private static $pdo = null;
	private $connection = null;

	public static function setUpBeforeClass() {
		if(is_null(self::$pdo)) {
			// Setup an in-memory sqlite database
			RedBean::setup('sqlite::memory:');
			RedBean::setAutoResolve(true);

			// Load schema
			self::$pdo = RedBean::getPDO();
			self::$pdo->exec(file_get_contents(\OpenSupports\Tests\DIR_DATA . 'sqlite.schema.sql'));

			// Set up OpenSupports (IMPORTANT: after database setup)
			OpenSupports::setup(\OpenSupports\Tests\DIR_TMP);
		}
	}

	final public function getConnection()
	{
		if(is_null($this->connection)) {
			$this->connection = $this->createDefaultDBConnection(self::$pdo, ':memory:');
		}
		return $this->connection;
	}

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(\OpenSupports\Tests\DIR_DATA . 'mysql.data.xml');
	}

	// Enable or disable user system (without applying hooks)
	protected function setUserSystemEnabled(bool $enable=true) {
		if(!$enable && OpenSupports::isUserLogged()) {
			$this->logout();
		}

		$userSystemEnabled = \Setting::getSetting('user-system-enabled');
		$userSystemEnabled->value = (int)$enable;
		$userSystemEnabled->store();
	}

	protected function login(bool $staff=false) {
		// A session already exists, check if it is the one we want
		if(OpenSupports::isUserLogged()) {
			// We want to login with userId = 1 (avoid ticket sessions)
			if(\Session::getInstance()->getUserId() == 1) {
				// Existing session is already staff: do nothing
				if(OpenSupports::isStaffLogged(3)) {
					if($staff) return;
				}
				// Existing session is already user: do nothing
				else {
					if(!$staff) return;
				}
			}

			// Otherwise, do not preserve existing session
			OpenSupports::logout();
		}
		// Do login
		$staff ? OpenSupports::login('admin@opensupports.com', 'admin@opensupports.com', true) : OpenSupports::login('customer@opensupports.com', 'customer@opensupports.com');
	}

	protected function logout() {
		OpenSupports::logout();
	}
}
