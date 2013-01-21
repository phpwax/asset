<?php


/**
 * CSS Bundle
 *
 * @return void
 * @author Ross Riley
 **/
function css_bundle($name, $options=array(), $plugin="") {
  if(ENV=="development") {     
    if($plugin) {
      $base = PLUGIN_DIR.$plugin."/resources/public/";
      if(!is_dir($base) && is_link(PUBLIC_DIR."stylesheets/".$name)) {
        $base = PUBLIC_DIR;
      } 
    } else $base = PUBLIC_DIR;
    $d = $base."stylesheets/".$name;       
    $dir = new \RecursiveIteratorIterator(new \RecursiveRegexIterator(new \RecursiveDirectoryIterator($d, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS), '#(?<!/)\.css$|^[^\.]*$#i'), true);
    foreach($dir as $file){
      $name = $file->getPathName();
      if(is_file($name)) $ret .= self::stylesheet_link_tag("/".str_replace($base, "", $name), $options);
    }
  } else $ret = self::stylesheet_link_tag("build/{$name}_combined", $options);
  return $ret;
}

function js_bundle($name, $options = array(), $plugin="") {
  if(ENV=="development" || defined("NO_JS_BUNDLE")) {
    
    if($plugin) {
      $base = PLUGIN_DIR.$plugin."/resources/public/";
      if(!is_dir($base) && is_link(PUBLIC_DIR."javascripts/".$name)) {
        $base = PUBLIC_DIR;
      } 
    } else $base = PUBLIC_DIR;    
    $d = $base."javascripts/".$name;
    $dir = new \RecursiveIteratorIterator(new \RecursiveRegexIterator(new \RecursiveDirectoryIterator($d, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS), '#(?<!/)\.js$|^[^\.]*$#i'), true);
    foreach($dir as $file){
      $name = $file->getPathName();
      if(is_file($name))$ret .= self::javascript_include_tag("/".str_replace($base, "", $name), $options);
    }
  } else $ret = self::javascript_include_tag("/javascripts/build/{$name}_combined", $options);
  return $ret;
}

