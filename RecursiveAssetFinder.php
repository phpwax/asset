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
    $this->initialize();
  }

  public function get_collection() {
    return new AssetCollection($this->assets);
  }
  
  public function add($file) {
    $relative_path = str_replace($this->base, "", $file); 
    $this->assets[]=new FileAsset($file, array(),null,null,array("relativePath"=>$relative_path));
  }
  
  private function initialize() {
    $this->add_directory($this->base);    
    $directories = new \RecursiveIteratorIterator(
        new \ParentIterator(new \RecursiveDirectoryIterator($this->base)), 
         \ RecursiveIteratorIterator::SELF_FIRST);
    foreach($directories as $dir) {
      $this->add_directory($dir);
    }
  }
  
  private function add_directory($dir) {
    if(false !== $paths = glob($dir."/".$this->pattern)) {
      foreach($paths as $path) {
        if(is_file($path)) {
          $this->add($path);
        }
      }
    }
  }
  
  
}