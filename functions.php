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
      if(is_link(PUBLIC_DIR."stylesheets/".$name)) {
        $base = PUBLIC_DIR;
        $d = PUBLIC_DIR."stylesheets/".$name;
      } else {
        $base = PLUGIN_DIR.$plugin."/resources/public/";
        $d = $base."stylesheets/";
      } 
    } else {
      $base = PUBLIC_DIR;
      $d = $base."stylesheets/".$name; 
    }
    if(!is_readable($d)) return false;
         
    
    foreach($tag_build->iterate_dir($d, "js") as $file){
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
      if(is_link(PUBLIC_DIR."javascripts/".$name)) {
        $base = PUBLIC_DIR;
        $d = PUBLIC_DIR."javascripts/".$name;
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

