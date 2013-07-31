<?php
class BuildController extends WaxController{
  public $use_layout = false;
  public $use_view = false;
  public function method_missing(){
    $as = AutoLoader::get_asset_server();
    if(!$this->filename) $this->filename = array_pop($this->route_array);
    $this->filename = substr($this->filename, 0, strrpos($this->filename, "."));
    if(!$this->type) $this->type = $this->route_array[2];
    $this->response->add_header("Content-Type", $as->mime($this->type));
    $this->hash = $this->route_array[1];
    $this->response->write($as->built_bundle($this->filename, $this->type, $this->hash));
  }
}
