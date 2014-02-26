<?php

require_once(dirname(__FILE__) . '/config/config.php');
$args = getopt('k:vd:r:ha:');

var_dump($args);

if(isset($args['h'])) {
  die("\tphp run.php -k=\"keyword name here\" -d 3 -r 10 -v\r\n");
}

if(!isset($args['k'])) {
  die('Must provide a keyword with -k flag');
}

if(!isset($args['a'])) {
  die('Must provide an adaptor with -a flag'."\r\nList of adaptors:\r\n\tstartpage\r\n\tcorrelate\r\n");
}

$config = [
  'resultLimit'=>$args['r'],
  'depthLevelLimit'=>$args['d'],
  'verbose'=>isset($args['v']),
  'model'=> $CipfadModel
];

$gckg = new KeywordGrabber($config);

$gckg->setUnacceptablePostfixWords([
  'in',
  'at',
  'of',
  'by',
  'and'
]);

switch($args['a']) {
  case 'correlate': 
    $adaptor = new GoogleCorrelateAdaptor();
  break;
  case 'startpage':
    $adaptor = new StartPageAdaptor();
  break;
  default:
    die('no known adaptor of '. $args['a']);
  break;
}

$gckg->setAdaptor($adaptor); 

$gckg->run($args['k']);

var_dump("overall: " . count($gckg->getAllKeywords()));
var_dump("distinct: " . count($gckg->getAllDistinctKeywords()));
