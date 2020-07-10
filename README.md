# PHP Pdfcrop

[![Build Status](https://secure.travis-ci.org/bolongo/phppdfcrop.png)](http://travis-ci.org/bolongo/phppdfcrop)
[![Latest Stable Version](https://poser.pugx.org/bolongo/phppdfcrop/v/stable.svg)](https://packagist.org/packages/bolongo/phppdfcrop)
[![Total Downloads](https://poser.pugx.org/bolongo/phppdfcrop/downloads)](https://packagist.org/packages/bolongo/phppdfcrop)
[![License](https://poser.pugx.org/bolongo/phppdfcrop/license.svg)](https://packagist.org/packages/bolongo/phppdfcrop)

PHP Pdfcrop is a PHP wrapper for [pdfcrop](http://pdfcrop.sourceforge.net/) based on
[PHP WkhtmlToPdf](https://github.com/mikehaertl/phpwkhtmltopdf) by [Michael Härtl](https://github.com/mikehaertl).
**The `pdfcrop` command must be installed in the system.**

## Installation

Install the package through [composer](https://getcomposer.org/):
```
composer require bolongo/phppdfcrop
```

Make sure you include the composer [autoloader](https://getcomposer.org/doc/01-basic-usage.md#autoloading)
somewhere in your codebase.

## Example
```php
// You can pass a filename or an options array to the constructor
$pdfCrop = new PdfCrop('/path/to/document.pdf');

try {
    $pdfCrop->saveAs('/path/to/cropped_document.pdf');
} catch (Exception $e) {
    //Handle error here
}
```

## Options
### Command options
These are used as options for the `pdfcrop` shell command. For a better explanation of these options, please see
`pdfcrop --help`.

```php
//Default values
$options = [
    'verbose' => false,//bool
    'debug' => false,//bool
    'gscmd' => 'gs',//string
    'tex-extension' => 'pdftex',//string
    'pdftexcmd' =>'pdftex',//string
    'xetexcmd' => 'xetex',//string
    'luatexcmd' => 'luatex',//string
    'margins' => [0, 0, 0, 0],//array|string
    'clip' => false,//bool
    'hires' => false,//bool
    'ini' => false,//bool

    'restricted' => false,//bool
    'papersize' => null,//string
    'resolution' => null,//string|int
    'bbox' => null,//string|array
    'bbox-odd' => null,//string|array
    'bbox-even' => null,//string|array
    'pdfversion' => null,//string
    
    'original' => null,//string
];
```

**Description**
- **verbose:** Makes the command do a verbose printing.
- **debug:** Makes the command print debug information.
- **gscmd:** Specifies the path to the ghostscript command to be used by the command.
- **tex-extension:** Specifies the tex extension to be used by the command. Value must be `pdftex`, `xetex` or `luatex`.
    This option is the union of `--pdftex`, `--xetex` and `luatex` options present in the `pdfcrop` shell command,
    in which only one of these must be specified.
- **pdftexcmd:** Specifies the path to the pdftex command to be used by the command.
- **xetexcmd:** Specifies the path to the xetex command to be used by the command.
- **luatexcmd:** Specifies the path to the luatex command to be used by the command.
- **margins:** Specifies extra margins to the command, unit is bp. If only one number is given, then it is used for all 
    margins, in the case of two numbers they are used for right and bottom.
- **clip:** Specifies clip support to the command if margins are set.
- **hires:** Specifies the use of `%%HiResBoundingBox` instead of `%%BoundingBox`.
- **ini:** Specifies the use of iniTeX variant of the TeX compiler to the command.
- **original:** File to be cropped by the command.

**How to set options to a PDFCrop instance:**

```php
$pdfCrop = new PDFCrop($options);
$pdfCrop->setOptions($options);
```

The **original** option is special, as it can be specified as a member of the array set on the constructor or the
`setOptions($options)` method, as a replacement of the array set on the constructor or directly on the attribute
`original`.

```php
$pdfCrop = new PDFCrop('/path/to/document.pdf');
$pdfCrop->original = '/path/to/document.pdf';
```

### Wrapper options
These options are specific to the wrapper. These options can be passed to the
    wrapper in the constructor or via the `setOptions($options)` method, mixed with the **Command Options**.

```php
//Default values
$commandOptions = [
    'binary' => 'pdfcrop',//string
    'tmpDir' => null,//string
    'ignoreWarnings' => true,//bool
    'ignoreOptionValidationErrors' => true,//bool
];
$pdfCrop = new PDFCrop($commandOptions);
$pdfCrop->setOptions($commandOptions);
$pdfCrop->binary = '/path/to/pdfcrop';
$pdfCrop->tmpDir = '/path/to/tmpDir';
$pdfCrop->ignoreWarnings = true;
$pdfCrop->ignoreOptionValidationErrors = true;
``` 

**Description**
- **binary:** path to the `pdfcrop` command.
- **tmpDir:** path to the tmp directory. Defaults to the PHP temp dir.
- **ignoreWarnings:** prevents the process from throwing exceptions.
- **ignoreOptionValidationErrors:** prevents the option validation from throwing exceptions (malformed options will be
    ignored).

## Error Handling

`new PDFCrop($options)` and `setOptions($options)` will throw exceptions if an option is malformed and the option
    `ignoreOptionValidationErrors` is set to `false`.

```php
$options = [
    'ignoreOptionValidationErrors' => false,
    'tex-extension' => 'im wrong',
];
try {
    $pdfCrop = new PDFCrop($options);
    $pdfCrop->setOptions($options);
} catch(Exception $e) {
    //The detailed error message will be present in the getMessage() method
    $e->getMessage();
}
```

`new saveAs($options)` and `toString()` will throw exceptions if an error presents itself in the command or the saving
    of the generated file if `ignoreWarnings` is set to `false`.

```php
$options = [
    'original' => '/path/to/original.pdf',
    'ignoreWarnings' => false,
];
$pdfCrop = new PDFCrop($options);
try {
    $pdfCrop->saveAs('/path/to/cropped.pdf');
    $croppedPdfContents = $pdfCrop->toString();
} catch(Exception $e) {
    //The detailed error message will be present in the getMessage() method
    $e->getMessage();
}
```

`new saveAs($options)` and `toString()` with the option `ignoreWarnings` set to `true` will prevent exceptions from
    showing, but if an error presents itself in the process or saving of the generated file, the method `getError()`
    will return a string with the detailed error message.

```php
$options = [
    'original' => '/path/to/original.pdf',
];
$pdfCrop = new PDFCrop($options);
$croppedPdfContents = $pdfCrop->toString();
if($pdfCrop->getError() != null) {
    //Handle error
}
$pdfCrop->saveAs('/path/to/cropped.pdf');
if($pdfCrop->getError() != null) {
    //Handle error
}
```

## Changelog
Check this library's changelog in [here](CHANGELOG.md).