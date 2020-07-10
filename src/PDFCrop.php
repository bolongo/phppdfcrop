<?php


namespace bolongo\phppdfcrop;


use Exception;
use mikehaertl\tmp\File;

class PDFCrop {

    /**
     * Path to the 'pdfcrop' binary.
     * @var string
     */
    public $binary = 'pdfcrop';
    /**
     * Options to pass to the Command constructor.
     * @var array
     */
    public $commandOptions = [];
    /**
     * Path to the directory that is going to be used to contain the temporary files.
     * @var string|null
     */
    public $tmpDir;
    /**
     * Path to the original pdf file to be cropped.
     * @var string
     */
    public $original;

    /**
     * Verbose parameter for its use when specifying options to this class. Makes the command do a verbose printing.
     */
    const PARAM_VERBOSE = 'verbose';
    /**
     * Debug parameter for its use when specifying options to this class. Makes the command print debug information.
     */
    const PARAM_DEBUG = 'debug';
    /**
     * Gscmd command parameter for its use when specifying options to this class. Specifies the path to the
     * ghostscript command to be used by the command.
     */
    const PARAM_GHOSTSCRIPT_COMMAND = 'gscmd';
    /**
     * Parameter for its use when specifying options to this class. Specifies the tex extension to be used by the
     * command.
     */
    const PARAM_TEX_EXTENSION = 'tex-extension';
    /**
     * Pdftexcmd command parameter for its use when specifying options to this class. Specifies the path to the
     * pdftex command to be used by the command.
     */
    const PARAM_PDFTEX_COMMAND = 'pdftexcmd';
    /**
     * Xetexcmd command parameter for its use when specifying options to this class. Specifies the path to the
     * xetex command to be used by the command.
     */
    const PARAM_XETEX_COMMAND = 'xetex';
    /**
     * Luatexcmd command parameter for its use when specifying options to this class. Specifies the path to the
     * luatex command to be used by the command.
     */
    const PARAM_LUATEX_COMMAND = 'luatex';
    /**
     * Margins command parameter for its use when specifying options to this class. Specifies extra margins to the
     * command, unit is bp. If only one number is given, then it is used for all margins, in the case of two numbers
     * they are used for right and bottom.
     */
    const PARAM_MARGINS = 'margins';
    /**
     * Clip command parameter for its use when specifying options to this class. Specifies clip support to the command
     * if margins are set.
     */
    const PARAM_CLIP = 'clip';
    /**
     * Hires command parameter for its use when specifying options to this class. Specifies the use of
     * '%%HiResBoundingBox' instead of '%%BoundingBox'.
     */
    const PARAM_HI_RES = 'hires';
    /**
     * Ini command parameter for its use when specifying options to this class. Specifies the use of iniTeX variant of
     * the TeX compiler to the command.
     */
    const PARAM_INI = 'ini';

    /**
     * Restricted command parameter for its use when specifying options to this class. Puts the command in restricted
     * mode.
     */
    const PARAM_RESTRICTED = 'restricted';
    /**
     * Papersize command parameter for its use when specifying options to this class. Parameter for gs's
     * -sPapersize=<value>, use only with older gs versions <7.32
     */
    const PARAM_PAPERSIZE = 'papersize';
    /**
     * Resolution command parameter for its use when specifying options to this class. Passes this argument to
     * ghostscript's option -r
     */
    const PARAM_RESOLUTION = 'resolution';
    /**
     * Bbox command parameter for its use when specifying options to this class. Overrides the bounding box found by
     * ghostscript with origin at the lower left corner.
     */
    const PARAM_BOUNDING_BOX = 'bbox';
    /**
     * Bbox-odd command parameter for its use when specifying options to this class. Same as Bbox but for odd pages.
     */
    const PARAM_BOUNDING_BOX_ODD = 'bbox-odd';
    /**
     * Bbox-even command parameter for its use when specifying options to this class. Same as Bbox but for even pages.
     */
    const PARAM_BOUNDING_BOX_EVEN = 'bbox-even';
    /**
     * Pdfversion command parameter for its use when specifying options to this class. Sets the pdf version. If 'auto'
     * is given as a value, then the PDF version is taken from the header of the input PDF file. An empty value or
     * 'none' uses the default of the TeX engine.
     */
    const PARAM_PDF_VERSION = 'pdfversion';

