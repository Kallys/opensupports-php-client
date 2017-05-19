<?php

namespace OpenSupports\Tests;

use OpenSupports\Ticket;
use OpenSupports\OpenSupports;

class TicketTest extends \OpenSupports\Tests\Lib\DbTestCase
{
	public function testGetTicketWithUserSystemEnabled() {
		// Enable user system
		$this->setUserSystemEnabled(true);

		// Login as user
		$this->login();

		// Get a valid ticket
		$ticket = Ticket::get(906662);
		$this->assertEquals('Customer', $ticket->author->name);

		// Logout
		$this->logout();

		// Get a valid ticket
		$this->expectExceptionMessage(\ERRORS::NO_PERMISSION);
		$ticket = Ticket::get(906662);
	}

	public function testWithUserSystemDisabled() {
		// Disable user system
		$this->setUserSystemEnabled(false);

		// Create a new ticket
		$ticket = $this->createWithUserInfos();

		// Get this ticket
		Ticket::get($ticket->ticketNumber, $ticket->author_email);

		// Get this ticket with invalid email
		$this->expectExceptionMessage(\ERRORS::NO_PERMISSION);
		Ticket::get($ticket->ticketNumber, 'invalid@email.com');
	}

	public function testCreationWithUserSystemEnabled() {
		// Enable user system
		$this->setUserSystemEnabled(true);

		// Login as user
		$this->login();

		// Create new tickets
		$this->createWithRegisteredUser();
		$this->createWithUserInfos();
		$this->createWithAttachment();

		// Logout
		$this->logout();

		// Create a new ticket
		$this->expectExceptionMessage(\ERRORS::NO_PERMISSION);
		$this->createWithRegisteredUser();
	}

	public function testCreationWithUserSystemDisabled() {
		// Disable user system
		$this->setUserSystemEnabled(false);

		// Create a new ticket
		$this->createWithUserInfos();

		// Create a new ticket
		$this->expectExceptionMessage(\ERRORS::INVALID_EMAIL);
		$this->createWithRegisteredUser();
	}

	public function testClose() {
		// Enable user system
		$this->setUserSystemEnabled(true);

		// Login as user
		$this->login();

		// Create new tickets
		$ticket = $this->createWithRegisteredUser();

		$ticket->close();
		$this->assertTrue((bool)$ticket->closed);
	}

	// Create ticket with registered user
	private function createWithRegisteredUser() {
		$ticket = Ticket::create('Ticket from registered', 'Ticket test content', 1, 'en');
		$this->assertEquals(1, $this->getConnection()->getRowCount('ticket', 'ticket_number = ' . $ticket->ticketNumber));
		return $ticket;
	}

	// Create ticket with user infos
	private function createWithUserInfos() {
		$ticket = Ticket::create('Ticket from unknown', 'Ticket test content', 1, 'en', 'ano@nymo.us', 'Anonymous');
		$this->assertEquals(1, $this->getConnection()->getRowCount('ticket', 'ticket_number = ' . $ticket->ticketNumber));
		return $ticket;
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
		$ticket = $this->createWithRegisteredUser();
		unset($_FILES);
		$this->assertNotEmpty($ticket->file);
	}
}
