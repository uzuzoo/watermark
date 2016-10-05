<?php
/**
 * Watermark Files of different Types
 * requires GD Library
 *
 * Based on https://github.com/zeanwork/Watermark/blob/master/watermark.php
 *
 * @author Alex Wright [alec@intrica.net]
 * @version 1.0.0
 * @copyright Copyright 2016, intrica.net
 */

 namespace Uzuzoo\Watermark;
 use Uzuzoo\Watermark\Watermark\Image;

class Watermark
{
  /**
   * @var string Path to the Input File
   */
  public $InputFile = '';

  /**
   * @var string Original Filename
   */
  private $InputFileName = '';

  /**
   * @var string Original File Extension
   */
  private $InputFileExt = '';

  /**
   * @var string Original File Type
   */
  private $InputFileType = '';

  /**
   * @var string Path to where the resulting file will be saved
   */
  public $OutputPath = 'images/';

  /**
   * @var string prefix for the output Filename
   * EG: input "image.jpg" output "watermark.image.jpg"
   */
  public $OutputFilePrefix = 'watermark';

  /**
   * @var bool Should the Output File overwrite Existing Files
   */
  public $OutputFileOverwrite = FALSE;

  /**
   * @var string Output Filename
   */
  private $OutputFileName = '';

  /**
   * Path to the Fonts
   * @var string
   */
  public $FontsPath = __DIR__.'/watermark/fonts/';

  /**
   * @var string
   */
  public $WmFont = 'arial.ttf';

  /**
   * @var int Font Size
   */
  public $WmFontSize = 12;
  /**
   * @var mixed Angle max 360
   */
  public $WmFontAngle = 0;

  /**
   * @var string Comma Delimited RED,BLUE,GREEN max 255
   */
  public $WmFontColour = self::FONT_COLOUR_BLACK;

  /**
   * @var int where is the watermark to be positioned
   */
  public $WmPosition = self::POS_CENTERED;

  /**
   * @var int Padding around the watermark
   */
  public $WmPadding = 10;

  /**
   * @var int Watermark Type
   * 1 = Image
   * 2 = Text (default)
   */
  public $WmType = self::WM_TYPE_TEXT;

  /**
   * @var string Text to use for the watermark
   */
  public $WmText = 'Sample Watermark';

  /**
   * @var string Watermark Image
   */
  public $WmImage = '';

  /**
   * @var string Watermark Image Extension
   */
  public $WmImageExt = '';

  /**
   * @var string Watermark Image Height
   */
  public $WmImageHeight = 100;

  /**
   * @var string Watermark Image
   */
  public $WmImageWidth = 100;

  /**
   * @var int 0 to 100: 100 is Opaque
   */
  public $WmImageOpacity = 100;

  /**
  * @var int Minimum Height for the Watermark Image
  */
  private $minHeight = 10;

  /**
  * @var int Minimum Width for the Watermark Image
  */
  private $minWidth = 10;

  /**
   * @var int Minimum Opacity
   */
  private $minOpacity = 0;

  /**
   * @var int Maximum Opacity
   */
  private $maxOpacity = 100;

  /**
   * @var int Font Size
   */
  private $minFontSize = 10;
  /**
   * @var int Minimum Font Angle
   */
  private $minFontAngle = -360;
  /**
   * @var int Maximum Font Angle
   */
  private $maxFontAngle = 360;
  /**
   * @var int Minimum Font Colour For RGB values
   */
  private $minFontColour = 0;
  /**
   * @var int Maximum Font Colour For RGB values
   */
  private $maxFontColour = 255;



  const WM_TYPE_IMAGE     = 1;
  const WM_TYPE_TEXT      = 2;

  const POS_CENTERED      = 1;
  const POS_TOP_LEFT      = 2;
  const POS_TOP_CENTER    = 3;
  const POS_TOP_RIGHT     = 4;
  const POS_MIDDLE_LEFT   = 5;
  const POS_MIDDLE_RIGHT  = 6;
  const POS_BOTTOM_LEFT   = 7;
  const POS_BOTTOM_CENTER = 8;
  const POS_BOTTOM_RIGHT  = 9;

  const FILETYPE_IMAGE    = 1;

  const FONT_COLOUR_BLACK = '0,0,0';
  const FONT_COLOUR_GREY = '128,128,128';
  const FONT_COLOUR_WHITE = '255,255,255';

