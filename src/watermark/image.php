<?php
namespace Uzuzoo\Watermark\WatermarkImage;
/**
 * Watermark Image Files of type jpg,png,gif
 * requires GD Library
 *
 *
 * @author Alex Wright [alec@intrica.net]
 * @version 1.0.0
 * @copyright Copyright 2016, intrica.net
 */
class WatermarkImage
{
  /**
   * @var Watermark container for the Watermark Class
   */
  private $Wm = FALSE;


  public function __construct(Watermark $Wm)
  {
    $this->Wm = $Wm;
  }

  /**
   * Process The Watermarking
   */
  public function process()
  {
    $ret = FALSE;
    $isImageWatermark = (($this->Wm->getWmType() == Watermark::WM_TYPE_IMAGE) ? TRUE : FALSE);

    // Load The Image
    $imSource = $this->loadImage($this->Wm->getInputFileExt(), $this->Wm->getInputFile());

    if ($isImageWatermark) {
      $wmSource = $this->loadImage($this->Wm->getWmImageExt(), $this->Wm->getWmImage());
      $wmSource = $this->resizeImageResource($wmSource, $this->Wm->getWmImageHeight(), $this->Wm->getWmImageWidth());
      // Get watermark Size
      $wmSizes = $this->getImageSize($wmSource);
      // Get Watermark Position
      $wmPosition = $this->getWmImagePosition($imSource, $this->Wm->getWmPosition(), $wmSizes, $this->Wm->getWmPadding());
      // Apply watermark
      $this->imagecopymerge_alpha($imSource, $wmSource, $wmPosition['x'], $wmPosition['y'], 0, 0, $wmSizes['width'], $wmSizes['height'], $this->Wm->getWmImageOpacity());
      # Destroy temp images
      imagedestroy($wmSource);
    } else {
      $colourParts = explode(",", $this->Wm->getWmFontColour());
      $colour = imagecolorallocate($imSource, $colourParts[0], $colourParts[1], $colourParts[2]);
      // Get Watermark Text Position
      $wmPosition = $this->getWmTextPosition($imSource, $this->Wm->getWmPosition(), $this->Wm->getWmFontSize(), $this->Wm->getWmFontAngle(), $this->Wm->getFontPathFilename(), $this->Wm->getWmText(), $this->Wm->getWmPadding());
      imagettftext($imSource, $this->Wm->getWmFontSize(), $this->Wm->getWmFontAngle(), $wmPosition['x'], $wmPosition['y'], $colour, $this->Wm->getFontPathFilename(), $this->Wm->getWmText());
    }
    // Save image
    $ret = $this->saveImage($this->Wm->getInputFileExt(), $imSource, $this->Wm->getOutput());

    # Destroy temp images
    imagedestroy($imSource);
    return $ret;
  }

