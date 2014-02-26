<?php
interface KeywordAdaptor {
  public function getUrlFromKeyword($keyword); 
  public function getResults();
  public function setParseable($url);
  public function isParseable();
}
