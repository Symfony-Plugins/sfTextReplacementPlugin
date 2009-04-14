<?php

class sfTextReplacementActions extends sfActions
{
	public function executeIndex()
	{
		if(!$this->hasRequestParameter('text'))
	      $text = sfConfig::get('app_sfTextReplacementPlugin_emptyText', 'No Image');
	    else 
	      $text = urldecode($this->getRequestParameter('text'));	    
	    $selector = $this->getRequestParameter('selector', 'default');
	    //include_once (dirname(__FILE__).'/../../../lib/imageGenerator.inc.php');

	    sfTextReplacement::generateImage($text, $selector);
	    
		return sfView::NONE;
	}
}

?>