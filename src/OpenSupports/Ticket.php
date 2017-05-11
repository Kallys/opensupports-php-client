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
use Respect\Validation\Validator as DataValidator;
DataValidator::with('OpenSupports\\Lib\\Validations', true);

class Ticket {
    private $title;
    private $content;
    private $departmentId;
    private $language;
    private $ticketNumber;
    private $email;
    private $name;
    private $author;

    public function __get($name)
    {
    	return isset($this->$name) ? $this->$name : null;
    }

    public function __isset($name)
    {
    	return isset($this->$name);
    }

    public function validate(array $data, $user=null) {
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

        if(!Setting::getSetting('user-system-enabled')->getValue()) {
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

    public function __construct(string $title, string $content, int $departmentId, string $language, $author=null, string $email=null, string $name=null) {
        $this->title = $title;
        $this->content = $content;
        $this->departmentId = $departmentId;
        $this->language = $language;
        $this->email = $email;
        $this->name = $name;
        $this->author = $author;

        $this->validate([
        	'title' => $this->title,
        	'content' => $this->content,
        	'departmentId' => $this->departmentId,
        	'language' => $this->language,
        	'email' => $this->email,
        	'name' => $this->name
        ], $author);

        $this->storeTicket();

        if(!Setting::getSetting('user-system-enabled')->getValue()) {
            $this->sendMail();
        }

        Log::createLog('CREATE_TICKET', $this->ticketNumber, $this->author);
    }

    private function storeTicket() {
        $department = Department::getDataStore($this->departmentId);

        $fileUploader = OpenSupports::getInstance()->uploadFile();

        $ticket = new \OpenSupports\Models\Ticket();
        $ticket->setProperties(array(
            'title' => $this->title,
            'content' => $this->content,
            'language' => $this->language,
            'author' => $this->author,
            'department' => $department,
            'file' => ($fileUploader instanceof FileUploader) ? $fileUploader->getFileName() : null,
            'date' => Date::getCurrentDate(),
            'unread' => false,
            'unreadStaff' => true,
            'closed' => false,
            'authorName' => $this->name,
            'authorEmail' => $this->email
        ));

        if(Setting::getSetting('user-system-enabled')->getValue()) {
            $this->author->sharedTicketList->add($ticket);
            $this->author->tickets++;

            $this->email = $this->author->email;
            $this->name = $this->author->name;

            $this->author->store();
        }

        $ticket->store();

        $this->ticketNumber = $ticket->ticketNumber;
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
