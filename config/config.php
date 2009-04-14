<?php
include(sfConfigCache::getInstance()->checkConfig(sfConfig::get('sf_app_config_dir_name').'/sfTextReplacementPlugin.yml'));
if(in_array('sfTextReplacement', sfConfig::get('sf_enabled_modules')))
{
  if(sfConfig::get('app_sfTextReplacementPlugin_routes_register', true))
  {
    $r = sfRouting::getInstance();
  
    // preprend our routes
    $r->prependRoute('sfTextReplacementImageSelector', '/textReplacement/:selector/:text.png', array('module' => 'sfTextReplacement', 'action' => 'index'));
    $r->prependRoute('sfTextReplacementImage', '/textReplacement/:text.png', array('module' => 'sfTextReplacement', 'action' => 'index'));
    // Base Route - is needed to provide the base URL to the JavaScript. Whenever you change the routes above, change this one as well
    $r->prependRoute('sfTextReplacementBase', '/textReplacement/', array('module'=>'sfTextReplacement', 'action'=>'index'));
  }
}
else
{
  sfContext::getInstance()->getLogger()->info('Please enable sfTextReplacement module in settings.yml');
}