<?php

namespace OpenSupports;

use OpenSupports\Data\ERRORS;
use OpenSupports\Lib\Date;
use OpenSupports\Lib\FileUploader;
use OpenSupports\Lib\MailSender;
use OpenSupports\Lib\Validator;
use OpenSupports\Models\Department;
use OpenSupports\Models\Language;
use OpenSupports\Models\Log;
use OpenSupports\Models\MailTemplate;
use OpenSupports\Models\Setting;
use OpenSupports\Models\Ticket as MTicket;
use Respect\Validation\Validator as DataValidator;
use OpenSupports\Models\User;
DataValidator::with('OpenSupports\\Lib\\Validations', true);

class Ticket extends \OpenSupports\Lib\Object {
    private $mTicket;

    public function __get($name)
    {
    	return $this->mTicket->$name;
    }

    public static function create(string $title, string $content, int $departmentId, string $language, int $authorId=null, string $email=null, string $name=null) {
    	$mAuthor = is_null($authorId) ? null : User::getUser($authorId);
    	self::validateCreation([
    		'title' => $title,
    		'content' => $content,
    		'departmentId' => $departmentId,
    		'language' => $language,
    		'email' => $email,
    		'name' => $name
    	], $mAuthor);

    	$ticket = new self(self::createTicket($title, $content, $departmentId, $language, $mAuthor, $email, $name));

    	if(!Setting::getSetting('user-system-enabled')->getValue()) {
    		$ticket->sendMail();
    	}

    	Log::createLog('CREATE_TICKET', $ticket->ticketNumber, $mAuthor);

    	return $ticket;
    }

    public static function get(int $ticketNumber) {
    	return new self(\OpenSupports\Models\Ticket::getByTicketNumber($ticketNumber));
    }

    public function close() {

    }

    private function __construct(MTicket $mTicket) {
    	$this->mTicket = $mTicket;
    }

    private static function validateCreation(array $data, $user=null) {
    	$validations = [
    		'permission' => 'user',
    		'requestData' => [
    			'title' => [
    				'validation' => DataValidator::length(10, 200),
    				'error' => ERRORS::INVALID_TITLE
    			],
    			'content' => [
    				'validation' => DataValidator::length(10, 5000),
    				'error' => ERRORS::INVALID_CONTENT
    			],
    			'departmentId' => [
    				'validation' => DataValidator::dataStoreId('department'),
    				'error' => ERRORS::INVALID_DEPARTMENT
    			],
    			'language' => [
    				'validation' => DataValidator::in(Language::getSupportedLanguages()),
    				'error' => ERRORS::INVALID_LANGUAGE
    			]
    		]
    	];

    	if(!self::isUserSystemEnabled()) {
    		$validations['permission'] = 'any';
    		/*$validations['requestData']['captcha'] = [
    		 'validation' => DataValidator::captcha(),
    		 'error' => ERRORS::INVALID_CAPTCHA
    		 ];*/

    		$validations['requestData']['name'] = [
    			'validation' => DataValidator::length(2, 55),
    			'error' => ERRORS::INVALID_NAME
    		];

    		$validations['requestData']['email'] = [
    			'validation' => DataValidator::email(),
    			'error' => ERRORS::INVALID_EMAIL
    		];
    	}

    	$validator = new Validator();
    	$validator->validate($validations, $data, $user);
    }

    private function validateGet() {
    	if (Controller::isUserSystemEnabled() || Controller::isStaffLogged()) {
    		return [
    			'permission' => 'user',
    			'requestData' => [
    				'ticketNumber' => [
    					'validation' => DataValidator::validTicketNumber(),
    					'error' => ERRORS::INVALID_TICKET
    				]
    			]
    		];
    	} else {
    		return [
    			'permission' => 'any',
    			'requestData' => [
    				'ticketNumber' => [
    					'validation' => DataValidator::equals($session->getTicketNumber()),
    					'error' => ERRORS::INVALID_TICKET
    				]
    			]
    		];
    	}
    }

    private static function createTicket(string $title, string $content, int $departmentId, string $language, $mAuthor=null, string $email=null, string $name=null) {
        $mDepartment = Department::getDataStore($departmentId);
        $fileUploader = OpenSupports::getInstance()->uploadFile();

        $mTicket = new \OpenSupports\Models\Ticket();
        $mTicket->setProperties(array(
        	'title' => $title,
        	'content' => $content,
        	'language' => $language,
        	'author' => $mAuthor,
        	'department' => $mDepartment,
        	'file' => ($fileUploader instanceof FileUploader) ? $fileUploader->getFileName() : null,
        	'date' => Date::getCurrentDate(),
        	'unread' => false,
        	'unreadStaff' => true,
        	'closed' => false,
        	'authorName' => $name,
        	'authorEmail' => $email
        ));

        if(self::isUserSystemEnabled()) {
        	$mAuthor->sharedTicketList->add($mTicket);
        	$mAuthor->tickets++;

        	$email = $mAuthor->email;
        	$name = $mAuthor->name;

        	$mAuthor->store();
        }

        $mTicket->store();

        return $mTicket;
    }

    private function sendMail() {
        $mailSender = new MailSender();

        $mailSender->setTemplate(MailTemplate::TICKET_CREATED, [
            'to' => $this->email,
            'name' => $this->name,
            'ticketNumber' => $this->ticketNumber,
            'title' => $this->title,
            'url' => Setting::getSetting('url')->getValue()
        ]);

        $mailSender->send();
    }
}
