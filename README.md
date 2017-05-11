opensupports-php-client is a partial client support of [OpenSupports](http://www.opensupports.com/) for PHP projects integration without using API.

It allows user to import this project as a composer dependency and use it like a library to interact with an running instance of OpenSuports server.

## Requirements
* PHP 5.6+
* MySQL 4.1+
* OpenSupports server 0.4b+ (currently based on [51dc76b](https://github.com/opensupports/opensupports/commit/51dc76b3861ff19c4c5ceba606d75589cb0619e6))

## Installation
```
composer config repositories.opensupports git https://github.com/Kallys/opensupports-php-client.git
composer require kallys/opensupports
```

## Features
Currently supported features:
* Ticket creation (except captcha integration)

## Setting up
```
<?php

// Setup OpenSupports to match running instance of OpenSupport server
\OpenSupports\OpenSupports::setup('path/to/opensupports/server/upload/files/', 'localhost', 'opensupports', 'opensupports', 'p4ssw0rd');

// Create ticket with user system disabled
try {
	$ticket = new \OpenSupports\Ticket('My new ticket', 'Ticket test content', 1, 'en', null, 'ano@nymo.us', 'Anonymous');
	echo 'Ticket #' . $ticket->ticketNumber . " successfully created\n";
}
catch(\OpenSupports\Lib\ValidationException $e) {
	// ticket creation failed
}
```
