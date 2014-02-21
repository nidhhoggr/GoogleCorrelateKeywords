<?php

require_once(dirname(__FILE__) . '/libs/HTML-DOM-Parser/simple_html_dom.php');

class GoogleCorrelateKeywordGrabber { 

  private $config;
  private $unacceptablePostfixWords = array();
  private $acquiredKeywords = array();

  function __construct($config) {
    $this->config = $config;
    $this->verbose = $config['verbose'];
  }

  public function setUnacceptablePostfixWords($words) {
    $this->unacceptablePostfixWords = $words;
  }

  public function run($keyword, $level = 0) {

    $level++;
 
    if($level <= $this->config['depthLevelLimit']) { 

      $keywords = $this->getKeywords($keyword);

      foreach($keywords as $kw) {

        if($this->verbose) echo str_pad("", $level-1, "\t") . $keyword . ' >> ' . $kw . "\r\n";

        $this->acquiredKeywords[] = $kw;

        $this->run($kw, $level);
      }
    }
  }

  public function getAllKeywords() {

    return $this->acquiredKeywords;
  }

  public function getAllDistinctKeywords() {

    return array_unique($this->acquiredKeywords);
  }

  public function getKeywords($keyword) {

    $keyword = urlencode($keyword);

    $url = 'http://www.google.com/trends/correlate/search?e='.$keyword.'&t=weekly&p=us';

    $html = file_get_html($url);

    $results = $html->find('.result a');

    $kCount = 0; 

    foreach($results as $k=>$result) { 

      $kw = $result->innertext;

      if(!$this->_isAcceptableKeyword($kw)) continue;
  
      $kCount++;

      if($kCount > $this->config['correlateResultLimit']) break;

      $keywords[] = $kw;
    }

    return $keywords;
  }

  function _getLastKeywordWord($keyword) {
    $split = explode(" ", $keyword);
    return $split[count($split)-1];
  }

  function _getFirstKeywordWord($keyword) { 
    $split = explode(" ", $keyword);
    return $split[0];
  } 

  function _isAcceptableKeyword($keyword) {
     return (!in_array($this->_getLastKeywordWord($keyword), $this->unacceptablePostfixWords) &&
       !in_array($this->_getFirstKeywordWord($keyword), $this->unacceptablePostfixWords));
  }
}
