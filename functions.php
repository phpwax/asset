<?php


/**
 * CSS Bundle
 *
 * @return void
 * @author Ross Riley
 **/
function css_bundle($name, $options=array(), $plugin="") {
  $tag_build = new AssetTagHelper;
  if(ENV=="development") {     
    if($plugin) {
      $as = AutoLoader::get_asset_server();
      if($as->handles("stylesheets/".$name)) {
        $base = dirname(dirname($as->asset_manager->get("stylesheets_".$name)->getSourceRoot()))."/";
        $d = $as->asset_manager->get("stylesheets_".$name)->getSourceRoot();
      } else {
        $base = PLUGIN_DIR.$plugin."/resources/public/";
        $d = $base."stylesheets/";
      } 
    } else {
      $base = PUBLIC_DIR;
      $d = $base."stylesheets/".$name; 
    }
    
    if(!is_readable($d)) return false;
         
    
    foreach($tag_build->iterate_dir($d, "css") as $file){
      $name = $file->getPathName();
      $ret .= $tag_build->stylesheet_link_tag("/".str_replace($base, "", $name), $options);
    }  
    
    
  } else $ret = $tag_build->stylesheet_link_tag("build/{$name}_combined", $options);
  return $ret;
}

function js_bundle($name, $options = array(), $plugin="") {
  $tag_build = new AssetTagHelper;
  if(ENV=="development" || defined("NO_JS_BUNDLE")) {
    if($plugin) {
      $as = AutoLoader::get_asset_server();
      if($as->handles("javascripts/".$name)) {
        $base = dirname(dirname($as->asset_manager->get("javascripts_".$name)->getSourceRoot()))."/";
        $d = $as->asset_manager->get("javascripts_".$name)->getSourceRoot();
      } else {
        $base = PLUGIN_DIR.$plugin."/resources/public/";
        $d = $base."javascripts/";
      } 
    } else {
      $base = PUBLIC_DIR;
      $d = $base."javascripts/".$name; 
    }
    
    if(!is_readable($d)) return false;
    
    foreach($tag_build->iterate_dir($d, "js") as $file){
      $name = $file->getPathName();
      $ret .= $tag_build->stylesheet_link_tag("/".str_replace($base, "", $name), $options);
    }  

  } else $ret = $tag_build->javascript_include_tag("/javascripts/build/{$name}_combined", $options);
  return $ret;
}

