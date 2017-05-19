opensupports-php-client is a partial client support of [OpenSupports](https://www.opensupports.com/) for PHP projects integration without using API.

It allows user to import this project as a composer dependency and use it like a library to interact with an installed instance of OpenSuports server.

## Requirements
* PHP 5.6+
* MySQL 4.1+
* Composer

## Installation
Add the following repositories to your composer.json:
```
"repositories": {

    "opensupports-php-client": {
        "type": "git",
        "url": "https://github.com/Kallys/opensupports-php-client.git"
    },
    "opensupports": {
        "type" : "package",
        "package" : {
            "name" : "opensupports/opensupports",
            "version" : "master",
            "source" : {
                "url" : "https://github.com/opensupports/opensupports.git",
                "type" : "git",
                "reference" : "master"
           }
           "autoload" : {
                "classmap" : [
                    "server/controllers/",
                    "server/data/",
                    "server/files/",
                    "server/libs/",
                    "server/models/"
                ],
                "exclude-from-classmap" : [
                    "server/libs/Controller.php",
                    "server/models/DataStore.php",
                    "server/models/Response.php",
                    "server/models/Session.php",
                    "server/libs/DataStoreList.php",
                    "server/libs/Validator.php"
                ]
            }
        }
    }

}
```

Then run:
`composer require kallys/opensupports-php-client`

## Supported features
* /ticket/create Create ticket (except captcha integration)
* /ticket/get Get ticket
* /ticket/close Close ticket
* /user/get-user Get user information
* /user/login Login
* /user/logout Log out

## Usage
Example with disabled user system:
```
<?php

// Setup OpenSupports to match running instance of OpenSupport server
\OpenSupports\OpenSupports::setup('path/to/opensupports/server/upload/files/', 'localhost', 'opensupports', 'opensupports', 'p4ssw0rd');

// Create ticket with user system disabled
try {
	$ticket =  \OpenSupports\Ticket::create('My new ticket title', 'My new ticket content', 1, 'en', 'ano@nymo.us', 'Anonymous');
	echo 'Ticket #' . $ticket->ticketNumber . " successfully created\n";
}
catch(\ValidationException $e) {
	// ticket creation failed
}
```

Example with enabled user system:
```
<?php

// Setup OpenSupports to match running instance of OpenSupport server
$os = \OpenSupports\OpenSupports::setup('path/to/opensupports/server/upload/files/', 'localhost', 'opensupports', 'opensupports', 'p4ssw0rd');

// Log in as normal user
$os->login('customer@opensupports.com', 'customer@opensupports.com');

// Create ticket with user system enabled (will use logged in user)
try {
	$ticket =  \OpenSupports\Ticket::create('My new ticket title', 'My new ticket content', 1, 'en');
	echo 'Ticket #' . $ticket->ticketNumber . " successfully created\n";
}
catch(\Exception $e) {
	// ticket creation failed
}

// Log out
$os->logout();

// ...

// Log in as staff member
$os->login('admin@opensupports.com', 'admin@opensupports.com', true);

// Get an existing ticket and close it
try {
	$ticket = \OpenSupports\Ticket::get(906662);
	$ticket->close();
	echo 'Ticket #' . $ticket->ticketNumber . " successfully closed\n";
}
catch(\Exception $e) {
	// ticket closing failed
}

```

## Technical details
This integration uses composer autoloading to bypass some code from official OpenSupports code.

The motivations behind this hackish integration are:
* Offer a rolling release and version independant support of opensupports
* Official OpenSupports architecture is currently too focused on Controllers (MVC design)

See more [here](https://github.com/opensupports/opensupports/issues/8).

## Development
Running tests:
`./src/lib/vendor/bin/phpunit`
