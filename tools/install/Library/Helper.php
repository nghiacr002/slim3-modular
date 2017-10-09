<?php
namespace Lego\Installation\Library;
use Lego\IO\FileManager;
class Helper
{
    protected $pathScript; 
    protected $pathSQL;
    public function __construct($pathScript = "", $pathSQL = "")
    {
        $this->pathScript = APP_PATH_INSTALL . 'Script'. DS; 
        $this->pathSQL = APP_PATH_INSTALL . 'Sql'. DS;
    }
    /**
     * get All Script Migration Files
     */
    public function getAllScripts()
    {
        return $this->getFilesInFolder($this->pathSQL, '.php');
    }
    /**
     * get All Script SQL Migration Files
     */
    public function getAllSQLFiles()
    {
        return $this->getFilesInFolder($this->pathSQL, '.sql');
    }
    /**
     * get all files in folder by file extension
     * @param string $path
     * @return array list of files
     */
    protected function getFilesInFolder($path = "", $fileExt = ".php")
    {
        $fileManager = new FileManager(); 
        $data = $fileManager->scanDir($path, $fileExt);
    }
    public function getScriptNameFromVersion($version)
    {
        $fileScript = $this->pathScript . $this->getClassNameFromVersion($version) . '.php';
        return $fileScript;
    }
    public function getClassNameFromVersion($version, $withNamespace = false)
    {
        $name = "Runner". str_replace('.', '', $version); 
        if($withNamespace)
        {
            $name = 'Lego\\Installation\\Script\\' . $name;
        }
        return $name;
    }
}