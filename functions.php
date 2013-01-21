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
      if(is_link(PUBLIC_DIR."stylesheets/".$name)) {
        $base = PUBLIC_DIR."stylesheets/".$name;
      } else $base = PLUGIN_DIR.$plugin."/resources/public/stylesheets/";
       
    } else $base = PUBLIC_DIR."stylesheets/".$name;
    $d = $base;
    if(!is_readable($d)) return false;
         
    $dir = new \RecursiveIteratorIterator(new \RecursiveRegexIterator(new \RecursiveDirectoryIterator($d, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS), '#(?<!/)\.css$|^[^\.]*$#i'), true);
    foreach($dir as $file){
      $name = $file->getPathName();
      if(is_file($name)) $ret .= AssetTagHelper::stylesheet_link_tag("/".str_replace($base, "", $name), $options);
    }
  } else $ret = AssetTagHelper::stylesheet_link_tag("build/{$name}_combined", $options);
  return $ret;
}

function js_bundle($name, $options = array(), $plugin="") {
  if(ENV=="development" || defined("NO_JS_BUNDLE")) {
    if($plugin) {
      if(is_link(PUBLIC_DIR."javascripts/".$name)) {
        $base = PUBLIC_DIR."javascripts/".$name;
      } else $base = PLUGIN_DIR.$plugin."/resources/public/javascripts/";
       
    } else $base = PUBLIC_DIR."javascripts/".$name;
    $d = $base; 
    if(!is_readable($d)) return false;
    
    $dir = new \RecursiveIteratorIterator(new \RecursiveRegexIterator(new \RecursiveDirectoryIterator($d, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS), '#(?<!/)\.js$|^[^\.]*$#i'), true);
    foreach($dir as $file){
      $name = $file->getPathName();
      if(is_file($name))$ret .= AssetTagHelper::javascript_include_tag("/".str_replace($base, "", $name), $options);
    }
  } else $ret = AssetTagHelper::javascript_include_tag("/javascripts/build/{$name}_combined", $options);
  return $ret;
}

