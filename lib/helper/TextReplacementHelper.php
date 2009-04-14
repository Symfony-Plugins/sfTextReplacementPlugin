<?php
  use_helper('Javascript');
  function graphical_text($text, $selector='default')
  {
    return image_tag(url_for('@sfTextReplacementImageSelector?text=' . urlencode($text) . '&selector=' . $selector), array('alt'=>$text));
  }
  
  function js_replace_text($selector)
  {
    use_javascript('/sfTextReplacementPlugin/js/replacement.js');
    
    $all_selectors = "";
    
    if(is_array($selector))
    {
      for($i = 0; $i < count($selector); $i++)
      {
        $all_selectors .= 'sfTextReplacement_single("' . $selector[$i] . '" ,"' . url_for('@sfTextReplacementBase') . '",' . sfConfig::get('sftextreplacementplugin_selectors_default_wordwrap', 'false') .');';
      }
    }
    else
    {
      $all_selectors = 'sfTextReplacement_single("' . $selector . '" ,"' . url_for('@sfTextReplacementBase') . '",' . sfConfig::get('sftextreplacementplugin_selectors_default_wordwrap', 'false') .');';
    }
    
    return javascript_tag($all_selectors);
  }
  
  function seo_graphical_text($text, $selector='default', $width=NULL, $height=NULL)
  {
    use_stylesheet('/sfTextReplacementPlugin/css/replacement.css');
    $url=url_for('@sfTextReplacementImageSelector?text=' . urlencode($text) . '&selector=' . $selector, TRUE);
    
    // FIXME: omg, this is really, I mean really really - UGLY AND SLOW! Fix that if in production use!
    if(!$width)
    {
              
      $dimensions = sfTextReplacement::getImageDimensions($text, $selector); //needs apache to allow outgoing http requests!
      $width  = $dimensions['width'];
      $height = $dimensions['height'];
    }
    return '<span class="sfTextReplacement" style="height: ' . $height . 'px; width: ' . $width. 'px; background-image: url(\'' . $url. '\')"><span>' . $text . '</span></span>';
  }
  
?>