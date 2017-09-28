<?php

/*
 * PHP-FlexPart (https://github.com/GuilhermeRossato/PHP-FlexPart)
 * Licensed under the MIT License (https://opensource.org/licenses/MIT)
 */

namespace GuilhermeRossato\FlexPart;

require_once __DIR__ . '/Exceptions.php';

final class PageBuilder {

	private $metas = array(
			'<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">',
			'<meta name="viewport" content="width=device-width, initial-scale=1.0">',
			'<meta http-equiv="Content-Type" content="text/html; charset=utf-8">',
			'<meta name="description" content="Internal Page">',
			'<meta name="keywords" content="intranet internal page">',
			'<meta name="robots" content="INDEX,FOLLOW">'
		);
	private $styles = array();
	private $scripts = array();
	private $name = "unnamed page";

	public function __construct($name) {
		$this->name = $name;
		$this->lang = 'pt';
		$this->bodyClass = 'index';
	}

	public function addMeta($str) {
		array_push($this->metas, $str);
	}

	public function addStyle($link) {
		array_push($this->styles, "<link href='{$link}' type='text/css' rel='stylesheet'>");
	}

	public function addScript($link) {
		array_push($this->scripts, "<script type='text/javascript' src='{$link}'></script>");
	}

	private function getHtmlHead() {
		$str = "<head>\n";
		$str .= join("\n", $this->metas)."\n";
		$str .= join("\n", $this->styles)."\n";
		$str .= join("\n", $this->scripts)."\n";
		$str .= "<title>{$this->name}</title>\n";
		$str .= "</head>\n";
		return $str;
	}

	private function getHtmlBody($content) {
		return "<body class='{$this->bodyClass}'>\n{$content}\n</body>\n";
	}

	public function getHtml($content) {
		$str = "<!DOCTYPE html>\n<html lang='{$this->lang}'>\n";
		$str .= $this->getHtmlHead();
		$str .= $this->getHtmlBody($content);
		$str .= "</html>";
	}
}