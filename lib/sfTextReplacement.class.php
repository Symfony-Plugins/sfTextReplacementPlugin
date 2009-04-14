<?php
class sfTextReplacement
{
  public static function generateImage($text, $selector)
  {
    $caching       = sfConfig::get('sftextreplacementplugin_caching', TRUE);
    $cacheFolder   = SF_ROOT_DIR . DIRECTORY_SEPARATOR . sfConfig::get('sftextreplacementplugin_cachedir', 'web/textReplacement/');
    $fontFolder    = SF_ROOT_DIR . DIRECTORY_SEPARATOR . sfConfig::get('sftextreplacementplugin_fontdir', 'plugins/sfTextReplacement/data/');

    if($caching)
    {
      $cacheInWebDir = sfConfig::get('sftextreplacementplugin_cacheinwebdir', TRUE);      
      // if cached image file is saved in webdir
      if($cacheInWebDir)
      {
        $filename =  $cacheFolder . $selector . DIRECTORY_SEPARATOR . self::generateFilename($text, $selector);        
        if(!file_exists($cacheFolder . $selector))
        {
          mkdir($cacheFolder . $selector);
        }
      }
      else
        $filename =  $cacheFolder . self::generateFilename($text, $selector);
        
      if(is_readable($filename))
      {
        $file = @fopen($filename, 'rb');
        header('Content-type: image/png');
        while(!feof($file))
          print(($buffer = fread($file, 4096))) ;
        fclose($file) ;
        exit ;
      }
    }
    
    // generate image
    $textImage = self::generateTextImage($text, $selector);
    
    // save copy if caching is enabled
    if($caching)
    {
      $textImage->save($filename);
    }    
    
    // send to browser
    $textImage->sendToBrowser();
  }
  
  public static function getImageDimensions($text, $selector)
  {
    $caching     = sfConfig::get('sftextreplacementplugin_caching', TRUE);
    $cacheFolder = SF_ROOT_DIR . DIRECTORY_SEPARATOR . sfConfig::get('sftextreplacementplugin_cachedir', 'web/textReplacement/');
    
    // Look for cached image file
    if($caching)
    {
      $cacheInWebDir = sfConfig::get('sftextreplacementplugin_cacheInWebDir', TRUE);
      if($cacheInWebDir)
      {
        $filename =  $cacheFolder . $selector . DIRECTORY_SEPARATOR . self::generateFilename($text, $selector);        
        if(!file_exists($cacheFolder . $selector))
        {
          mkdir($cacheFolder . $selector);
        }
      }
      else
        $filename =  $cacheFolder . self::generateFilename($text, $selector);
      if(is_readable($filename))
      {
        $info = getimagesize($filename);
        $dimensions['width']  = $info[0];
        $dimensions['height'] = $info[1];
        return $dimensions;
      }    
    }
    
    $textImage = self::generateTextImage($text, $selector);
    $dimensions['width']  = $textImage->getImageWidth();
    $dimensions['height'] = $textImage->getImageHeight();
    return $dimensions;   
  }
  
  private static function generateTextImage($text, $selector)
  {
    $fontFolder  = SF_ROOT_DIR . DIRECTORY_SEPARATOR . sfConfig::get('sftextreplacementplugin_fontdir', 'data/fonts/');
    
    $defaultCfg = array(
      'fontFile'=>                 'JOURNAL_.TTF' ,
      'fontSize'=>                 50 ,
      'fontColor'=>                '#000000',
      'backgroundColor'=>          '#ff0000',
      'transparentBackground'=>    true,
      );
    
    // Load image settings
    $defaultCfg=array_merge($defaultCfg, sfConfig::get('sftextreplacementplugin_selectors_default', array()));
    if($selector)
      $cfg=array_merge($defaultCfg, sfConfig::get('sftextreplacementplugin_selectors_'.$selector, array()));
    else
      $cfg = $defaultCfg;
    
    $cfg['fontFile'] =$fontFolder . $cfg['fontFile'];
      
    // Generate image
    $textImage = new sfTextImage($text, $cfg['fontFile'], $cfg['fontSize'], $cfg['fontColor'], $cfg['transparentBackground'], $cfg['backgroundColor']);
    return $textImage;
  }
  
  private static function generateFilename($text, $selector)
  {
    $cacheInWebDir = sfConfig::get('sftextreplacementplugin_cacheinwebdir', TRUE);
    if($cacheInWebDir)
      return urlencode($text) . '.png';
    else
      return md5($text . $selector) . '.png';
  }
  
}
?>