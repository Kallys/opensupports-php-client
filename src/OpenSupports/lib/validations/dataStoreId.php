<?php

namespace OpenSupports\Lib\Validations;

use Respect\Validation\Rules\AbstractRule;
use OpenSupports\OpenSupports;

class DataStoreId extends AbstractRule {
    private $dataStoreName;

    public function __construct($dataStoreName = '') {
        if ($this->isDataStoreNameValid($dataStoreName)) {
            $this->dataStoreName = $dataStoreName;
        } else {
            throw new \Exception("Invalid DataStore: $dataStoreName");
        }
    }

    public function validate($dataStoreId) {
        $dataStore = null;

        switch ($this->dataStoreName) {
            case 'user':
            	$dataStore = \OpenSupports\Models\User::getUser($dataStoreId);
                break;
            case 'staff':
            	$dataStore = \OpenSupports\Models\Staff::getUser($dataStoreId);
                break;
            case 'ticket':
            	$dataStore = \OpenSupports\Models\Ticket::getTicket($dataStoreId);
                break;
            case 'department':
                $dataStore = \OpenSupports\Models\Department::getDataStore($dataStoreId);
                break;
            case 'customresponse':
            	$dataStore = \OpenSupports\Models\CustomResponse::getDataStore($dataStoreId);
                break;
            case 'topic':
            	$dataStore = \OpenSupports\Models\Topic::getDataStore($dataStoreId);
                break;
            case 'article':
            	$dataStore = \OpenSupports\Models\Article::getDataStore($dataStoreId);
                break;
        }

        return !$dataStore->isNull();
    }

    private function isDataStoreNameValid($dataStoreName) {
        return in_array($dataStoreName, [
            'user',
            'staff',
            'ticket',
            'department',
            'customresponse',
            'topic',
            'article'
        ]);
    }
}