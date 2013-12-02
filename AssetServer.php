<?php
namespace Wax\Asset;
use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


/**
* A collection of assets loaded by glob.
*
* @author Ross Riley
*/
class AssetServer {

  public $asset_manager = false;
  public $listeners     = array();
  public $bundle_map    = array();

  public $mime_types_map = array(
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
  );

  public $type_mapping = array(
      "stylesheets" => array(
        "filter" => "css",
        "b_method" => "stylesheet_link_tag"),
      "javascripts" => array(
        "filter" => "js",
        "b_method" => "javascript_include_tag"));

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
   public function register($listener, $asset_directory, $pattern) {
     if(!$pattern) $pattern = "/*";
     $glob = rtrim($asset_directory,"/").$pattern;
     $bundle = $this->bundle_formatter($listener);
     $this->listeners[$listener]=$bundle;
     $this->bundle_map[$bundle] = $glob;
   }


  /**
   * Returns whether server can handle url based on listener
   *
   * @param string $listener
   * @return boolean
   */
  public function handles($url) {
    foreach($this->listeners as $pattern=>$bundle) {
      if(preg_match("#^".preg_quote($pattern)."/#i", $url)) return true;
    }
    return false;
  }


  public function load($bundle) {
    $locator = $this->bundle_map[$bundle];
    $finder = new RecursiveAssetFinder($locator);
    $this->asset_manager->set($bundle, $finder->get_collection());
    return $this->asset_manager->get($bundle);
  }



  public function serve($url) {
    foreach($this->listeners as $pattern=>$bundle) {
      if(preg_match("#^".preg_quote($pattern)."/#i", $url)) {
        $matched_pattern = $pattern;
        if(!isset($this->bundle_map[$bundle])) return;
        $locator = $this->bundle_map[$bundle];
        $finder = new RecursiveAssetFinder($locator);
      }
    }

    $asset_url = preg_replace("#^".$matched_pattern."#", "", $url);
    $matched_asset = $finder->get_single_asset($asset_url);
    if($matched_asset) {
      $this->asset_response($matched_asset);
    }


  }

  private function asset_response($asset) {
    $response = new Response();
    $response->setExpires(new \DateTime());

    // last-modified
    if (null !== $lastModified = $asset->getLastModified()) {
      $date = new \DateTime();
      $date->setTimestamp($lastModified);
      $response->setLastModified($date);
    }


    if($response->isNotModified(Request::createFromGlobals())) {
      $response->send();
      exit;
    }

    $asset_content = $asset->dump();
    $response->setContent($asset_content);
    $response->headers->set("Content-type",$this->guess_mime($asset));
    $response->send();
    exit;
  }

  private function guess_mime($asset_file) {
    $path = pathinfo($asset_file->relative);
    $mapped_mime = $this->mime_types_map[$path["extension"]];
    if($mapped_mime) return $mapped_mime;
    else {
      $finfo = new \finfo(FILEINFO_MIME_TYPE);
      return $finfo->buffer($asset_file->dump());
    }
  }

  public function bundle_builder($name, $options = array(), $plugin="", $type) {
    $tag_build = new \AssetTagHelper;
    $b_method = $this->type_mapping[$type]['b_method'];
    if(ENV=="development" && !defined("PRODUCTION_ASSETS")) {
      if($plugin) {
        $dir = $this->fetch_dir($name, $type);
        if(!$dir) $dir = array(
          "base" => PLUGIN_DIR.$plugin."/resources/public/",
          "dir"  => PLUGIN_DIR.$type."/");
      }else{
        $dir = array(
          "base" => PUBLIC_DIR,
          "dir"  => PUBLIC_DIR.$type."/".$name);
      }

      foreach($this->find_files($dir["dir"], $this->type_mapping[$type]['filter']) as $file)
        $ret .= $tag_build->$b_method("/".str_replace($dir["base"], "", $file), $options);
    }else{
      $git_rev = \AssetTagHelper::git_revision();
      $ret = $tag_build->$b_method("/build/$git_rev/$type/$name", $options);
    }
    return $ret;
  }

  private function find_files($dir, $filter){
    if(!is_readable($dir)) return;
    $tag_build = new \AssetTagHelper;
    foreach($tag_build->iterate_dir($dir, $filter) as $file) $ret[] = $file->getPathName();
    return $ret;
  }

  private function fetch_dir($name, $type){
    if(!$this->handles($type."/".$name."/")) return false;
    $asset_bundle = $this->bundle_formatter($type."/".$name);
    $this->load($asset_bundle);
    if($this->asset_manager->has($asset_bundle))
      return array(
        "base" => dirname(dirname($this->asset_manager->get($asset_bundle)->getSourceRoot()))."/",
        "dir"  => $this->asset_manager->get($asset_bundle)->getSourceRoot());
  }

  public function built_bundle($name, $type, $version_hash){
    $dir = $this->fetch_dir($name, $type);
    if(!$dir){
      foreach(array(
        array(
          "base" => PUBLIC_DIR,
          "dir"  => PUBLIC_DIR.$type."/".$name),
        array(
          "base" => PLUGIN_DIR,
          "dir"  => PLUGIN_DIR.$plugin."/resources/public/".$type."/")
      ) as $test)
        if(is_dir($test["dir"])) $dir = $test;
    }
    $extension = $this->type_mapping[$type]['filter'];
    foreach($this->find_files($dir["dir"], $extension) as $file)
      $combined .= file_get_contents($file)."\n";

    //cache into public dir for direct http serving if possible
    if(is_writable(PUBLIC_DIR."build")){
      $base = PUBLIC_DIR."build/$version_hash/$type";
      mkdir($base, 0777, true);
      file_put_contents("$base/$name.$extension", $combined);
    }

    return $combined;
  }

  public function mime($type){
    return $this->mime_types_map[$this->type_mapping[$type]['filter']];
  }

  private function bundle_formatter($listener) {
    return preg_replace("/[^A-Za-z0-9 ]/", '_', $listener);
  }


}
