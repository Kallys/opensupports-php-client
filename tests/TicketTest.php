<?php

namespace OpenSupports\Tests;

use OpenSupports\Ticket;
use OpenSupports\Models\User;
use OpenSupports\Models\Setting;
use OpenSupports\Lib\FileUploader;
use OpenSupports\OpenSupports;

class TicketTest extends \OpenSupports\Tests\Lib\DbTestCase
{
	public function testCreationWithUserSystemEnabled() {
		// Enable user system
		$userSystemEnabled = Setting::getSetting('user-system-enabled');
		$userSystemEnabled->value = 1;
		$userSystemEnabled->store();

		$this->createWithRegisteredUser();
		$this->createWithAttachment();

		$this->expectException(\OpenSupports\Lib\ValidationException::class);
		$this->createWithUserInfos();
	}

	public function testCreationWithUserSystemDisabled() {
		// Disable user system
		$userSystemEnabled = Setting::getSetting('user-system-enabled');
		$userSystemEnabled->value = 0;
		$userSystemEnabled->store();

		$this->createWithUserInfos();

		$this->expectException(\OpenSupports\Lib\ValidationException::class);
		$this->createWithRegisteredUser();
	}

	// Create ticket with registered user
	private function createWithRegisteredUser() {
		$ticket = new Ticket('Ticket from registered', 'Ticket test content', 1, 'en', User::getUser(1));
		$this->assertEquals(1, $this->getConnection()->getRowCount('ticket', 'ticket_number = ' . $ticket->ticketNumber));
	}

	// Create ticket with user infos
	private function createWithUserInfos() {
		$ticket = new Ticket('Ticket from unknown', 'Ticket test content', 1, 'en', null, 'ano@nymo.us', 'Anonymous');
		$this->assertEquals(1, $this->getConnection()->getRowCount('ticket', 'ticket_number = ' . $ticket->ticketNumber));
	}

	// Create a ticket with an attachment (and registered user)
	private function createWithAttachment() {
		// Mock $_FILES
		$_FILES = array(
			'file' => array(
				'name' => basename(__FILE__),
				'type' => 'text/plain',
				'size' => filesize(__FILE__),
				'tmp_name' => __FILE__,
				'error' => 0
			)
		);

		// Create ticket with attachment (mocked $_FILES)
		$this->createWithRegisteredUser();

		// Retrieve uploaded file infos
		$fileUploader = FileUploader::getInstance();
		$attachment = $fileUploader->getLocalPath() . $fileUploader->getFileName();
		$this->assertTrue(is_file($attachment));

		// Clean up
		unlink($attachment);
		unset($_FILES);
	}
}
