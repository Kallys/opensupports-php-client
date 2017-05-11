<?php

namespace OpenSupports\Tests\Lib;

use RedBeanPHP\Facade as RedBean;
use OpenSupports\OpenSupports;

abstract class DbTestCase extends \PHPUnit\DbUnit\TestCase
{
	static private $pdo = null;
	private $connection = null;

	static public function setUpBeforeClass() {
		// Setup PDO as in-memory sqlite database
		self::$pdo = new \PDO('sqlite::memory:');
		self::$pdo->exec(file_get_contents(\OpenSupports\Tests\DIR_DATA . 'sqlite.schema.sql'));

		// Setup RedBean BEFORE OpenSupports
		RedBean::setup(self::$pdo);
		RedBean::setAutoResolve(true);

		// Set up OpenSupports
		OpenSupports::setup(\OpenSupports\Tests\DIR_TMP);
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
}
