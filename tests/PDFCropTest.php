<?php


namespace tests;

use PHPUnit\Framework\TestCase;
use bolongo\phppdfcrop\PDFCrop;

class PDFCropTest extends TestCase {
    public function testCanCreateCroppedPdfFromPdf() {
        $inFile = $this->getPdfAsset();
        $outFile = $this->getOutPdf();
        $binary = $this->getBinary();

        $pdfCrop = new PDFCrop($inFile);
        $pdfCrop->binary = $binary;
        $pdfCrop->saveAs($outFile);
        $this->assertNull($pdfCrop->getError());
        $this->assertFileExists($outFile);

        $tempFile = $pdfCrop->getCroppedFilename();

        $this->assertEquals("$binary '$inFile' '$tempFile'", (string) $pdfCrop->getCommand());
        unlink($outFile);
    }

    public function testCanCroppedPdfStringFromPdf() {
        $inFile = $this->getPdfAsset();
        $binary = $this->getBinary();

        $pdfCrop = new PDFCrop($inFile);
        $pdfCrop->binary = $binary;
        $croppedPDFContents = $pdfCrop->toString();
        $this->assertNotEmpty($croppedPDFContents);
        $this->assertNull($pdfCrop->getError());

        $tempFile = $pdfCrop->getCroppedFilename();

        $this->assertEquals("$binary '$inFile' '$tempFile'", (string) $pdfCrop->getCommand());
    }

    protected function getBinary() {
        return '/usr/bin/pdfcrop';
    }

    protected function getPdfAsset() {
        return __DIR__ . '/assets/test.pdf';
    }

    protected function getOutPdf() {
        return __DIR__ . '/test.pdf';
    }
}