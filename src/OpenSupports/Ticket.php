<?php

namespace OpenSupports;

class Ticket extends \OpenSupports\Lib\Object {
	private $csrf_token = null;

    /**
     * Create a new ticket
     * Note: If user system is disabled, a "ticket session" will be created.
     *
     * @see \CreateController
     *
     * @param string $title Ticket title
     * @param string $content Ticket content
     * @param int $departmentId Departement ID of the ticket
     * @param string $language Language of the ticket content
     * @param string $email Email of the ticket author (only when user system is disabled)
     * @param string $name Name of the ticket author (only when user system is disabled)
     * @return \OpenSupports\Ticket
     */
    public static function create(string $title, string $content, int $departmentId, string $language, string $email=null, string $name=null) {
		$controller = new \CreateController;
		$response = $controller->call([
			'title' => $title,
			'content' => $content,
			'departmentId' => $departmentId,
			'language' => $language,
			'email' => $email,
			'name' => $name
		]);

		return self::get($response['ticketNumber'], $email);
    }

    /**
     * Return an existing ticket by its number
     *
     * @see \CheckTicketController and \TicketGetController
     *
     * @param int $ticketNumber ticket number
     * @param string $authorEmail ticket author email (only when user system is disabled)
     * @param string $captcha captach code (only when user system is disabled)
     * @return \OpenSupports\Ticket
     */
    public static function get(int $ticketNumber, string $authorEmail = null, string $captcha = null) {
    	$csrf_token = null;

    	// Without User System we need to 'check' (create a ticket session) before 'get' the ticket
    	if(!\Controller::isUserSystemEnabled() && !\Controller::isStaffLogged()) {
    		$controller = new \CheckTicketController;
    		$response = $controller->call([
    			'ticketNumber' => $ticketNumber,
    			'email' => $authorEmail,
    			'captcha' => $captcha
    		]);
    		$csrf_token = $response['token'];
    	}

    	// Check if request is valid
		$controller = new \TicketGetController;
		$response = $controller->call([
			'ticketNumber' => $ticketNumber,
			'csrf_token' => $csrf_token
		]);

		// Returns the ticket
		$ticket = new self(\Ticket::getByTicketNumber($response['ticketNumber']));
		$ticket->csrf_token = $csrf_token;
		return $ticket;
    }

    /**
     * Close this ticket
     *
     * @see \CloseController
     */
    public function close() {
    	$controller = new \CloseController;
    	$response = $controller->call([
    		'ticketNumber' => $this->ticketNumber,
    		'csrf_token' => $this->csrf_token
    	]);
    	$this->updateData();
    }

    /**
     * Add a comment to this ticket
     *
     * @see \CommentController
     *
     * @param string $content
     */
    public function comment(string $content) {
    	$controller = new \CommentController;
    	$response = $controller->call([
    		'ticketNumber' => $this->ticketNumber,
    		'content' => $content,
    		'csrf_token' => $this->csrf_token
    	]);
    	$this->updateData();
    }
}