  private $types = array(
    'Image'         => self::WM_TYPE_IMAGE,
    'Text'          => self::WM_TYPE_TEXT,
  );
  private $positions = array(
    'Centered'      => self::POS_CENTERED,
    'Top-Left'      => self::POS_TOP_LEFT,
    'Top-Center'    => self::POS_TOP_CENTER,
    'Top-Right'     => self::POS_TOP_RIGHT,
    'Middle-Left'   => self::POS_MIDDLE_LEFT,
    'Middle-Right'  => self::POS_MIDDLE_RIGHT,
    'Bottom-Left'   => self::POS_BOTTOM_LEFT,
    'Bottom-Center' => self::POS_BOTTOM_CENTER,
    'Bottom-Right'  => self::POS_BOTTOM_RIGHT,
  );

  private $fontExt = array(
    'ttf'
  );
  private $fonts = array();

  private $inputExt = array(
    'jpg'   => array(
      'filetype'  => self::FILETYPE_IMAGE,
      'mimetypes' => array(
        'image/jpeg'
      ),
    ),
    'gif'   => array(
      'filetype'  => self::FILETYPE_IMAGE,
      'mimetypes' => array(
        'image/gif'
      ),
    ),
    'png'   => array(
      'filetype'  => self::FILETYPE_IMAGE,
      'mimetypes' => array(
        'image/png'
      ),
    ),
  );

  private $watermarkExt = array(
    'jpg'   => array(
      'image/jpeg',
    ),
    'gif'   => array(
      'image/gif'
    ),
    'png'   => array(
      'image/png'
    ),
  );




  public function __construct($params = array())
  {
    // Check the GD Library Exists
    if (!$this->gdLibraryExists()) {
      throw new Exception("Error: GD Library is not installed on this web sever");
    }
    $this->setFonts();
    $this->setParams($params);
  }


  /**
   * Select Type of Watermarking based upon Current Settings
   * @param array $params
   */
  public function apply($params = array())
  {
    $ret = FALSE;
    $this->setParams($params);
    $this->validate();

    if ($this->isInputAnImage()) {
      if(($WmProcess = new Image($this)) && ($WmProcess->process())) {
          $ret = TRUE;
      } else {
        # ????????
        # Need to get any process errors
        # ????????
      }
    } else {
      throw new Exception("Error: Input File of type ".$this->getInputFileType()." is not currently supported for watermarking.");
    }
    return $ret;
  }




  /**
   * Validate all Relevant settings before processing
   */
  private function validate()
  {
    // Lets set any extra settings
    $pathInfo = pathinfo($this->getInputFile());
    if ((!$this->getInputFile()) || (!$pathInfo['basename']) || (!isset($pathInfo['extension']))) {
      throw new Exception("(InputFile) Error: The InputFile ".$this->getInputFile()." could not be successfully used.");
    }

    if ($fileType = $this->getFileType($this->getInputFile())) {
      $this->setInputFileName(strtolower($pathInfo['basename']));
      $this->setInputFileExt(strtolower($pathInfo['extension']));
      $this->setInputFileType($fileType);
    } else {
      throw new Exception("(InputFile) Error: The InputFile ".$this->getInputFile()." does has an unsupported Mime Type.");
    }
    // Set the OutputFileName
    $this->setOutputFileName($this->createOutputFileName());

    // CHECK COMMON SETTINGS
    $this->validateCommon();

    // CHECK SPECIFIC SETTINGS
    switch ($this->getWmType()) {
      case self::WM_TYPE_TEXT:
        $this->validateImageText();
        break;

      case self::WM_TYPE_IMAGE:
        $this->validateImageImage();
        break;

      default:
        throw new Exception("Error: An Invalid Type has been specified.");
        break;
    }
  }


