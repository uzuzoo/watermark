<?php
/**
 * Watermark PDF Files
 *
 *
 * @author Alex Wright [alec@intrica.net]
 * @version 1.0.0
 * @copyright Copyright 2016, intrica.net
 */

namespace Uzuzoo\Watermark\Watermark;

use Uzuzoo\Watermark\Watermark;
use Uzuzoo\Watermark\Watermark\WmAbstract;


class Pdf extends WmAbstract
{
  public $pdf = FALSE;
  private $tmpWmImage = '';
  private $debug = FALSE;

  /**
   * @param Watermark $Wm
   */
  public function __construct(Watermark $Wm)
  {
    parent::__construct($Wm);
    $this->pdf = new \FPDI();
  }


  /**
   * @inheritDoc
   */
  public function process()
  {
    $ret = FALSE;
    $tmpWmImage = $this->generateWatermark();
    $sizes = getimagesize($tmpWmImage);
    $wmSizes = array(
      'width' => $sizes[0],
      'height' => $sizes[1]
    );

    // Count the pages of the Original PDF
    $pageCount = $this->pdf->setSourceFile($this->Wm->getInputFile());
    // Loop through pages of PDF applying the watermark
    for($i = 1; $i <= $pageCount; $i++) {

      // Get the current Page template
      $tplidx = $this->pdf->importPage($i);

      // get size of current page
      $specs = $this->pdf->getTemplateSize($tplidx);
      $pageSize = array(
        'width' => $specs['w'],
        'height' => $specs['h'],
      );
      // Set correct orientation
      $this->pdf->addPage($pageSize['height'] > $pageSize['width'] ? 'P' : 'L');
      $this->pdf->useTemplate($tplidx);

      // Get Watermark Position for current page
      $wmPosition = $this->getWmImagePosition($pageSize, $this->Wm->getWmPosition(), $wmSizes, $this->Wm->getWmPadding());
      // Apply the Watermark to the PDF Page
      $this->pdf->Image($tmpWmImage, $wmPosition['x'], $wmPosition['y']);
    }
    // Unlink Temp WmImage
    if(!$this->debug) {
      unlink($this->tmpWmImage);
    }
    // Save the watermarked PDF
    $ret = $this->pdf->Output($this->Wm->getOutput(), 'F');
    return TRUE;
  }


  private function generateWatermark()
  {
    $isImageWatermark = (($this->Wm->getWmType() == Watermark::WM_TYPE_IMAGE) ? TRUE : FALSE);
    if ($isImageWatermark) {
      $wmSource = $this->loadImage($this->Wm->getWmImageExt(), $this->Wm->getWmImage());
      $wmSource = $this->resizeImageResource($wmSource, $this->Wm->getWmImageHeight(), $this->Wm->getWmImageWidth());
      $wmSource = $this->imagesetopacity($wmSource, $this->Wm->getWmImageOpacity());
      imagesavealpha($wmSource, true);
    } else {

      $bbox = imagettfbbox($this->Wm->getWmFontSize(), $this->Wm->getWmFontAngle(), $this->Wm->getFontPathFilename(), $this->Wm->getWmText());
      $minX = min(array($bbox[0],$bbox[2],$bbox[4],$bbox[6]));
      $maxX = max(array($bbox[0],$bbox[2],$bbox[4],$bbox[6]));
      $minY = min(array($bbox[1],$bbox[3],$bbox[5],$bbox[7]));
      $maxY = max(array($bbox[1],$bbox[3],$bbox[5],$bbox[7]));
      // Dimensions of Container Box
      $width = ($maxX - $minX);
      $height = ($maxY - $minY);
      // Bottom Left Corner of Text regardless of angle
      $textX = $bbox[0];
      $textY = $bbox[1];
      // Top, Bottom, Left and Right Offsets
      $offset = array(
        't' => abs($textY - $minY),
        'b' => abs($textY - $maxY),
        'l' => abs($textX - $minX),
        'r' => abs($textX - $maxX),
      );

      // Create Watermark Image
      $wmSource = imagecreatetruecolor($width, $height);

      // Get a colour that is not the font colour so we can set it as the transparency

      // --------- Tried the bolow but it didn't work as good as expected
      // Lets set the transparency to a colour close to the font colour
      // $transColour = explode(",", $this->Wm->getWmFontColour());
      // $diff = 5;
      // foreach ($transColour as $key => $tc) {
      //   $transColour[$key] = (($tc > $diff) ? ($tc - $diff) : ($tc + $diff));
      // }
      // $transparentColour = $this->Wm->getAllocatedColour($wmSource, implode(",", $transColour));
      $transparentColour = $this->Wm->getAllocatedColour($wmSource, (($this->Wm->getWmFontColour() == Watermark::FONT_COLOUR_WHITE) ? Watermark::FONT_COLOUR_BLACK : Watermark::FONT_COLOUR_WHITE));
      imagefilledrectangle($wmSource, 0, 0, $width, $height, $transparentColour);
      imagecolortransparent($wmSource, $transparentColour);

      // Add the text
      $textColour = $this->Wm->getAllocatedColour($wmSource, $this->Wm->getWmFontColour());
      imagettftext($wmSource, $this->Wm->getWmFontSize(), $this->Wm->getWmFontAngle(), $offset['l'], $offset['t'], $textColour, $this->Wm->getFontPathFilename(), $this->Wm->getWmText());
    }
    $this->tmpWmImage = (($this->debug) ? $this->Wm->getOutputPath().'tmp.png' : $this->Wm->getOutputPath().md5(time()).'.png');
    imagepng($wmSource, $this->tmpWmImage);
    return $this->tmpWmImage;
  }

