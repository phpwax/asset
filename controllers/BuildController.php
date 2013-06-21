<?php
namespace Wax\Asset;
class BuildController extends WaxController{
  public function method_missing(){
    print_r("here");exit;
  }
}