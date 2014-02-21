<?php

require_once(dirname(__FILE__) . '/libs/HTML-DOM-Parser/simple_html_dom.php');
require_once(dirname(__FILE__) . '/GoogleCorrelateKeywordGrabber.class.php');

$gckg = new GoogleCorrelateKeywordGrabber();

/*
$gckg->run('college');

echo count($gckg->getAllKeywords());
echo count($gckg->getAllDistinctKeywords());
*/

var_dump($gckg->_isAcceptableKeyword('colleges of'));
var_dump($gckg->_isAcceptableKeyword('colleges of humanity'));
var_dump($gckg->_isAcceptableKeyword('of humanity'));