    /**
     * Original command parameter for its use when specifying options to this class. Path to the original pdf file to be
     * cropped.
     */
    const PARAM_ORIGINAL_FILE = 'original';
    /**
     * tmpDir command parameter for its use when specifying options to this class. Path to the directory that is going
     * to be used to contain the temporary files.
     */
    const PARAM_TEMPORARY_DIRECTORY = 'tmpDir';
    /**
     * Binary command parameter for its use when specifying options to this class. Path to the 'pdfcrop' binary.
     */
    const PARAM_BINARY = 'binary';
    /**
     * IgnoreWarnings command parameter for its use when specifying options to this class. Should the command prevent
     * throwing exceptions if a PDF file was created?.
     */
    const PARAM_IGNORE_WARNINGS = 'ignoreWarnings';
    /**
     * IgnoreOptionValidationErrors command parameter for its use when specifying options to this class. Prevents the
     * exceptions caused by the options that do not comply with validations done by the class.
     */
    const PARAM_IGNORE_OPTION_VALIDATION_ERRORS = 'ignoreOptionValidationErrors';

    /**
     * Default value for the 'pdfcrop' parameter
     */
    const BINARY_DEFAULT = 'pdfcrop';
    /**
     * Value for the 'verbose' parameter if is desired.
     */
    const VERBOSE = true;
    /**
     * Value for the 'verbose' parameter if is not desired.
     */
    const NO_VERBOSE = false;
    /**
     * Value for the 'debug' parameter if is desired.
     */
    const DEBUG = true;
    /**
     * Value for the 'debug' parameter if is not desired.
     */
    const NO_DEBUG = false;
    /**
     * Default value for the 'gscmd' parameter.
     */
    const GHOSTSCRIPT_COMMAND_DEFAULT = 'gs';
    /**
     * Value for the 'tex-extension' parameter if 'pdftex' is desired.
     */
    const TEX_EXTENSION_PDFTEX = 'pdftex';
    /**
     * Value for the 'tex-extension' parameter if 'xetex' is desired.
     */
    const TEX_EXTENSION_XETEX = 'xetex';
    /**
     * Value for the 'tex-extension' parameter if 'luatex' is desired.
     */
    const TEX_EXTENSION_LUATEX = 'luatex';
    /**
     * Default value for the 'pdftexcmd' parameter.
     */
    const PDFTEX_COMMAND_DEFAULT = 'pdftex';
    /**
     * Default value for the 'xetexcmd' parameter.
     */
    const XETEX_COMMAND_DEFAULT = 'xetex';
    /**
     * Default value for the 'luatexcmd' parameter.
     */
    const LUATEX_COMMAND_DEFAULT = 'luatex';
    /**
     * Default value for the 'margins' parameter.
     */
    const MARGINS_DEFAULT = [0, 0, 0, 0];
    /**
     * Value for the 'clip' parameter if clip support is desired.
     */
    const CLIP = true;
    /**
     * Value for the 'clip' parameter if no clip support is desired.
     */
    const NO_CLIP = false;
    /**
     * Value for the 'hires' parameter if the use of '%%HiResBoundingBox' is desired over '%%BoundingBox'.
     */
    const HI_RES = true;
    /**
     * Value for the 'hires' parameter if '%%BoundingBox' is desired over '%%HiResBoundingBox'.
     */
    const NO_HI_RES = false;
    /**
     * Value for the 'ini' parameter if the use of the iniTeX variant is desired.
     */
    const INI = true;
    /**
     * Value for the 'ini' parameter if the use of the iniTeX variant is not desired.
     */
    const NO_INI = false;
    /**
     * Value for the 'pdfversion' parameter. The command will use the default of the TeX engine.
     */
    const PDF_VERSION_NONE = 'none';
    /**
     * Value for the 'pdfversion' parameter. The command will use the version specified in the header of the input PDF
     * file.
     */
    const PDF_VERSION_AUTO = 'auto';
    /**
     * Value for the 'pdfversion' parameter.
     */
    const PDF_VERSION_1_1 = '1.1';
    /**
     * Value for the 'pdfversion' parameter.
     */
    const PDF_VERSION_1_2 = '1.2';
    /**
     * Value for the 'pdfversion' parameter.
     */
    const PDF_VERSION_1_3 = '1.3';
    /**
     * Value for the 'pdfversion' parameter.
     */
    const PDF_VERSION_1_4 = '1.4';
    /**
     * Value for the 'pdfversion' parameter.
     */
    const PDF_VERSION_1_5 = '1.5';
    /**
     * Value for the 'pdfversion' parameter.
     */
    const PDF_VERSION_1_6 = '1.6';
    /**
     * Value for the 'pdfversion' parameter.
     */
    const PDF_VERSION_1_7 = '1.7';
    /**
     * Value for the 'pdfversion' parameter.
     */
    const PDF_VERSION_1_8 = '1.8';

