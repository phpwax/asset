<?php
namespace Wax\Asset;
class StylesheetsController extends BuildController{
  public function build(){
    return parent::method_missing();
  }
}
