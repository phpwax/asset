<?php
class StylesheetsController extends BuildController{
  public function build(){
    $this->action = "stylesheets";
    return parent::method_missing();
  }
}
