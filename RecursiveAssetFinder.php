<?php
namespace Wax\Asset;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;


/**
* A collection of assets loaded by glob.
*
* @author Ross Riley
*/
class RecursiveAssetFinder {
  
  private $initialized;
  private $assets;
  
  
  /**
  * Constructor.
  *
  * @param string|array $globs A single glob path or array of paths
  */
  public function __construct($glob) {
    $this->glob = $glob;
    $this->pattern = basename($glob);
    $this->base = dirname($glob);
    $this->assets = array();
  }

  public function get_collection() {
    $this->initialize();
    return new AssetCollection($this->assets, array(),$this->base);
  }
  
  public function get_single_asset($asset_url) {
    $files = $this->rglob($this->pattern, 0, $this->base);
    foreach($files as $file) {
      $relative = str_replace($this->base, "", $file);
      if($relative == $asset_url) {
        $as = new FileAsset($file);
        $as->relative = $relative;
        return $as;
      }
    }
  }
  
  public function add($file) {
    $relative_path = str_replace($this->base, "", $file); 
    $as = new FileAsset($file);
    $as->relative = $relative_path;
    $this->assets[]=$as;
  }
  
  private function initialize() {
    $files = $this->rglob($this->pattern, 0, $this->base);
    foreach($files as $path) {
      if(is_file($path)) $this->add($path);
    }
  }
  
  
  /**
   * @param int $pattern
   *  the pattern passed to glob()
   * @param int $flags
   *  the flags passed to glob()
   * @param string $path
   *  the path to scan
   * @return mixed
   *  an array of files in the given path matching the pattern.
   */

  private function rglob($pattern='*', $flags = 0, $path='') {
    $paths=glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
    $files=glob($path.$pattern, $flags);
    foreach ($paths as $path) { $files=array_merge($files,$this->rglob($pattern, $flags, $path)); }
    return $files;
  }
  
  
  
}
