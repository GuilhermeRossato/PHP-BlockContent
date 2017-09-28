<?php

/*
 * PHP-FlexPart (https://github.com/GuilhermeRossato/PHP-FlexPart)
 * Licensed under the MIT License (https://opensource.org/licenses/MIT)
 */

namespace GuilhermeRossato\FlexPart;

require_once __DIR__ . '/Exceptions.php';

final class Controller {

	public $minifyCss = false;
	public $combineCss = false;

	private $rootBlocks = array();
	private $allBlocks = array();

	public function __construct($layoutDir, $cacheDir) {
		if (!is_dir($layoutDir)) {
			throw new FolderNotFoundException($layoutDir);
		}
		if (isset($cacheDir) && !is_dir($cacheDir)) {
			throw new FolderNotFoundException($cacheDir);
		}
		$this->layoutDir = $layoutDir;
		$this->cacheDir = $cacheDir;
	}
	public function getLayout($blockName = 'root') {

	}

	public function generateStyleFile() {
		$defaults = array();
		$grouped = array();
		foreach ($this->allBlocks as $block) {
			foreach ($block->style as $index=>$style) {
				if (!empty($style)) {
					if ($index === "default") {
						$defaults[] = ".{$block->name}{{$style}}";
					} else {
						if (!isset($grouped{$index}) || !is_array($grouped{$index})) {
							$grouped{$index} = array();
						}
						$grouped{$index}[] = array(
							'name' => $block->name,
							'content' => $style
						);
					}
				}
			}
		}
		$fileContent = array();
		$fileContent[] = "/* ALL EDITS ON THIS FILE WILL BE LOST ON STYLE CACHE PURGE */\n\n";
		$fileContent[] = implode("\n",$defaults)."\n";
		foreach ($grouped as $type=>$group) {
			if ($type === "desktop") {
				$fileContent[] = "@media (min-width: 760px) {\n";
			} else if ($type === "tablet") {
				$fileContent[] = "@media (min-width: 560px) and (max-width: 860px) {\n";
			} else if ($type === "mobile") {
				$fileContent[] = "@media (max-width: 560px) {\n";
			} else {
				$fileContent[] = "@media (max-width: 230px) {\n";
			}

			foreach ($group as $index=>$block) {
				$fileContent[] = ".".$block["name"]."{".($block["content"])."}\n";
				//$fileContent .= "block: {$block["name"]}\n";
			}
			$fileContent[] = "}\n";
		}
		echo "File to save: ".$this->cacheDir."/style.css<br>";
		echo getcwd();
		echo "<br>";

		file_put_contents($this->cacheDir."/style.css", $fileContent);
		//echo "<pre>".implode("",$fileContent)."</pre>";
	}

	public function process($hash = '') {
		$files = $this->getDirectories($this->layoutDir);
		$parser = new Parser();
		foreach ($files as $file) {
			$parser->assign($this->layoutDir.$file);
			array_push($this->allBlocks, $parser->parse());
		}
	}
	private function getDirectories($dir) {
		$result = array();
		$cdir = scandir($dir);
		if (!$cdir) {
			throw new CouldNotScanFolderException();
		}
		foreach ($cdir as $key => $value) {
			if (!in_array($value,array(".",".."))) {
				if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
					$result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
				} else {
					$result[] = $value;
				}
			}
		}
		return $result;
	}

	public function getAllBlocks() {
		return $this->allBlocks;
	}
}