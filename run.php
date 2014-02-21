<?php

$args = getopt('k:v');

var_dump($args);

if(!isset($args['k'])) {
  die('Must provide a keyword with -k flag');
}

require_once(dirname(__FILE__) . '/libs/HTML-DOM-Parser/simple_html_dom.php');
require_once(dirname(__FILE__) . '/GoogleCorrelateKeywordGrabber.class.php');

$config = [
  'correlateResultLimit'=>10,
  'depthLevelLimit'=> 3,
  'verbose'=>isset($args['v'])
];

$gckg = new GoogleCorrelateKeywordGrabber($config);

$gckg->setUnacceptablePostfixWords([
  'in',
  'at',
  'of',
  'by'
]);

$gckg->run($args['k']);

var_dump("overall: " . count($gckg->getAllKeywords()));
var_dump("distinct: " . count($gckg->getAllDistinctKeywords()));