  private function validateCommon()
  {
    // Check InputFile has supported extension
    if (!array_key_exists($this->getInputFileExt(), $this->inputExt)) {
      throw new Exception("(InputFile) Error: The InputFile ".$this->getInputFile()." has an unsupported Extention of (".$this->getInputFileExt()."). Supported Extensions are [".implode("|", array_keys($this->inputExt))."].");
    }

    // Check InputFile has supported mime type
    if (!in_array($this->getInputFileType(), $this->inputExt[$this->getInputFileExt()]['mimetypes'])) {
      throw new Exception("(InputFile) Error: The InputFile ".$this->getInputFile()." has an unsupported Mime Type of (".$this->getInputFileType()."). Supported Mime Types for ".$this->getInputFileExt()." are [".implode("|", $this->inputExt[$this->getInputFileExt()]['mimetypes'])."].");
    }

    // Check InputFile is readable
    if ((!file_exists($this->getInputFile())) || (!is_readable($this->getInputFile()))) {
      throw new Exception("(InputFile) Error: The InputFile ".$this->getInputFile()." does not exist or is not readable.");
    }

    // Check OutputPath exists and is writable
    if ((!is_dir($this->getOutputPath()) || (!is_writable($this->getOutputPath())))) {
      throw new Exception("(OutputPath) Error: The OutputPath ".$this->getOutputPath()." does not exist or is not writable.");
    }

    // Check that the resulting OutputFile Name doesn't already exist
    if (!$this->getOutputFileOverwrite()) {
      if (file_exists($this->getOutputPath().$this->getOutputFileName())) {
        throw new Exception("(OutputPath) Error: The OutputPath ".$this->getOutputPath()." already has a file called ".$this->getOutputFileName()." and overwriting is disabled.");
      }
    }

    // check valid Position
    if (!in_array($this->getWmPosition(), $this->positions)) {
      throw new Exception("(WmPosition) Error: The position has an invalid value of ".$this->getWmPosition().".");
    }

    // check valid Padding
    if (!is_int($this->getWmPadding())) {
      throw new Exception("(WmPadding) Error: The padding must be an integer.");
    }
  }


  private function validateImageText()
  {
    // check font is installed
    if (!in_array($this->getWmFont(), $this->fonts)) {
      throw new Exception("(WmFont) Error: The Font ".$this->getWmFont()." is not installed.");
    }
    // Check if FontSize is set
    if ((!is_int($this->getWmFontSize())) || (!($this->getWmFontSize() >= $this->minFontSize))) {
      throw new Exception("(WmFontSize) Error: The Font Size has an invalid value of ".$this->getWmFontSize().". Valid values are an integer ".$this->minFontSize." or above.");
    }
    // Check Font Angle is set
    if ((!is_int($this->getWmFontAngle())) || (!($this->getWmFontAngle() >= $this->minFontAngle)) || (!($this->getWmFontAngle() <= $this->maxFontAngle))) {
      throw new Exception("(WmFontAngle) Error: The Font Angle has an invalid value of ".$this->getWmFontAngle().". Valid values are integers between ".$this->minFontAngle." and ".$this->maxFontAngle.".");
    }
    // Check FontColour has valid values
    if (($colourParts = explode(",", $this->getWmFontColour())) && (count($colourParts) == 3)) {
      foreach ($colourParts as $key => $colourValue) {
        if ((!($colourValue >= $this->minFontColour)) || (!($colourValue <= $this->maxFontColour))) {
          throw new Exception("(WmFontColour) Error: The Font Colour must have 3 values between ".$this->minFontColour." and ".$this->maxFontColour.". Submitted values '".$this->getWmFontColour()."'.");
        }
      }
    } else {
      throw new Exception("(WmFontColour) Error: The Font Colour must have 3 values for RGB delimited by ',' comma. Submitted values '".$this->getWmFontColour()."'.");
    }
  }

