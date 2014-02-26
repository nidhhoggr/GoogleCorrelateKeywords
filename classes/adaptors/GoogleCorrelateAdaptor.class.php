<?php
require_once(dirname(__FILE__) . '/../../libs/HTML-DOM-Parser/simple_html_dom.php');

class GoogleCorrelateAdaptor implements KeywordAdaptor {

  private $apiUrl = 'http://www.google.com/trends/correlate/search?e={KEYWORD}&t=weekly&p=us';
  private $sourceId = 4;

  public function getUrlFromKeyword($keyword) {
    return str_replace('{KEYWORD}',$keyword,$this->apiUrl);
  }

  public function getResults() {
    return $this->parseable->find('.result a');
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
