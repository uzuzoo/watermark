<?php
namespace uzuzoo\Watermark;
/**
 * Watermark PDF Files
 *
 *
 * @author Alex Wright [alec@intrica.net]
 * @version 1.0.0
 * @copyright Copyright 2016, intrica.net
 */
class WatermarkPdf
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

  }


}
