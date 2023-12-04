<?php
namespace ETL\Services;

use ETL\Services\LayoutConfigService;
use ETL\Traits\HelperTrait as Helper;

class ZipFileService extends LayoutConfigService {
  private $zip;
  private $zipFilePath;
  private $filesToZipPath;
  private $zipFileName;

  use Helper;

  public function __construct(array $sourceData, string $layout){
    parent::__construct();

    $layoutConfig = parent::get($layout);

    $this->zipFileName = $this->_hasWildCard($layoutConfig['outFileNameZip'] ?? $layout.date('dmYHis').'.zip', $sourceData[0]);
    $this->filesToZipPath = $this->_hasWildCard($layoutConfig['outputPath'], $sourceData[0]);
    $this->zipFilePath = $this->_hasWildCard($layoutConfig['outputZipPath'], $sourceData[0]);

    $this->zip = new \ZipArchive();
  }

  private function createZip(): object{
    if (!file_exists($this->zipFilePath)) {
      mkdir($this->zipFilePath, 0777, true);
    }

    if ($this->zip->open($this->zipFilePath.'/'.$this->zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
      throw new \Exception('ERROR - Unable to open ZIP file: '.$this->zipFilePath . '/' . $this->zipFileName);
    }
    return $this;
  }

  private function addFiles(): object{
    $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->filesToZipPath), \RecursiveIteratorIterator::LEAVES_ONLY);

    foreach ($files as $file) {
      if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $this->zip->addFile($filePath, basename($filePath));
      }
    }

    return $this;
  }

  private function closeZip(): void{
    $this->zip->close();
  }

  public function run(): void{
    $this
      ->createZip()
      ->addFiles()
      ->closeZip();
  }
}
