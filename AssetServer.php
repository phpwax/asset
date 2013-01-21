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
    if(!$target_link) $target_link = PUBLIC_DIR.$type."/".$bundle;
    if(!is_link($target_link)) {
      if(is_writable(basename($target_link))) {
        symlink($source_path, $target_link);
      } else {
        throw new \Exception("Unable to create $type bundle at $target_link - Allow write access to parent folder");
      }
    }
  }
  
  
}