<?php

class KeywordGrabber { 

  private $config;
  private $unacceptablePostfixWords = array();
  private $acquiredKeywords = array();

  function __construct($config) {
    $this->config = $config;
    $this->verbose = $config['verbose'];
    $this->model = $config['model'];
    $this->parentKeywords = [];
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

        //save keyword here
        $this->saveKeyword($kw, $keyword);

        $this->run($kw, $level);
      }
    }
  }

  public function saveKeyword($keyword, $parentKeyword) {

    $this->model->setTable('keyword');
    $this->setParentKeywordId($parentKeyword);
    $alreadyExists = $this->model->findOneBy(['conditions'=>["name = '".addslashes($keyword)."'"]]);

    if(!$alreadyExists) {
      $this->model->keyword_source_id = $this->adaptor->getKeywordSourceId();
      $this->model->name = $keyword;
      $alreadyExistsId = $this->model->save();
      echo 'save keyword id '. $alreadyExistsId ."\r\n"; 
    } else {
      $alreadyExistsId = $alreadyExists->id;
    }

    $this->model->setTable('keyword_join');    
    $this->model->pkeyword_id = $this->parentKeywords[$parentKeyword];
    $this->model->ckeyword_id = $alreadyExistsId;

    try { 
      $this->model->save(['hasIdentifier'=>false]);
    } catch(Exception $e) {
      $this->model->saveError($e->getMessage());
    }
  }

  private function setParentKeywordId($keyword) {
    if(array_key_exists($keyword,$this->parentKeywords)) return;
    $keywordObj = $this->model->findOneBy(['conditions'=>["name = '".addslashes($keyword)."'"]]);
    if(!$keywordObj) {
      $this->model->name = $keyword;
      $keywordId = $this->model->save(); 
    } else {
      $keywordId = $keywordObj->id;
    }
    $this->parentKeywords[$keyword] = $keywordId;
  }

  public function getAllKeywords() {

    return $this->acquiredKeywords;
  }

  public function getAllDistinctKeywords() {

    return array_unique($this->acquiredKeywords);
  }

  public function setAdaptor(KeywordAdaptor $adaptor) {
    $this->adaptor = $adaptor;
  }

  private function getAdaptorUrl($keyword) {
    return $this->adaptor->getUrlFromKeyword($keyword);
  }

  private function getAdaptorResults() {
    return $this->adaptor->getResults();
  }

  private function setAdaptorParseable($url) {
    $this->adaptor->setParseable($url);
  }

  private function handleRetries() {

    $retryCount = 0;

    while(!$this->adaptor->isParseable() && $retryCount < 3) {
      $this->setAdaptorParseable($url);
      $retryCount++;
    }

    return $this->adaptor->isParseable();
  }

  private function handleError($err) {
    echo 'saving '. $err . "\r\n";
    $this->model->saveError($err);
  }

  public function getKeywords($keyword) {

    $keyword = urlencode($keyword);

    $url = $this->getAdaptorUrl($keyword);

    $this->setAdaptorParseable($url);

    if(!$this->adaptor->isParseable()) {
      if(!$this->handleRetries()) {
        $this->handleError('Could not parse: ' . $url); 
        return;
      }
    }

    $results = $this->getAdaptorResults();

    $kCount = 0; 

    foreach($results as $k=>$result) { 

      $kw = $result->plaintext;

      if(!$this->_isAcceptableKeyword($kw)) continue;
  
      $kCount++;

      if($kCount > $this->config['resultLimit']) break;

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
