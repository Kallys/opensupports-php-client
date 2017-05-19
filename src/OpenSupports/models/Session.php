<?php

class Session {
    static $instance = null;
    private $hive = [];

    private function __construct() {
    	$this->hive = [];
    }

    public function closeSession() {
        $this->hive = [];
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Session();
        }

        return self::$instance;
    }

    public function createSession($userId, $staff = false) {
        $this->store('userId', $userId);
        $this->store('staff', $staff);
        $this->store('token', Hashing::generateRandomToken());
    }

    public function createTicketSession($ticketNumber) {
        $this->store('ticketNumber', $ticketNumber);
        $this->store('token', Hashing::generateRandomToken());
    }

    public function getTicketNumber() {
        return $this->getStoredData('ticketNumber');
    }

    public function getUserId() {
        return $this->getStoredData('userId');
    }

    public function getToken() {
        return $this->getStoredData('token');
    }

    public function sessionExists() {
        return !!$this->getToken();
    }

    public function isStaffLogged() {
        return $this->getStoredData('staff');
    }

    public function store($key, $value) {
    	$this->hive[$key] = $value;
    }

    private function getStoredData($key) {
    	return array_key_exists($key, $this->hive) ? $this->hive[$key] : null;
    }

    public function isLoggedWithId($userId) {
        return ($this->getStoredData('userId') === $userId);
    }
}