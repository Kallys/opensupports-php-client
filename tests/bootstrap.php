<?php

namespace OpenSupports\Lib {

	// Override move_uploaded_file for testing purpose
	function move_uploaded_file($filename, $destination)
	{
		return copy($filename, $destination);
	}
}

namespace OpenSupports\Tests {

	define('OpenSupports\Tests\DIR_BASE', realpath(__DIR__) . DIRECTORY_SEPARATOR);

	const
		DIR_DATA = DIR_BASE . 'data' . DIRECTORY_SEPARATOR,
		DIR_LIB = DIR_BASE . 'lib' . DIRECTORY_SEPARATOR,
		DIR_TMP = DIR_BASE . 'tmp' . DIRECTORY_SEPARATOR;

	if(!is_dir(DIR_TMP)) {
		mkdir(DIR_TMP) or die('Unable to create temp directory.');
	}
}