  private function validateImageImage()
  {
    $pathInfo = pathinfo($this->getWmImage());
    if ((!$this->getWmImage()) || (!$pathInfo['basename']) || (!isset($pathInfo['extension']))) {
      throw new Exception("(WmImage) Error: The Watermark Image ".$this->getWmImage()." could not be successfully used.");
    }
    $this->setWmImageExt(strtolower($pathInfo['extension']));

    // Check Watermark File Exists and is readable
    if ((!file_exists($this->getWmImage())) || (!is_readable($this->getWmImage()))) {
      throw new Exception("(InputFile) Error: The Watermark Image ".$this->getWmImage()." does not exist or is not readable.");
    }

    // Check Watermark File has supported extension
    if (!array_key_exists($this->getWmImageExt(), $this->watermarkExt)) {
      throw new Exception("(WmImage) Error: The Watermark Image ".$this->getWmImage()." has an unsupported Extention of (".$this->getWmImageExt()."). Supported Extensions are [".implode("|", array_keys($this->watermarkExt))."].");
    }

    // Check Watermark File has supported mime type
    $fileType = $this->getFileType($this->getWmImage());
    if (!in_array($fileType, $this->watermarkExt[$this->getWmImageExt()])) {
      throw new Exception("(WmImage) Error: The Watermark Image ".$this->getWmImage()." has an unsupported Mime Type of (".$fileType."). Supported Mime Types for ".$this->getWmImageExt()." are [".implode("|", $this->watermarkExt[$this->getWmImageExt()])."].");
    }

    // Check Watermark Height
    if ((!is_int($this->getWmImageHeight())) || (!($this->getWmImageHeight() >= $this->minHeight))) {
      throw new Exception("(WmImageHeight) Error: The Watermark Image Height has an invalid value. A valid value must be an integer of ".$this->minHeight." or above.");
    }

    // Check Watermark Width
    if ((!is_int($this->getWmImageWidth())) || (!($this->getWmImageWidth() >= $this->minWidth))) {
      throw new Exception("(WmImageWidth) Error: The Watermark Image Width has an invalid value. A valid value must be an integer of ".$this->minWidth." or above.");
    }

    // Check Watermark Opacity
    if ((!is_int($this->getWmImageOpacity())) || (($this->getWmImageOpacity() < $this->minOpacity)) || (($this->getWmImageOpacity() > $this->maxOpacity))) {
      throw new Exception("(WmImageWidth) Error: The Watermark Image Opacity has an invalid value. A valid value must be an integer between ".$this->minOpacity." and ".$this->maxOpacity.".");
    }
  }


  #----------------------------------------------------
  # WATERMARK OUTPUT FILE
  #----------------------------------------------------
  /**
   * @return string
   */
  public function getOutputFilePrefix()
  {
    return $this->OutputFilePrefix;
  }

  /**
   * @param string $OutputFilePrefix
   * @return static
   */
  public function setOutputFilePrefix($OutputFilePrefix)
  {
    $this->OutputFilePrefix = $OutputFilePrefix;
    return $this;
  }

  /**
   * Create the Output Filename
   */
  private function createOutputFileName()
  {
    return ((strlen($this->getOutputFilePrefix())) ? implode(".", array($this->getOutputFilePrefix(), $this->getInputFileName())) : $this->getInputFileName());
  }

  /**
   * @return string
   */
  public function getOutputFileName()
  {
    return $this->OutputFileName;
  }

  /**
   * @param string $OutputFileName
   * @return static
   */
  public function setOutputFileName($OutputFileName)
  {
    $this->OutputFileName = $OutputFileName;
    return $this;
  }

  public function getOutput()
  {
    return implode("", array($this->getOutputPath(), $this->getOutputFileName()));
  }

  #----------------------------------------------------
  # WATERMARK POSITION
  #----------------------------------------------------
  /**
   * @return void
   */
  public function getWmPosition()
  {
    return $this->WmPosition;
  }

  /**
   * @param int $WmPosition
   * @return static
   */
  public function setWmPosition($WmPosition)
  {
    $this->WmPosition = $WmPosition;
    return $this;
  }

  #----------------------------------------------------
  # WATERMARK PADDING
  #----------------------------------------------------
  /**
   * @return int
   */
  public function getWmPadding()
  {
    return $this->WmPadding;
  }

  /**
   * @param int $WmPadding
   * @return static
   */
  public function setWmPadding($WmPadding)
  {
    $this->WmPadding = $WmPadding;
    return $this;
  }


  #----------------------------------------------------
  # WATERMARK INPUT FILE
  #----------------------------------------------------
  /**
   * @return string Path to Input File
   */
  public function getInputFile()
  {
    return $this->InputFile;
  }

  /**
   * @param string $InputFile
   * @return static
   */
  public function setInputFile($InputFile)
  {
    $this->InputFile = $InputFile;
    return $this;
  }

  /**
   * @return string
   */
  public function getInputFileName()
  {
    return $this->InputFileName;
  }

  /**
   * @param string $InputFileName
   * @return static
   */
  public function setInputFileName($InputFileName)
  {
    $this->InputFileName = $InputFileName;
    return $this;
  }

  /**
   * @return string
   */
  public function getInputFileExt()
  {
    return $this->InputFileExt;
  }

  /**
   * @param string $InputFileExt
   * @return static
   */
  public function setInputFileExt($InputFileExt)
  {
    $this->InputFileExt = $InputFileExt;
    return $this;
  }

  /**
   * @return string
   */
  public function getInputFileType()
  {
    return $this->InputFileType;
  }

