<?php

/*
 * PHP-FlexPart (https://github.com/GuilhermeRossato/PHP-FlexPart)
 * Licensed under the MIT License (https://opensource.org/licenses/MIT)
 */

namespace GuilhermeRossato\FlexPart;

class FGException extends \Exception {}

class FolderNotFoundException extends FGException {}

class CouldNotScanFolderException extends FGException {}

class CouldNotOpenFileException extends FGException {}

class InvalidBlockException extends FGException {}
