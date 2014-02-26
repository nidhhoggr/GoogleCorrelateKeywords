<?php

require_once(dirname(__FILE__) . '/../../libs/HTML-DOM-Parser/simple_html_dom.php');

class StartPageAdaptor implements KeywordAdaptor {

  private $apiUrl = 'http://startpage.com/do/search?cat=web&cmd=process_search&language=english&engine0=v1all&query={KEYWORD}';
  private $sourceId = 3;

  public function getUrlFromKeyword($keyword) {
    return str_replace('{KEYWORD}',$keyword,$this->apiUrl);
  }

  public function getResults() {
    $results =  $this->parseable->find('#results ol .result h3 a');
    return array_slice((array)$results, 4);
  }

  public function setParseable($url) {
    $this->parseable = file_get_html($url);
  }

  public function isParseable() {
    return is_object($this->parseable);
  }

  public function getKeywordSourceId() {
    return $this->sourceId;
  }
}

