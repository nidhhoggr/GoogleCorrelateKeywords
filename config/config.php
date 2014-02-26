<?php

//include all of the adaptors etc
require_once(dirname(__FILE__) . '/../libs/SupraModel/SupraModel.class.php');
require_once(dirname(__FILE__) . '/../libs/HTML-DOM-Parser/simple_html_dom.php');
require_once(dirname(__FILE__) . '/../classes/KeywordGrabber.class.php');
require_once(dirname(__FILE__) . '/../classes/adaptors/AdaptorInterface.class.php');
require_once(dirname(__FILE__) . '/../classes/adaptors/GoogleCorrelateAdaptor.class.php');
require_once(dirname(__FILE__) . '/../classes/adaptors/StartPageAdaptor.class.php');

//SET THE CONNECTION VARS HERE
$dbuser = 'root';
$dbpassword  = 'root';
$dbname = 'simplesearch';
$dbhost = 'localhost';
$driver = 'mysql';

$connection_args = compact('dbuser','dbname','dbpassword','dbhost','driver');

//EXTEND THE BASE MODEL
class CipfadModel extends SupraModel {

  //SET THE TABLE OF THE MODEL AND THE IDENTIFIER
  public function configure() {}
 
  public function saveError($err) {
    $this->setTable("error");
    $this->msg = addslashes($err);
    $this->save();
  }
}
 
$CipfadModel = new CipfadModel($connection_args);
