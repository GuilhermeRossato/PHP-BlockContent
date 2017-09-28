<?php

/*
 * PHP-FlexPart (https://github.com/GuilhermeRossato/PHP-FlexPart)
 * Licensed under the MIT License (https://opensource.org/licenses/MIT)
 */

namespace GuilhermeRossato\FlexPart;

require_once __DIR__ . '/Exceptions.php';

class Block {
	public function __construct($name, $style) {
		$this->name = $name;
		$this->style = (object) array();
		if ($style instanceof \SimpleXMLElement) {
			foreach ($style as $root=>$value) {
				if ($root === "style") {
					foreach ($value as $index=>$value) {
						if ($index == "default" || $index == "desktop" || $index == "tablet" || $index == "mobile" || $index == "custom") {
							$this->style->$index = Block::minifyCssContent((string)$value);
						} else {
							trigger_error("Unhandled style child element \"{$index}\" on block \"{$name}\"", E_USER_ERROR);
						}
					}
				} else {
					echo "Can't handle {$root}<br>";
				}
			}
		}
	}

	public static function minifyCssContent($content) {
		$mode = 0;
		$clean = preg_replace("/(\r\n|\t|\n|\r)/m","", $content);
		$exploded = str_split($clean);
		$filtered = "";
		foreach($exploded as $char) {
			if ($mode == 0 || $mode == 2 || $mode === 1) {
				if ($mode === 1) {
					$mode = 5;
				} elseif ($char == ":") {
					$mode = 1;
				} elseif ($char === "/" && $mode === 0) {
					$mode = 2;
				} elseif ($char === "*" && $mode === 2) {
					$mode = 3;
				} elseif ($mode === 2) {
					$mode = 0;
				}
				if ($char !== ' ') {
					$filtered .= $char;
				}
			} elseif ($mode === 5) {
				if ($char == ";") {
					$mode = 0;
				}
				$filtered .= $char;
			} elseif ($mode === 3 || $mode == 4) {
				if ($char === "*" && $mode === 3) {
					$mode = 4;
				} else if ($char === "/" && $mode === 4) {
					$mode = 0;
					$filtered .= "/";
					continue;
				} else if ($mode === 4) {
					$mode = 3;
				}
				$filtered .= $char;
			}
		};
		return $filtered;
	}
}