  /**
   * @param string $InputFileType
   * @return static
   */
  public function setInputFileType($InputFileType)
  {
    $this->InputFileType = $InputFileType;
    return $this;
  }

  public function getFileType($filename)
  {
    $ret = '';
    if (function_exists('finfo_open')) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $ret = finfo_file($finfo, $filename);
    } elseif (function_exists('mime_content_type')) {
      if ($type = mime_content_type($filename)) {
        $ret = $type;
      }
    }
    return $ret;
  }

  /**
   * Checks whether or not the input file is an image
   */
  private function isInputAnImage()
  {
    return (($this->inputExt[$this->getInputFileExt()]['filetype'] == self::FILETYPE_IMAGE) ? TRUE : FALSE);
  }

  #----------------------------------------------------
  # OUTPUT IMAGE
  #----------------------------------------------------
  /**
   * @return string Destination for resulting File
   */
  public function getOutputPath()
  {
    return $this->OutputPath;
  }

  /**
   * @param string $OutputPath
   * @return static
   */
  public function setOutputPath($OutputPath)
  {
    $this->OutputPath = $this->addTrailingSlash($OutputPath);
    return $this;
  }

  /**
   * @return bool
   */
  public function getOutputFileOverwrite()
  {
    return $this->OutputFileOverwrite;
  }

  /**
   * @param bool $OutputFileOverwrite
   * @return static
   */
  public function setOutputFileOverwrite($OutputFileOverwrite)
  {
    $this->OutputFileOverwrite = (($OutputFileOverwrite === TRUE) ? TRUE : FALSE);
    return $this;
  }
  #----------------------------------------------------
  # WATERMARK TYPE
  #----------------------------------------------------
  /**
   * @return int
   */
  public function getWmType()
  {
    return $this->WmType;
  }

  /**
   * @param int $WmType
   * @return static
   */
  public function setWmType($WmType)
  {
    $this->WmType = $WmType;
    return $this;
  }

  #----------------------------------------------------
  # WATERMARK IMAGE
  #----------------------------------------------------
  /**
   * @return string
   */
  public function getWmImage()
  {
    return $this->WmImage;
  }

  /**
   * @param string $WmImage
   * @return static
   */
  public function setWmImage($WmImage)
  {
    $this->WmImage = $WmImage;
    return $this;
  }

  /**
   * @return string
   */
  public function getWmImageHeight()
  {
    return $this->WmImageHeight;
  }

  /**
   * @param string $WmImageHeight
   * @return static
   */
  public function setWmImageHeight($WmImageHeight)
  {
    $this->WmImageHeight = intval($WmImageHeight);
    return $this;
  }

  /**
   * @return string
   */
  public function getWmImageWidth()
  {
    return $this->WmImageWidth;
  }

  /**
   * @param string $WmImageWidth
   * @return static
   */
  public function setWmImageWidth($WmImageWidth)
  {
    $this->WmImageWidth = intval($WmImageWidth);
    return $this;
  }

  /**
   * @return string
   */
  public function getWmImageExt()
  {
    return $this->WmImageExt;
  }

  /**
   * @param string $WmImageExt
   * @return static
   */
  public function setWmImageExt($WmImageExt)
  {
    $this->WmImageExt = $WmImageExt;
    return $this;
  }

  /**
   * @return int
   */
  public function getWmImageOpacity()
  {
    return $this->WmImageOpacity;
  }

  /**
   * @param int $WmImageOpacity
   * @return static
   */
  public function setWmImageOpacity($WmImageOpacity)
  {
    $this->WmImageOpacity = $WmImageOpacity;
    return $this;
  }

  #----------------------------------------------------
  # WATERMARK TEXT
  #----------------------------------------------------
  /**
   * @return string
   */
  public function getWmText()
  {
    return $this->WmText;
  }

  /**
   * @param string $WmText
   * @return static
   */
  public function setWmText($WmText)
  {
    $this->WmText = $WmText;
    return $this;
  }




  #----------------------------------------------------
  # WATERMARK FONTS
  #----------------------------------------------------
  /**
   * @return string
   */
  public function getWmFont()
  {
    return $this->WmFont;
  }

  /**
   * @param string $WmFont
   * @return static
   */
  public function setWmFont($WmFont)
  {
    $this->WmFont = $WmFont;
    return $this;
  }

  /**
   * @return int
   */
  public function getWmFontSize()
  {
    return $this->WmFontSize;
  }

  /**
   * @param int $WmFontSize
   * @return static
   */
  public function setWmFontSize($WmFontSize)
  {
    $this->WmFontSize = $WmFontSize;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getWmFontAngle()
  {
    return $this->WmFontAngle;
  }

  /**
   * @param mixed $WmFontAngle
   * @return static
   */
  public function setWmFontAngle($WmFontAngle)
  {
    $this->WmFontAngle = $WmFontAngle;
    return $this;
  }

  /**
   * @return string
   */
  public function getWmFontColour()
  {
    return $this->WmFontColour;
  }

  /**
   * @param string $WmFontColour
   * @return static
   */
  public function setWmFontColour($WmFontColour)
  {
    $this->WmFontColour = $WmFontColour;
    return $this;
  }

  /**
   * @return string
   */
  public function getFontsPath()
  {
    return $this->FontsPath;
  }

  public function getFontPathFilename()
  {
    return implode("", array($this->getFontsPath(), $this->getWmFont()));
  }

  /**
   * @param string $FontsPath
   * @return static
   */
  public function setFontsPath($FontsPath)
  {
    $this->FontsPath = $this->addTrailingSlash($FontsPath);
    return $this;
  }

  private function addFont($font)
  {
    $this->fonts[] = $font;
  }


  /**
   * Set the currently available fonts
   */
  private function setFonts()
  {
    $this->fonts = array();
    if ($handle = opendir($this->getFontsPath())) {
      while (false !== ($entry = readdir($handle))) {
        if (($entry != ".") && ($entry != "..")) {
          $this->addFont($entry);
        }
      }
      closedir($handle);
    }
  }


  /**
   * @param string $fontFile Path and Filename to the Font File to Install
   */
  public function installFont($fontFile)
  {
    // Check that the fontFile exists and is readable
    if ((!file_exists($fontFile)) || (!is_file($fontFile)) || (!is_readable($fontFile))) {
      throw new Exception("Error: The FontFile ".$fontFile." does not exist, or is not readable.");
    }

    $pathInfo = pathinfo($fontFile);
    // Check that the fontFile has a valid extension
    if (!in_array($pathInfo['extension'], $this->fontExt)) {
      throw new Exception("Error: The FontFile ".$fontFile." must be a TrueType Font and have an extension of (".implode("|", $this->fontExt).").");
    }

    // Check that the fontFile doesn't already exist
    if (file_exists($this->getFontsPath().$pathInfo['basename'])) {
      throw new Exception("Error: There is already a font installed with the name ".$pathInfo['basename'].".");
    }

    // Copy FontFile to Fonts Folder
    if((!($font = file_get_contents($fontFile))) || (!(file_put_contents($this->getFontsPath().$pathInfo['basename'], $font))))
    {
      throw new Exception("Error: There was a problem Installing FontFile ".$fontFile." into folder ".$this->getFontsPath());
    }

    // Refrash the fonts list
    $this->setFonts();
  }

  public function uninstallFont($fontName)
  {
    // Check the font name is Installed
    if (!in_array($fontName, $this->fonts)) {
      throw new Exception("Error: Font ".$fontName." does not seem to be installed.");
    }
    // Check that the fontFile exists and the font directory is writable
    if ((!file_exists($this->getFontsPath().$fontName)) ||  (!is_writable($this->getFontsPath()))) {
      throw new Exception("Error: Font ".$fontName." does not exist or can not be uninstalled.");
    }
    if (!unlink($this->getFontsPath().$fontName)) {
      throw new Exception("Error: There was a problem uninstalling the font ".$fontName.".");
    }
  }

  #----------------------------------------------------
  # WATERMARK HELPERS
  #----------------------------------------------------
  /**
   * @param string $value Add a Trailing Slash to any Directory Paths
   */
  private function addTrailingSlash($value)
  {
    return rtrim($value, "/")."/";
  }

  /**
   * Check to see if the GD Library Exists
   * @return bool
   */
  private function gdLibraryExists()
  {
      return (((extension_loaded('gd')) && function_exists('gd_info')) ? TRUE : FALSE);
  }

  /**
   * Set any Passed Parameters
   * @param array $params
   */
  private function setParams($params)
  {
    if (($params) && (is_array($params))) {
      foreach ($params as $property => $paramValue) {
        $methodName = 'set'.$property;
        if ((property_exists($this, $property)) && (method_exists($this, $methodName))) {
          $this->$methodName($paramValue);
        }
      }
    }
  }

}
