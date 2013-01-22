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
  public $handlers = array();
  
  
  public function __construct() {
    $this->asset_manager = new AssetManager;
  }
  
  public function register($bundle, $asset_directory, $type=false) {
    $glob = rtrim($asset_directory,"/")."/*";
    $finder = new RecursiveAssetFinder($glob);
    $this->asset_manager->set($bundle."_".$type, $finder->get_collection());
  }
  
  public function handles($bundle, $type) {
    return $this->asset_manager->has($bundle."_".$type);
  }
  
  
  public function serve($asset_paths = array()) {
    $type = array_shift($asset_paths);
    $bundle = array_shift($asset_paths);
    $asset_url = implode("/",$asset_paths);
    $collection = $this->asset_manager->get($bundle."_".$type);
    foreach($collection as $asset) {
      if($asset->getTargetPath() == $asset_url) {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        print_r($finfo->file($asset->getSourcePath()));
      }
    }
    exit;
  }
  
  
  static public function symlink_bundle($bundle, $type, $source_path, $target_link = false) {
    if(!$target_link) {
      if($type == "images") $target_base = PUBLIC_DIR.$type;
      else $target_base = PUBLIC_DIR.$type."/build/vendor/";
			if(is_dir($target_base) === false ) mkdir($target_base, 0777, true);
      $target_link = $target_base.$bundle;
    }
    if(!is_link($target_link)) {
      if(is_writable(dirname($target_link))) {
        symlink($source_path, $target_link);
      } else {
        throw new \Exception("Unable to create $type bundle at $target_link : Allow write access to parent folder");
      }
    }
  }
  
  
  
  
}