  private function saveImage($ext, $imageResource, $filepath)
  {
    $ret = FALSE;
    switch ($ext) {
      case 'jpg':
        $ret = imagejpeg($imageResource, $filepath, 100);
        break;

      case 'gif':
        $ret = imagegif($imageResource, $filepath);
        break;

      case 'png':
        $ret = imagepng($imageResource, $filepath, 0);
        break;
    }
    return $ret;
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
   * Get the Position for a Watermark Image
   * @param mixed $imSource
   * @param mixed $position
   * @param mixed $wmSizes
   * @param mixed $padding
   * @return array [x,y] Position to place the waermark
   */
  private function getWmImagePosition($imSource, $position, $wmSizes, $padding)
  {
    $imSizes = $this->getImageSize($imSource);
    $posX = 0;
    $posY = 0;

    switch ($position) {

      # Centered
      case 1:
        $posX = ( $imSizes['width'] / 2 ) - ( $wmSizes['width'] / 2 );
        $posY = ( $imSizes['height'] / 2 ) - ( $wmSizes['height'] / 2 );
      break;

      # Top-Left
      case 2:
        $posX = $padding;
        $posY = $padding;
      break;

      # Top-Center
      case 3:
        $posX = (($imSizes['width'] - $wmSizes['width']) / 2);
  			$posY = $padding;
      break;

      # Top-Right
      case 4:
        $posX = (($imSizes['width'] - $wmSizes['width']) - $padding);
  			$posY = $padding;
      break;

      # Middle-Left
      case 5:
        $posX = $padding;
  			$posY = (($imSizes['height'] / 2) - ($wmSizes['height'] / 2));
      break;

      # Middle-Right
      case 6:
        $posX = (($imSizes['width'] - $wmSizes['width']) - $padding);
  			$posY = (($imSizes['height'] / 2) - ($wmSizes['height'] / 2));
      break;

      # Bottom-Left
      case 7:
        $posX = $padding;
  			$posY = (($imSizes['height'] - $wmSizes['height']) - $padding);
      break;

      # Bottom-Center
      case 8:
        $posX = (($imSizes['width'] - $wmSizes['width']) / 2);
			  $posY = (($imSizes['height'] - $wmSizes['height']) - $padding);
      break;

      # Bottom-Right
      case 9:
        $posX = (($imSizes['width'] - $wmSizes['width']) - $padding);
  			$posY = (($imSizes['height'] - $wmSizes['height']) - $padding);
      break;
    }
    return array('x' => $posX, 'y' => $posY);
  }


  /**
   * Get the Position for a Watermark Text String
   * @param mixed $imSource
   * @param mixed $position
   * @param mixed $fontSize
   * @param mixed $fontAngle
   * @param mixed $font
   * @param mixed $text
   * @param mixed $padding
   */
  private function getWmTextPosition($imSource, $position, $fontSize, $fontAngle, $font, $text, $padding)
  {
    $bbox = imagettfbbox($fontSize, $fontAngle, $font, $text);
    $minX = min(array($bbox[0],$bbox[2],$bbox[4],$bbox[6]));
    $maxX = max(array($bbox[0],$bbox[2],$bbox[4],$bbox[6]));
    $minY = min(array($bbox[1],$bbox[3],$bbox[5],$bbox[7]));
    $maxY = max(array($bbox[1],$bbox[3],$bbox[5],$bbox[7]));

    // Bottom Left Corner of Text regardless of angle
    $textX = $bbox[0];
    $textY = $bbox[1];

    // Dimensions of Container Box
    $width = ($maxX - $minX);
    $height = ($maxY - $minY);

    // Top, Bottom, Left and Right Offsets
    $offset = array(
      't' => abs($textY - $minY),
      'b' => abs($textY - $maxY),
      'l' => abs($textX - $minX),
      'r' => abs($textX - $maxX),
    );

    $imSizes = $this->getImageSize($imSource);
    $posX = 0;
    $posY = 0;

    switch ($position) {

      # Centered
      case 1:
        $posX = ((($imSizes['width'] / 2) - ($width / 2)) + $offset['l']);
        $posY = ((($imSizes['height'] / 2) + ($height / 2)) - $offset['b']);
      break;

      # Top-Left
      case 2:
        $posX = ($offset['l'] + $padding);
        $posY = ($offset['t'] + $padding);
      break;

      # Top-Center
      case 3:
        $posX = ((($imSizes['width'] - $width) / 2) + $offset['l']);
        $posY = ($offset['t'] + $padding);

      break;

      # Top-Right
      case 4:
        $posX = ((($imSizes['width'] - $width) + $offset['l']) - $padding);
  			$posY = ($offset['t'] + $padding);
      break;

      # Middle-Left
      case 5:
        $posX = ($offset['l'] + $padding);
  			$posY = (($imSizes['height'] / 2) - ($height / 2) + $offset['t']);
      break;

      # Middle-Right
      case 6:
        $posX = ((($imSizes['width'] - $width) + $offset['l']) - $padding);
  			$posY = (($imSizes['height'] / 2) - ($height / 2) + $offset['t']);
      break;

      # Bottom-Left
      case 7:
        $posX = ($offset['l'] + $padding);
  			$posY = (($imSizes['height'] - $offset['b']) - $padding);
      break;

      # Bottom-Center
      case 8:
        $posX = ((($imSizes['width'] - $width) / 2) + $offset['l']);
			  $posY = (($imSizes['height'] - $offset['b']) - $padding);
      break;

      # Bottom-Right
      case 9:
        $posX = ((($imSizes['width'] - $width) + $offset['l']) - $padding);
  			$posY = (($imSizes['height'] - $offset['b']) - $padding);
      break;
    }

    return array('x' => $posX, 'y' => $posY);
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

  private function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
    // creating a cut resource
    $cut = imagecreatetruecolor($src_w, $src_h);
    // copying relevant section from background to the cut resource
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
    // copying relevant section from watermark to the cut resource
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
    // insert cut resource to destination image
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
}

}
