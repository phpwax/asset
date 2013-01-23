<?php
use Wax\Asset\AssetServer;

/**
 * CSS Bundle
 *
 * @return void
 * @author Ross Riley
 **/
function css_bundle($name, $options=array(), $plugin="") {
  echo AssetServer::bundle_builder($name, $options, $plugin,"stylesheets");
}

function js_bundle($name, $options = array(), $plugin="") {
  echo AssetServer::bundle_builder($name, $options, $plugin,"javascripts");
}

