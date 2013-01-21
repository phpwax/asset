<?php
namespace Wax\Asset;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;


/**
* A collection of assets loaded by glob.
*
* @author Ross Riley
*/
class AssetServer {
  
  
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
  
  public function build_bundle($bundle, $type, $source_path, $target_link =false) {
    
  }
  
  
}