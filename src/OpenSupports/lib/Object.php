<?php

namespace OpenSupports\Lib;

abstract class Object {
	protected $data;

	protected function __construct(\DataStore $data) {
		$this->data = $data;
	}

	public function __get($name)
	{
		return $this->data->$name;
	}

	public function __isset($name)
	{
		return isset($this->data->$name);
	}

	protected function updateData() {
		$this->data->updateProperties();
	}
}
