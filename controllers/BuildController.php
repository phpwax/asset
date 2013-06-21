<?php
class BuildController extends WaxController{
  public $use_layout = false;
  public $use_view = false;
  public function method_missing(){
    $as = AutoLoader::get_asset_server();
    $this->response->add_header("Content-Type", $as->mime($this->action));
    $this->response->write($as->built_bundle(WaxURL::get("id"), $this->action));
  }
}
