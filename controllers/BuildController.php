<?php
class BuildController extends WaxController{
  public function method_missing(){
    print_r("here");exit;
  }
}