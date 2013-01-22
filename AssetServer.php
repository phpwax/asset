<?php
namespace Wax\Asset;
use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;


/**
* A collection of assets loaded by glob.
*
* @author Ross Riley
*/
class AssetServer {
  
  public $asset_manager = false;
  public $listeners     = array();
  
  
  public function __construct() {
    $this->asset_manager = new AssetManager;
  }
  
  /**
   * Registers an asset handler, the listener decides what url fragments to take responsibility for.
   *
   * @param string $listener 
   * @param string $asset_directory 
   * @param string $pattern 
   */
  public function register($listener, $asset_directory, $pattern="/*") {
    $glob = rtrim($asset_directory,"/").$pattern;
    $finder = new RecursiveAssetFinder($glob);
    $bundle = $this->bundle_formatter($listener);
    $this->listeners[$listener]=$bundle;
    $this->asset_manager->set($bundle, $finder->get_collection());
  }
  
  /**
   * Returns whether server can handle url based on listener
   *
   * @param string $listener 
   * @return boolean
   */
  public function handles($url) {
    foreach($this->listeners as $pattern=>$bundle) {
      if(preg_match("/".$pattern."/", $url)) return $this->asset_manager->has($bundle);
    }
  }
  
  
  public function serve($url) {    
    foreach($this->listeners as $pattern=>$bundle) {
      if(preg_match("/".$pattern."/", $url)) {
        $matched_pattern = $pattern;
        $collection = $this->asset_manager->get($bundle);
      }
    }
    if(!$collection) return;
    $asset_url = preg_replace("/^".$matched_pattern."/", "", $url);
    foreach($collection as $asset) {
      if($asset->relative == $asset_url) {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        header("Content-Type: ".$finfo->buffer($asset->dump()));
        echo $asset->dump();
        exit;
      }
    }
  }

  
  private function bundle_formatter($listener) {
    return preg_replace("/[^A-Za-z0-9 ]/", '_', $listener);
  }
  
  
}
