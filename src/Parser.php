<?php

/*
 * PHP-FlexPart (https://github.com/GuilhermeRossato/PHP-FlexPart)
 * Licensed under the MIT License (https://opensource.org/licenses/MIT)
 */

namespace GuilhermeRossato\FlexPart;

require_once __DIR__ . '/Exceptions.php';

final class Parser {

	//public fileSizeLimit = 1048576;

	public function __construct() {
		$this->fileSizeLimit = 1048576;
	}

	public function assign($file) {
		$this->file = $file;
	}

	public function assert($condition, $errMessage = '') {
		if (!$condition) {
			throw new InvalidBlockException($errMessage." - Error while parsing {$this->file}");
		}
	}

	public function assertBlock($b) {
		$name = (string)$b->name;
		$this->assert(!empty($name), "Block name must be defined");
		$this->assert(is_string($name), "Block name must be a string");
		$this->assert(!preg_match('/[^A-Za-z0-9\-\.]/', $name), "The only characters allowed on a block name are letters, numbers, commas and dots");
	}

	public function extractNameFromFile() {
		return pathinfo($this->file, PATHINFO_FILENAME);
	}

	public function parse() {
		$content = file_get_contents($this->file, null, null, null, $this->fileSizeLimit);
		if (!$content) {
			throw new CouldNotOpenFileException($file);
		}
		libxml_use_internal_errors(true);
		$object = simplexml_load_string($content, null, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG | LIBXML_NONET);
		if (!$object) {
			throw new InvalidBlockException("Invalid XML structure for block for {$this->file}");
		}
		$object->fileName = $this->file;
		if (!isset($object->name) || empty((string)$object->name))
			$object->name = $this->extractNameFromFile($this->file);
		$this->assertBlock($object);
		$block = new Block((string) $object->name, $object->style);
		return $block;
	}
}