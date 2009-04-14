<?php
class sfTextImage {

  /**
  * filename of font file
  * @access private
  * @var string
  */
  private $fontFile;
  
  /**
  * color of text in hexadecimal format, eg #ff0000 for red
  * @access private
  * @var string
  */
  private $fontColor;
  
  /**
  * size of text in point
  * @access private
  * @var int
  */
  private $fontSize;
  
  /**
  * background color for the image, also used to calculate the anti-aliased edges
  * @access private
  * @var int
  */
  private $backgroundColor;
  
  /**
  * true to enable transparent background, false to disable
  * @access private
  * @var int
  */
  private $transparentBackground;
  
  /**
  * Output text
  * @access private
  * @var string
  */
  private $text;
  
  /**
  * the image width in pixel
  * @access private
  * @var int
  */
  private $imageWidth;
  
  /**
  * the image height in pixel
  * @access private
  * @var int
  */
  private $imageHeight;
  
  /**
  * the generated image
  * @access private
  * @var ressource
  */
  private $image;

  /**
  * TextImage constructor
  * @param string Output text of the image
  * @param string Filename of the TrueType font file
  * @param int (optional) size of output text
  * @param string (optional) Color of output text in hexadecimal (#000000)
  * @param boolean (optional) enables transparent background
  * @param string (optional) Background color in hexadecimal (#ffffff)
  * @access public
  */
  public function __construct($text, $fontFile, $fontSize = '12', $fontColor = '#000000', $transparentBackground = TRUE, $backgroundColor = '#ffffff')
  {
    $this->text                  = urldecode($text).' ';
    $this->fontFile              = $fontFile;
    $this->fontSize              = $fontSize;
    $this->fontColor             = $fontColor;
    $this->transparentBackground = $transparentBackground;
    $this->backgroundColor =       $backgroundColor;

    // Check if fontFile exists and is readable
    if(!is_readable($fontFile))
    {
      throw new Exception('Font file  "'. $fontFile .' not found');
    }
    
    // create image
    $backgroundRgb     = $this->hexToRgb($this->backgroundColor);
    $fontRgb           = $this->hexToRgb($this->fontColor);
    $dip               = $this->getDip($this->fontFile, $this->fontSize);
    $box               = @ImageTTFBBox($this->fontSize, 0, $this->fontFile, $this->text);
    $this->imageWidth  = abs($box[2]- $box[0]);
    $this->imageHeight = abs($box[5] - $dip);
    $this->image       = @ImageCreate($this->imageWidth, $this->imageHeight);

    if(!$this->image || !$box)
    {
      throw new Exception('The server could not create this heading image. You need GD lib to run properly!');
    }
    
    // allocate colors and draw text
    $background_color = ImageColorAllocate($this->image, $backgroundRgb['red'], $backgroundRgb['green'], $backgroundRgb['blue']);
    $fontColor        = ImageColorAllocate($this->image, $fontRgb['red'], $fontRgb['green'], $fontRgb['blue']);   
    ImageTTFText($this->image, $this->fontSize, 0, -$box[0], abs($box[5]-$box[3])-$box[1], $fontColor, $this->fontFile, $this->text) ;
        
    // set background transparency
    if($this->transparentBackground)
      ImageColorTransparent($this->image, $this->backgroundColor);
  }

  /**
  * returns the Image ressource
  * @access public
  */
  public function getImage()
  {
    return $this->image;
  }

  /**
  * returns the image's width in pixels
  * @access public
  */
  public function getImageWidth()
  {
    return $this->imageWidth;
  }

  /**
  * returns the image's height in pixels
  * @access public
  */
  public function getImageHeight()
  {
    return $this->imageHeight;
  }
  
  /**
  * sends the image to the browser
  * @access public
  */
  public function sendToBrowser()
  {
    header('Content-type: image/png');
    ImagePNG($this->image);
  }

  /**
  * saves the generated image to disk
  * @param string filename
  * @access public
  */  
  public function save($filename)
  {
    ImagePNG($this->image, $filename);
  }

  /**
  * Converts hexadecimal color code to its RGB value
  * @param string hexadecimal color code
  * @access private
  */
  private function hexToRgb($hex)
  {
    // remove '#'
    if(substr($hex, 0, 1) == '#')
        $hex = substr($hex, 1) ;

    // expand short form ('fff') color
    if(strlen($hex) == 3)
    {
        $hex = substr($hex, 0, 1) . substr($hex, 0, 1) .
               substr($hex, 1, 1) . substr($hex, 1, 1) .
               substr($hex, 2, 1) . substr($hex, 2, 1) ;
    }

    if(strlen($hex) != 6)
    {
      throw new Exception('Error: Invalid color "'.$hex.'"');
    }

    // convert
    $rgb['red'] =   hexdec(substr($hex,0,2)) ;
    $rgb['green'] = hexdec(substr($hex,2,2)) ;
    $rgb['blue'] =  hexdec(substr($hex,4,2)) ;

    return $rgb ;
  }

  /**
  * try to determine the "dip" (pixels dropped below baseline) of this font for this size. 
  * @param string hexadecimal color code
  * @access private
  */  
  private function getDip($font, $size)
  {
    $testChars = 'abcdefghijklmnopqrstuvwxyz' .
                 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' .
  				 '1234567890' .
  				 '!@#$%^&*()\'"\\/;.,`~<>[]{}-+_-=' ;
  	$box = @ImageTTFBBox($size, 0, $font, $testChars) ;
  	return $box[3];
  }
  
  public function __destruct()
  {
    ImageDestroy($this->image);
  }
}
?>