  private function imagesetopacity( $imageSrc, $opacity )
  {
    // Opacity value needs converting
    if(($opacity = (100 - $opacity)) == 0) {
      return $imageSrc;
    }

    $width  = imagesx( $imageSrc );
    $height = imagesy( $imageSrc );

    // Duplicate image and convert to TrueColor
    $imageDst = imagecreatetruecolor( $width, $height );
    imagealphablending( $imageDst, false );
    imagefill( $imageDst, 0, 0, imagecolortransparent( $imageDst ) );
    imagecopy( $imageDst, $imageSrc, 0, 0, 0, 0, $width, $height );

    // Set new opacity to each pixel
    for( $x = 0; $x < $width; ++$x ) {
      for( $y = 0; $y < $height; ++$y ) {
        $color = imagecolorat( $imageDst, $x, $y );
        $alpha = 127 - ( ( $color >> 24 ) & 0xFF );
        if ( $alpha > 0 ) {
          $color = ( $color & 0xFFFFFF ) | ( (int)round( 127 - $alpha * $opacity ) << 24 );
          imagesetpixel( $imageDst, $x, $y, $color );
        }
      }
    }
    return $imageDst;
  }

  private function getWmImagePosition($pageSize, $position, $wmSizes, $padding)
  {
    $posX = 0;
    $posY = 0;

    // $wmSizes are in pixels and $pageSize is in mm
    // therefore we need to convert one to match the other.
    // We convert $wmSizes to mm as we need the return values in mm
    $wmSizes = array_map(function($value){
      $pdfDPI = 96;
      $mmPerInch = 25.4;
      return ($value * $mmPerInch) / $pdfDPI;
    }, $wmSizes);

    switch ($this->Wm->getWmPosition()) {

      # Centered
      case 1:
        $posX = ( $pageSize['width'] / 2 ) - ( $wmSizes['width'] / 2 );
        $posY = ( $pageSize['height'] / 2 ) - ( $wmSizes['height'] / 2 );
      break;

      # Top-Left
      case 2:
        $posX = $padding;
        $posY = $padding;
      break;

      # Top-Center
      case 3:
        $posX = (($pageSize['width'] - $wmSizes['width']) / 2);
        $posY = $padding;
      break;

      # Top-Right
      case 4:
        $posX = (($pageSize['width'] - $wmSizes['width']) - $padding);
        $posY = $padding;
      break;

      # Middle-Left
      case 5:
        $posX = $padding;
        $posY = (($pageSize['height'] / 2) - ($wmSizes['height'] / 2));
      break;

      # Middle-Right
      case 6:
        $posX = (($pageSize['width'] - $wmSizes['width']) - $padding);
        $posY = (($pageSize['height'] / 2) - ($wmSizes['height'] / 2));
      break;

      # Bottom-Left
      case 7:
        $posX = $padding;
        $posY = (($pageSize['height'] - $wmSizes['height']) - $padding);
      break;

      # Bottom-Center
      case 8:
        $posX = (($pageSize['width'] - $wmSizes['width']) / 2);
        $posY = (($pageSize['height'] - $wmSizes['height']) - $padding);
      break;

      # Bottom-Right
      case 9:
        $posX = (($pageSize['width'] - $wmSizes['width']) - $padding);
        $posY = (($pageSize['height'] - $wmSizes['height']) - $padding);
      break;
    }
    return array('x' => $posX, 'y' => $posY);
  }


  private function loadImage($ext, $file)
  {
    $ret = FALSE;
    switch ($ext) {
      case 'jpg':
        $ret = imagecreatefromjpeg($file);
        break;

      case 'gif':
        $ret = imagecreatefromgif($file);
        break;

      case 'png':
        $ret = imagecreatefrompng($file);
        break;
    }
    return $ret;
  }

  /**
   * Get the Widtha nd Height of an Image Resource
   * @param mixed $img
   * @return array [width,height]
   */
  private function getImageSize($img)
  {
    return array('width' => imagesx($img), 'height' => imagesy($img));
  }



    /**
     * Resize an Image Resource
     * @param mixed $imgResource Image resource to be resized
     * @param int $newH
     * @param int $newW
     */
    private function resizeImageResource($imgResource, $newH, $newW)
    {
      $ret = $imgResource;
      $origSizes = $this->getImageSize($imgResource);
      $ratio = ($origSizes['width'] / $origSizes['height']);

      if (($newW / $newH) > $ratio) {
         $newW = ($newH * $ratio);
      } else {
         $newH = ($newW / $ratio);
      }

      if ($NewImageResource = imagecreatetruecolor($newW, $newH)) {
        imagealphablending($NewImageResource, false);
        imagesavealpha($NewImageResource, true);
        if (imagecopyresampled($NewImageResource, $imgResource, 0, 0, 0, 0, $newW, $newH, $origSizes['width'], $origSizes['height'])) {
          $ret = $NewImageResource;
        }
      }

      return $ret;
    }

}
