<?php
class BuildController extends WaxController{
  public $use_layout = false;
  public $use_view = false;
  public function method_missing(){
    $as = AutoLoader::get_asset_server();
    $filename = array_pop($this->route_array);
    $filename = substr($filename, 0, strrpos($filename, "."));
    $this->response->add_header("Content-Type", $as->mime($this->route_array[2]));
    $this->response->write($as->built_bundle($filename, $this->route_array[2], $this->route_array[1]));
  }
}
