<?php
class BuildController extends WaxController{
  public $use_layout = false;
  public $use_view = false;
  public function method_missing(){
    $as = AutoLoader::get_asset_server();
    echo $as->built_bundle(WaxURL::get("id"), $this->action);
  }
}
