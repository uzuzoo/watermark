<?php

/**
 *
 */

namespace Uzuzoo\Watermark\Watermark;
use Uzuzoo\Watermark\Watermark;

abstract class WmAbstract
{

  /**
   * @var Watermark container for the Watermark Class
   */
  protected $Wm = FALSE;

  public function __construct(Watermark $Wm)
  {
    $this->Wm = $Wm;
  }


  /**
   * Process the Watermark
   * @return bool
   */
  abstract function process();
}
