<?php

namespace OpenSupports\Lib;

use Respect\Validation\Validator as DataValidator;
use OpenSupports\OpenSupports;
use OpenSupports\Models\User;
use OpenSupports\Models\Staff;
use OpenSupports\Data\ERRORS;

class ValidationException extends \Exception {}

class Validator {

	public function validate($config, array $data, $user=null) {
		$this->validatePermissions($config['permission'], $user);
        $this->validateAllRequestData($config['requestData'], $data);
    }

    public static function isValidUser($user=null) {
    	return is_a($user, User::class) || is_a($user, Staff::class);
    }

    public static function isStaffUser($staff=null, $level = 1) {
    	return is_a($staff, Staff::class) && ($staff->level >= $level);
    }

    private function validatePermissions($permission, $user=null) {
        $permissions = [
            'any' => true,
        	'user' => self::isValidUser($user),
        	'staff_1' => self::isStaffUser($user, 1),
        	'staff_2' => self::isStaffUser($user, 2),
        	'staff_3' => self::isStaffUser($user, 3)
        ];

        if (!$permissions[$permission]) {
            throw new ValidationException(ERRORS::NO_PERMISSION);
        }
    }

    private function validateAllRequestData($requestDataValidations, $data) {
        foreach ($requestDataValidations as $requestDataKey => $requestDataValidationConfig) {
        	$requestDataValue = $data[$requestDataKey];
            $requestDataValidator = $requestDataValidationConfig['validation'];
            $requestDataValidationErrorMessage = $requestDataValidationConfig['error'];

            $this->validateData($requestDataValue, $requestDataValidator, $requestDataValidationErrorMessage);
        }
    }

    private function validateData($value, DataValidator $dataValidator, $error) {
        if (!$dataValidator->validate($value)) {
            throw new ValidationException($error);
        }
    }

}