    /**
     * Value for the 'restricted' parameter. Sets the command in restricted mode.
     */
    const RESTRICTED = true;
    /**
     * Value for the 'pdfversion' parameter. Does not sets the command in restricted mode.
     */
    const NO_RESTRICTED = false;

    /**
     * Values for the 'tex-extension' parameter.
     */
    const TEX_EXTENSIONS = [
        self::TEX_EXTENSION_PDFTEX => self::TEX_EXTENSION_PDFTEX,
        self::TEX_EXTENSION_XETEX => self::TEX_EXTENSION_XETEX,
        self::TEX_EXTENSION_LUATEX => self::TEX_EXTENSION_LUATEX,
    ];

    /**
     * Temporary files prefix.
     */
    const TMP_PREFIX = 'tmp_pdfcrop_';

    /**
     * Associates this class' parameters with the actual pdfcrop command parameters.
     */
    const PARAM_CONVERSION = [
        self::PARAM_VERBOSE => 'verbose',
        self::PARAM_DEBUG => 'debug',
        self::PARAM_GHOSTSCRIPT_COMMAND => 'gscmd',
        self::PARAM_TEX_EXTENSION => '',
        self::PARAM_PDFTEX_COMMAND => 'pdftex',
        self::PARAM_XETEX_COMMAND => 'xetex',
        self::PARAM_LUATEX_COMMAND => 'luatex',
        self::PARAM_MARGINS => 'margins',
        self::PARAM_CLIP => 'clip',
        self::PARAM_HI_RES => 'hires',
        self::PARAM_INI => 'ini',

        self::PARAM_RESTRICTED => 'restricted',
        self::PARAM_PAPERSIZE => 'papersize',
        self::PARAM_RESOLUTION => 'resolution',
        self::PARAM_BOUNDING_BOX => 'bbox',
        self::PARAM_BOUNDING_BOX_ODD => 'bbox-odd',
        self::PARAM_BOUNDING_BOX_EVEN => 'bbox-even',
        self::PARAM_PDF_VERSION => 'pdfversion',
    ];

    /**
     * Association of this class' parameters with its respective default values.
     * @var array
     */
    protected $_defaults = [
        self::PARAM_BINARY => self::BINARY_DEFAULT,
        self::PARAM_IGNORE_WARNINGS => true,
        self::PARAM_ORIGINAL_FILE => null,
        self::PARAM_VERBOSE => self::NO_VERBOSE,
        self::PARAM_DEBUG => self::NO_DEBUG,
        self::PARAM_GHOSTSCRIPT_COMMAND => self::GHOSTSCRIPT_COMMAND_DEFAULT,
        self::PARAM_TEX_EXTENSION => self::TEX_EXTENSION_PDFTEX,
        self::PARAM_PDFTEX_COMMAND => self::PDFTEX_COMMAND_DEFAULT,
        self::PARAM_XETEX_COMMAND => self::XETEX_COMMAND_DEFAULT,
        self::PARAM_LUATEX_COMMAND => self::LUATEX_COMMAND_DEFAULT,
        self::PARAM_MARGINS => self::MARGINS_DEFAULT,
        self::PARAM_CLIP => self::NO_CLIP,
        self::PARAM_HI_RES => self::NO_HI_RES,
        self::PARAM_INI => self::NO_INI,

        self::PARAM_RESTRICTED => self::NO_RESTRICTED,
        self::PARAM_PAPERSIZE => null,
        self::PARAM_RESOLUTION => null,
        self::PARAM_BOUNDING_BOX => null,
        self::PARAM_BOUNDING_BOX_ODD => null,
        self::PARAM_BOUNDING_BOX_EVEN => null,
        self::PARAM_PDF_VERSION => null,
    ];

    /**
     * Current set options.
     * @var array
     */
    protected $_options = [];
    /**
     * State to keep track if the cropped PDF was already generated.
     * @var bool
     */
    protected $_isCreated = false;
    /**
     * Command that is generated using the options set on this class.
     * @var Command
     */
    protected $_command;
    /**
     * Temporary file where the command saves the cropped PDF.
     * @var File
     */
    protected $_tmpPdfFile;
    /**
     * Error descriptions will be kept in here.
     * @var string|null
     */
    protected $_error;
    /**
     * Ignore errors if a PDF file was created.
     * @var bool
     */
    public $ignoreWarnings = true;
    /**
     * Ignore options validation errors.
     * @var bool
     */
    public $ignoreOptionValidationErrors = true;

