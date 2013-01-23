<?php
use Wax\Asset\AssetServer;

/**
 * CSS Bundle
 *
 * @return void
 * @author Ross Riley
 **/
function css_bundle($name, $options=array(), $plugin="") {
  echo AssetServer::bundle_builder($name, $options = array(), $plugin="","stylesheets");
}

function js_bundle($name, $options = array(), $plugin="") {
  echo AssetServer::bundle_builder($name, $options = array(), $plugin="","javascripts");
}

