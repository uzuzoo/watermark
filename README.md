# Watermark

This package was created to allow watermarking of various file types.

### Features
- 9 watermark positions
- 2 types of watermark [text & image]
- watermark padding
- image watermark resize
- image watermark opacity
- TrueType Font text watermark size
- TrueType Font text watermark colour
- TrueType Font text watermark angle: 360 degree rotation


## Supported File types

| Images        | Documents   | Others      |
| ------------- |-------------|-------------|
| jpg           | pdf         | More to come|
| gif           | &nbsp;      | &nbsp;      |
| png           | &nbsp;      | &nbsp;      |


## Supported Watermark Images
| Images        | info      |
| ------------- |-------------|
| jpg           | |
| gif           | alpha transparency supported|
| png           | alpha transparency supported|

## Supported Watermark Text
As an alternative to a Watermark image, support is also available for TrueType Font watermarking with 360 degrees of rotation on the text.
There are also methods to install and uninstall TrueType Fonts.

> NOTE: When the InputFile is a PDF and WmType is Text, the 'WmText' Text will be converted to an Image before being placed on the PDF!


## Basic Usage
`$params` can be specified on `$wm = new Watermark($params);`
<br>or can be set with setters `$wm->setInputFile($params['InputFile']);`
<br>or optionally be sent on the `$wm->apply($params);` method
<br>or a mixture of either.
```php
<?php
use Uzuzoo\Watermark\Watermark;

$params = array(
  'InputFile' => 'images/image.jpg',
);

// Start the Watermarking process
try {

  // Instantiate the Watermark Class
  $wm = new Watermark($params);

  // Apply the Watermarking
  $result = $wm->apply();

  // Return the watermarked File path & filename
  $output = $wm->getOutput();

} catch (Exception $e) {

  // Any validation errors will cause an Exception to be thrown
  echo $e->getMessage();

}

```

## Options
Options can be passed, as an array, into the constructor of Watermark or on the apply method.<br>
Alternatively, each option has a getter and setter, and can used by prefixing the option name with set or get respectively. E.G. to set the InputFile use `$wm->setInputFile()` and to get `$wm->getInputFile()`

| Option Name |  Default | Type | Description |
|-------------|----------|------|-------------|
| InputFile   |         | string| The File that is to have the watermark applied              
| OutputPath  | | string | Path to where the resulting file is to be saved. If not specified it will try to save in the same directory as the InputFile     
| FontsPath  | [Package]/Watermark/Fonts/ | string | Path to the TrueType Fonts.    
| OutputFilePrefix  | "watermark" |string| Prefix for the output filename    
| OutputFileOverwrite  | FALSE |boolean| Allow overwriting of existing files for the output
| WmFont | "arial.ttf" |string| TrueType Font to Use |
| WmFontSize | 12 |integer| Size of the TrueType Font Watermark. Minimum 10|
| WmFontAngle | 0 |integer| Number of degrees to rotate the text, 0-360.<br>0 = Horizontal |
| WmFontColour | "0,0,0" |string| Colour of the watermark text. RGB colour values. Valid values are 0-255. <br>Class Constants:<br>FONT_COLOUR_BLACK = "0,0,0"<br>FONT_COLOUR_GREY = '128,128,128'<br>FONT_COLOUR_WHITE = '255,255,255'|
| WmPosition | 1 |integer| Position of the Watermark.<br>Class Constants:<br>POS_CENTERED = 1<br>POS_TOP_LEFT = 2<br>POS_TOP_CENTER = 3<br>POS_TOP_RIGHT = 4<br>POS_MIDDLE_LEFT = 5<br>POS_MIDDLE_RIGHT = 6<br>POS_BOTTOM_LEFT = 7<br>POS_BOTTOM_CENTER = 8<br>POS_BOTTOM_RIGHT  = 9|
| WmPadding | 10 |integer| Padding around the watermark |
| WmType | 2 |integer| Image Watermark or Text Watermark<br>Class Constants:<br>WM_TYPE_IMAGE = 1<br>WM_TYPE_TEXT = 2 |
| WmText | "Sample Watermark" |string| The text string for the watermark |
| WmImage |  |string| The File that is to be the watermark |
| WmImageHeight | 100 |integer| Height of the Watermark Image, keeps aspect ratio in combination with WmImageWidth. Minimum 10. |
| WmImageWidth | 100 |integer| Width of the Watermark Image, keeps aspect ratio in combination with WmImageHeight. Minimum 10. |
| WmImageOpacity | 100 |integer| How opaque the watermark image is 0-100. 100 is Opaque, 0 is Transparent |
