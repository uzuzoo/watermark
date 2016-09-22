<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Watermark Example</title>
  </head>
  <body>

<?php

include_once '../watermark.php';

try {
  // Options
  $params = array(
    'InputFile'           => 'images/image.jpg',
    'OutputPath'          => 'tempimages/',
    'FontsPath'           => '../watermark/fonts',
    'OutputFilePrefix'    => 'watermark',
    'OutputFileOverwrite' => TRUE,
    'WmFont'              => 'vera.ttf',
    'WmFontSize'          => 20,
    'WmFontAngle'         => 0,
    'WmFontColour'        => Watermark::FONT_COLOUR_BLACK,
    'WmPosition'          => Watermark::POS_CENTERED,
    'WmPadding'           => 5,
    'WmType'              => Watermark::WM_TYPE_IMAGE,
    'WmText'              => 'Another Watermark',
    'WmImage'             => 'images/watermark.jpg',
    'WmImageHeight'       => 300,
    'WmImageWidth'        => 300,
    'WmImageOpacity'      => 70,
  );

  // Options can be passed into the constructor as an array
  $wm = new Watermark($params);

  // Options can be set by their setters
  $wm->setInputFile($params['InputFile']);
  $wm->setOutputPath($params['OutputPath']);
  $wm->setFontsPath($params['FontsPath']);
  $wm->setOutputFilePrefix($params['OutputFilePrefix']);
  $wm->setOutputFileOverwrite($params['OutputFileOverwrite']);
  $wm->setWmFont($params['WmFont']);
  $wm->setWmFontSize($params['WmFontSize']);
  $wm->setWmFontAngle($params['WmFontAngle']);
  $wm->setWmFontColour($params['WmFontColour']);
  $wm->setWmPosition($params['WmPosition']);
  $wm->setWmPadding($params['WmPadding']);
  $wm->setWmType($params['WmType']);
  $wm->setWmText($params['WmText']);
  $wm->setWmImage($params['WmImage']);
  $wm->setWmImageHeight($params['WmImageHeight']);
  $wm->setWmImageWidth($params['WmImageWidth']);
  $wm->setWmImageOpacity($params['WmImageOpacity']);


  // Options can be set by chaining their setters
  $wm->setInputFile($params['InputFile'])
  ->setOutputPath($params['OutputPath'])
  ->setFontsPath($params['FontsPath'])
  ->setOutputFilePrefix($params['OutputFilePrefix'])
  ->setOutputFileOverwrite($params['OutputFileOverwrite'])
  ->setWmFont($params['WmFont'])
  ->setWmFontSize($params['WmFontSize'])
  ->setWmFontAngle($params['WmFontAngle'])
  ->setWmFontColour($params['WmFontColour'])
  ->setWmPosition($params['WmPosition'])
  ->setWmPadding($params['WmPadding'])
  ->setWmType($params['WmType'])
  ->setWmText($params['WmText'])
  ->setWmImage($params['WmImage'])
  ->setWmImageHeight($params['WmImageHeight'])
  ->setWmImageWidth($params['WmImageWidth'])
  ->setWmImageOpacity($params['WmImageOpacity']);

  // Options can be passed into the apply method
  $result = $wm->apply($params);

  // $output will contain the path & filename of the created watermarked image
  $output = $wm->getOutput();

  #$wm->installFont('./temp/vera.ttf');
  #$wm->uninstallFont('vera.ttf');

} catch (Exception $e) {
  // Any validation errors will cause an Exception to be thrown
  echo $e->getMessage();
  die();
}

$i = 1;
?>
  <div>
    <figure style="float:left;">
      <figcaption>Fig<?php echo $i++;?>. - Input Image.</figcaption>
      <img src="<?php echo $params['InputFile'];?>" alt="Original Image" style="Height:300px;"/>
    </figure>

    <?php if ($params['WmType'] == Watermark::WM_TYPE_IMAGE): ?>
      <figure style="float:left;">
        <figcaption>Fig<?php echo $i++;?>. - Watermark Image (on grey).</figcaption>
        <div style="background-color:#cccccc;padding:10px;">
          <img src="<?php echo $params['WmImage'];?>" alt="Watermark Image" style="max-height:300px;"/>
        </div>
      </figure>
    <?php endif; ?>

  <?php if ($result): ?>
    <figure style="float:left;">
      <figcaption>Fig<?php echo $i++;?>. - Output Image.</figcaption>
      <img src="<?php echo $output;?>" alt="Result Image" style="Height:300px;"/>
    </figure>
  <?php endif;?>
</div>


  </body>
</html>