    /**
     * Gets the last error description
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * PDFCrop constructor. If an array is specified as the parameter, it will be interpreted as a set of options, if a
     * string is given, it will be interpreted as the path to the PDF to be cropped.
     * @param array|string|null $options
     * @throws Exception
     */
    public function __construct($options = null) {
//        $this->_options = $this->_defaults;
        if(is_array($options)) {
            $this->setOptions($options);
        } elseif(is_string($options)) {
            $this->setOptions([self::PARAM_ORIGINAL_FILE => $options]);
        } else {
            $this->setOptions($options);
        }
    }

    /**
     * Sets the options to be used by the pdfcrop command
     * The posible keys for the parameter $options are specified as constants prefixed by 'PARAM_' in this class
     * Some of the posible values for the keys in the parameter $options are specified as constants in this class,
     * these constants are by no means an extensive list of all the posible values and it's recommended that for a
     * better understanding of pdfcrop functionality one can start with the 'pdfcrop --help' command.
     * @param array $options
     * @throws Exception
     */
    public function setOptions($options = []) {

        $this->_options = [];

        if (isset($options[self::PARAM_ORIGINAL_FILE])) {
            $this->original = $options[self::PARAM_ORIGINAL_FILE];
            unset($options[self::PARAM_ORIGINAL_FILE]);
        }

        if (isset($options[self::PARAM_BINARY])) {
            $this->binary = $options[self::PARAM_BINARY];
            unset($options[self::PARAM_BINARY]);
        } else {
            $this->binary = $this->_defaults[self::PARAM_BINARY];
        }

        if (isset($options[self::PARAM_TEMPORARY_DIRECTORY])) {
            $this->tmpDir = $options[self::PARAM_TEMPORARY_DIRECTORY];
            unset($options[self::PARAM_TEMPORARY_DIRECTORY]);
        }

        if (isset($options[self::PARAM_IGNORE_WARNINGS])) {
            $this->ignoreWarnings = $options[self::PARAM_IGNORE_WARNINGS];
            unset($options[self::PARAM_IGNORE_WARNINGS]);
        }

        if (isset($options[self::PARAM_IGNORE_OPTION_VALIDATION_ERRORS])) {
            $this->ignoreOptionValidationErrors = $options[self::PARAM_IGNORE_OPTION_VALIDATION_ERRORS];
            unset($options[self::PARAM_IGNORE_OPTION_VALIDATION_ERRORS]);
        }

        foreach ($options as $parameter => $value) {
            switch ($parameter) {
                case self::PARAM_GHOSTSCRIPT_COMMAND:
                case self::PARAM_PDFTEX_COMMAND:
                case self::PARAM_XETEX_COMMAND:
                case self::PARAM_LUATEX_COMMAND:
                case self::PARAM_PAPERSIZE:
                case self::PARAM_PDF_VERSION:
                    if(!isset($value)) {
                        $value = $this->_defaults[$parameter];
                    }
                    if(!isset($value)) {
                        break;
                    }
                    if(!is_string($value)) {
                        if($this->ignoreOptionValidationErrors) {
                            break;
                        }
                        $this->throwOnlyStringException($parameter);
                    }
                    $this->_options[$parameter] = $value;
                    break;
                case self::PARAM_TEX_EXTENSION:
                    if(!isset($value)) {
                        $value = $this->_defaults[$parameter];
                    }
                    if(!isset($value)) {
                        break;
                    }
                    if(!is_string($value)) {
                        if($this->ignoreOptionValidationErrors) {
                            break;
                        }
                        $this->throwOnlyStringException($parameter);
                    }
                    if(!array_key_exists($value, self::TEX_EXTENSIONS)) {
                        if($this->ignoreOptionValidationErrors) {
                            break;
                        }
                        $this->throwOnlyValuesInArray($parameter, self::TEX_EXTENSIONS);
                    }
                    break;
                case self::PARAM_RESTRICTED:
                case self::PARAM_VERBOSE:
                case self::PARAM_DEBUG:
                case self::PARAM_CLIP:
                case self::PARAM_HI_RES:
                case self::PARAM_INI:
                    if(!isset($value)) {
                        $value = $this->_defaults[$parameter];
                    }
                    if(!isset($value)) {
                        break;
                    }
                    if(!is_bool($value)) {
                        $value = $value == true;
                    }
                    $this->_options[$parameter] = $value;
                    break;
                case self::PARAM_MARGINS:
                    if(!isset($value)) {
                        $value = $this->_defaults[$parameter];
                    }
                    if(!isset($value)) {
                        break;
                    }
                    if(is_string($value)) {
                        $value = explode(' ', $value);
                    }
                    if(!is_array($value) || (sizeof($value) == 0 && sizeof($value) > 4)) {
                        if($this->ignoreOptionValidationErrors) {
                            break;
                        }
                        $this->throwMarginsException($parameter);
                    }
                    if(sizeof($value) === 3) {
                        $value[] = 0;
                    }
                    foreach ($value as $item) {
                        if(!is_int($item)) {
                            if($this->ignoreOptionValidationErrors) {
                                break;
                            }
                            $this->throwMarginsException($parameter);
                        }
                    }
                    $this->_options[$parameter] = $value;
                    break;
                case self::PARAM_RESOLUTION:
                    if(!isset($value)) {
                        $value = $this->_defaults[$parameter];
                    }
                    if(!isset($value)) {
                        break;
                    }
                    if(is_int($value)) {
                        $this->_options[$parameter] = (string)$value;
                    }
                    if(preg_match('/^[0-9]*$/', $value)) {
                        $this->_options[$parameter] = $value;
                        break;
                    }
                    if(preg_match('/^[0-9]*x[0-9]*$/', $value)) {
                        $this->_options[$parameter] = $value;
                        break;
                    }
                    if($this->ignoreOptionValidationErrors) {
                        break;
                    }
                    $this->throwResolutionException($parameter);
                    break;
                case self::PARAM_BOUNDING_BOX:
                case self::PARAM_BOUNDING_BOX_ODD:
                case self::PARAM_BOUNDING_BOX_EVEN:
                    if(!isset($value)) {
                        $value = $this->_defaults[$parameter];
                    }
                    if(!isset($value)) {
                        break;
                    }
                    if(is_string($value)) {
                        $value = explode(' ', $value);
                    }
                    if(!is_array($value) || sizeof($value) !== 4) {
                        if($this->ignoreOptionValidationErrors) {
                            break;
                        }
                        $this->throwBoundingBoxException($parameter);
                    }
                    foreach ($value as $item) {
                        if(!is_int($item)) {
                            if($this->ignoreOptionValidationErrors) {
                                break;
                            }
                            $this->throwBoundingBoxException($parameter);
                        }
                    }
                    $this->_options[$parameter] = $value;
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Executes the command and saves the resulting cropped PDF file to the specified filename. Returns the filename of
     * the saved file on success.
     * @param $filename
     * @return string Filename of the created new cropped PDF file
     * @throws Exception
     */
    public function saveAs($filename) {
        $this->_error = null;
        if($this->_isCreated) {
            if(!$this->_tmpPdfFile->saveAs($filename)) {
                $this->_error = "Could not save Cropped PDF as '$filename'";
                $this->throwCouldNotCreatePDF($filename);
            }
            return $filename;
        }

        $this->createCropped();

        if(!$this->_tmpPdfFile->saveAs($filename)) {
            $this->throwCouldNotCreatePDF($filename);
        }
        return $filename;
    }

    /**
     * Executes the command and gets the contents of the resulting cropped PDF file as a string. Returns true if no
     * errors were present, false otherwise.
     * @return string
     * @throws Exception
     */
    public function toString() {
        $this->_error = null;
        if($this->_isCreated) {
            return file_get_contents($this->_tmpPdfFile->getFileName());
        }

        $this->createCropped();

        return file_get_contents($this->_tmpPdfFile->getFileName());
    }

    /**
     * Executes the command and gets the temp file reference.
     * @return bool
     * @throws Exception
     */
    protected function createCropped() {
        if($this->_isCreated) {
            return false;
        }

        $command = $this->getCommand();
        $filename = $this->getCroppedFilename();

        $command->addArgs($this->getCommandParameters($this->_options));

        $command->addArg($this->original, null, true);

        $command->addArg($filename, null, true);

        if(!$command->execute()) {
            $this->_error = $command->getError();
            if(!(file_exists($filename) && filesize($filename) !== 0) && !$this->ignoreWarnings) {
                throw new Exception($this->_error);
            }
        }
        $this->_isCreated = true;
        return true;
    }

    /**
     * Gets the temp file in which the instance will keep the cropped PDF file. If the file did not exist, it is generated
     * @return File
     */
    public function getCroppedFile() {
        if($this->_tmpPdfFile === NULL) {
            $this->_tmpPdfFile = new File('', '.pdf', self::TMP_PREFIX, $this->tmpDir);
        }
        return $this->_tmpPdfFile;
    }

    /**
     * Gets the filename of the temp file
     * @return string
     */
    public function getCroppedFilename() {
        return $this->getCroppedFile()->getFileName();
    }

    /**
     * Gets the command instance used by this class.
     * @return Command
     */
    public function getCommand() {
        if($this->_command === NULL) {
            $options = $this->commandOptions;
            if(!isset($options['command'])) {
                $options['command'] = $this->binary;
            }
            $this->_command = new Command($options);
        }
        return $this->_command;
    }

    /**
     * Creates an array with the actual parameter keys used by the command from an array with the parameter keys used by
     * this class.
     * @param array $options
     * @return array
     * @throws Exception
     */
    protected function getCommandParameters($options) {
        $commandParameters = [];

        if(!isset($this->original)) {
            $this->throwParameterNotSpecified(self::PARAM_ORIGINAL_FILE);
        }

        foreach ($options as $parameter => $value) {
            switch ($parameter) {
                case self::PARAM_GHOSTSCRIPT_COMMAND:
                case self::PARAM_PDFTEX_COMMAND:
                case self::PARAM_XETEX_COMMAND:
                case self::PARAM_LUATEX_COMMAND:
                    if(isset($value)) {
                        $commandParameters[self::PARAM_CONVERSION[$parameter]] = $value;
                    }
                    break;
                case self::PARAM_TEX_EXTENSION:
                    if(isset($value)) {
                        $commandParameters[self::PARAM_CONVERSION[$parameter]] = null;
                    }
                    break;
                case self::PARAM_VERBOSE:
                case self::PARAM_DEBUG:
                case self::PARAM_CLIP:
                case self::PARAM_HI_RES:
                case self::PARAM_INI:
                    if($value) {
                        $commandParameters[self::PARAM_CONVERSION[$parameter]] = null;
                    }
                    break;
                case self::PARAM_MARGINS:
                    if(isset($value)) {
                        $commandParameters[self::PARAM_CONVERSION[$parameter]] = "'" . implode(' ', $value) . "'";
                    }
                    break;
                default:
                    break;
            }
        }

        return $commandParameters;
    }

    /**
     * Throws an Exception related to the format of the margins parameter.
     * @param string $parameter
     * @throws Exception
     */
    protected function throwMarginsException($parameter) {
        throw new Exception($parameter . ' only accepts arrays with 1 to 4 integers or strings
            with 1 to 4 integers separated by spaces');
    }

    /**
     * Throws an Exception related to the format of the bbox, bbox-even or bbox-odd parameter.
     * @param string $parameter
     * @throws Exception
     */
    protected function throwBoundingBoxException($parameter) {
        throw new Exception($parameter . ' only accepts arrays with 4 integers or strings
            with 4 integers separated by spaces');
    }

    /**
     * Throws an Exception related to the format of the resolution parameter.
     * @param string $parameter
     * @throws Exception
     */
    protected function throwResolutionException($parameter) {
        throw new Exception($parameter . ' resolution must be specified as an integer or as 2 integer separated
            by a "x"');
    }

    /**
     * Throws an Exception if the parameter is not a string.
     * @param string $parameter
     * @throws Exception
     */
    protected function throwOnlyStringException($parameter) {
        throw new Exception($parameter . ' only accepts string values');
    }

    /**
     * Throws an Exception rif the parameter value is not part of the of the specified array.
     * @param string $parameter
     * @param array $array
     * @throws Exception
     */
    protected function throwOnlyValuesInArray($parameter, $array) {
        $values = '"' . implode('", ', $array);
        $values = rtrim(rtrim($values, ' '),',');
        throw new Exception($parameter . ' only accepts ' . $values . ' as values');
    }

    /**
     * Throws an Exception if the parameter is not specified.
     * @param string $parameter
     * @throws Exception
     */
    protected function throwParameterNotSpecified($parameter) {
        throw new Exception($parameter . ' was not specified');
    }

    /**
     * Throws an Exception if the cropped pdf file could not be saves in the specified filename.
     * @param string $parameter
     * @throws Exception
     */
    protected function throwCouldNotCreatePDF($parameter) {
        throw new Exception("Could not save Cropped PDF as $parameter");
    }
}