<?php

namespace OpenSupports;

use OpenSupports\Lib\FileUploader;
use OpenSupports\Models\Setting;
use RedBeanPHP\Facade as RedBean;

class OpenSupports
{
	private static $instance = null;
	private $uploadPath = null;

	private function __construct(string $uploadPath, string $mysqlHost = null, string $mysqlName = null, string $mysqlUser = null, string $mysqlPassword = null) {
		$this->uploadPath = $uploadPath;

		// RedBean can be setup before setting up OpenSupports
		if(is_null(RedBean::getToolBox())) {
			RedBean::setup('mysql:host=' . $mysqlHost. ';dbname=' . $mysqlName, $mysqlUser, $mysqlPassword);
			RedBean::setAutoResolve(true);
		}
	}

	public static function setup(string $uploadPath, string $mysqlHost = null, string $mysqlName = null, string $mysqlUser = null, string $mysqlPassword = null) {
		if(!is_null(self::$instance)) {
			throw new \Exception('Already set up');
		}

		self::$instance = new self($uploadPath, $mysqlHost, $mysqlName, $mysqlUser, $mysqlPassword);
	}

	public static function getInstance() {
		if(is_null(self::$instance)) {
			throw new \Exception('Not yet set up');
		}

		return self::$instance;
	}

	public function getUploadPath() {
		return $this->uploadPath;
	}

	public function uploadFile($forceUpload = false) {
		$allowAttachments = Setting::getSetting('allow-attachments')->getValue();

		if(!isset($_FILES['file']) || (!$allowAttachments && !$forceUpload)) return '';

		$maxSize = Setting::getSetting('max-size')->getValue();
		$fileGap = Setting::getSetting('file-gap')->getValue();
		$fileFirst = Setting::getSetting('file-first-number')->getValue();
		$fileQuantity = Setting::getSetting('file-quantity');

		$fileUploader = FileUploader::getInstance();
		$fileUploader->setMaxSize($maxSize);
		$fileUploader->setGeneratorValues($fileGap, $fileFirst, $fileQuantity->getValue());

		if($fileUploader->upload($_FILES['file'])) {
			$fileQuantity->value++;
			$fileQuantity->store();

			return $fileUploader;
		} else {
			throw new Exception(ERRORS::INVALID_FILE);
		}
	}
}