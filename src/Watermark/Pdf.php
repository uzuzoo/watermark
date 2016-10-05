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

class Pdf
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
