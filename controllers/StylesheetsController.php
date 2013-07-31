<?php
class StylesheetsController extends BuildController{
  public function build(){
    if(!$this->filename) $this->filename = array_pop($this->route_array);
    $this->filename = str_replace("_combined", "", $this->filename);
    $this->action = $this->type = "stylesheets";
    return parent::method_missing();
  }
}
