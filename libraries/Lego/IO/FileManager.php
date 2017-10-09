<?php

namespace Lego\IO;

use ZipArchive;
/**
 * 
 * @author nghia
 * Working with File & Folders
 */

class FileManager {
    /**
     * Remove File
     * @param string $sFile
     * @return boolean
     */
	public function removeFile($sFile) 
	{
		if(file_exists($sFile)){
			@unlink ( $sFile );
			return true;
		}
		return false;
	}
	/**
	 * Remove all child files & folders in folder
	 * @param string $sSourcePath
	 * @param string $bForceRemoveParent
	 * @return boolean
	 */
	public function recurse_remove($sSourcePath, $bForceRemoveParent = true) 
	{
		$dir = opendir ( $sSourcePath );
		while ( false !== ($file = readdir ( $dir )) ) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir ( $sSourcePath . '/' . $file )) {
					$this->recurse_remove ( $sSourcePath . '/' . $file );
				} else {
					@unlink ( $sSourcePath . '/' . $file );
				}
			}
		}
		closedir ( $dir );
		if($bForceRemoveParent){
			@rmdir ( $sSourcePath );
		}

		return true;
	}
	/**
	 * Copy folders in recurse mode
	 * @param string $sSourcePath
	 * @param string $sDestinationPath
	 * @param true|false $bMoving
	 */
	public function recurse_copy($sSourcePath, $sDestinationPath , $bMoving = false) {
		$sBaseSource = $sSourcePath;
		if (! is_dir ( $sDestinationPath )) {
			@mkdir ( $sDestinationPath );
		}

		$dir = opendir ( $sSourcePath );

		while ( false !== ($file = readdir ( $dir )) ) {

			if (($file != '.') && ($file != '..')) {

				if (is_dir ( $sSourcePath . '/' . $file )) {

				    $this->recurse_copy ( $sSourcePath . '/' . $file, $sDestinationPath . '/' . $file, $bMoving );
				} else {

					copy ( $sSourcePath . '/' . $file, $sDestinationPath . '/' . $file );
					if ($bMoving) {
						@unlink ( $sSourcePath . '/' . $file );
					}
				}
			}
		}
		closedir ( $dir );
		if ($bMoving) {

			@rmdir ( $sBaseSource );
		}
	}
	/**
	 * Uncompress ZIP file
	 * @param string $sSourcePath
	 * @param string $sDestinationPath
	 * @return boolean
	 */
	public function unzip($sSourcePath, $sDestinationPath) {
	    if(!class_exists('ZipArchive'))
	    {
	        return false;
	    }
		$zip = new ZipArchive ();
		$res = $zip->open ( $sSourcePath );
		if ($res == true) {
			$zip->extractTo ( $sDestinationPath );
			$zip->close ();
			return true;
		}
		return false;
	}
	/**
	 * Compress Folders or Files to Zip File
	 * @param string $aSources
	 * @param string $sDestinationPath
	 * @param true|false $bIncludeDir
	 * @return boolean
	 */
	public function zip($aSources, $sDestinationPath, $bIncludeDir = false) {
		if (! extension_loaded ( 'zip' )) {
			return false;
		}
		if (file_exists ( $sDestinationPath )) {
			@unlink ( $sDestinationPath );
		}
		$fp = fopen ( $sDestinationPath, 'w' );
		if ($fp === FALSE) {
			return false;
		}
		fclose ( $fp );
		$zip = new ZipArchive ();
		if (! $zip->open ( $sDestinationPath, ZIPARCHIVE::CREATE )) {
			return false;
		}
		foreach ( $aSources as $source ) {
			$source = str_replace ( '\\', '/', realpath ( $source ) );
			if (is_dir ( $source ) === true) {
				$files = new \RecursiveIteratorIterator ( new \RecursiveDirectoryIterator ( $source ), \RecursiveIteratorIterator::SELF_FIRST );
				if ($bIncludeDir) {
					$arr = explode ( "/", $source );
					$maindir = $arr [count ( $arr ) - 1];
					$source = "";
					for($i = 0; $i < count ( $arr ) - 1; $i ++) {
						$source .= '/' . $arr [$i];
					}
					$source = substr ( $source, 1 );
					$zip->addEmptyDir ( $maindir );
				}
				foreach ( $files as $file ) {
					$file = str_replace ( '\\', '/', $file );
					if (in_array ( substr ( $file, strrpos ( $file, '/' ) + 1 ), array (
							'.',
							'..'
					) ))
						continue;
					$file = realpath ( $file );
					if (is_dir ( $file ) === true) {
						$zip->addEmptyDir ( str_replace ( $source . '/', '', $file . '/' ) );
					} else if (is_file ( $file ) === true) {
						$zip->addFromString ( str_replace ( $source . '/', '', $file ), file_get_contents ( $file ) );
					}
				}
			} else if (is_file ( $source ) === true) {
				$zip->addFromString ( basename ( $source ), file_get_contents ( $source ) );
			}
		}
		return $zip->close ();
	}
	/**
	 * Scan first level of directory to get files & folders. 
	 * @param string $sPath
	 * @param string $bUpdatePath
	 * @return array
	 */
	public function scanDir($sPath = null, $fileExt = "*") {
		if (strrpos ( $sPath, DIRECTORY_SEPARATOR ) != strlen ( $sPath ) - 1) {
		    $sPath = $sPath . DIRECTORY_SEPARATOR;
		}
		$mData = @scandir ( $sPath, SCANDIR_SORT_ASCENDING );

		//d($mData);
		$aResult = array (
				'folder' => array (),
				'file' => array ()
		);
		if (count ( $mData )) {
			foreach ( $mData as $key => $hFile ) {
				if ($hFile == ".") {
					continue;
				}
				$aFile = array (
						'title' => $hFile,
						'path' => $sPath,
						'type' => 'folder',
						'time' => 'N/A',
						'size' => 'N/A',
						'perm' => '',
						'full_path' => $sPath,
						'file_size_view' => 'N/A',
				);
				switch ($hFile) {
					case "." :
						break;

					case ".." :
						$aFile ['path'] = dirname ( $aFile ['path'] ) . APP_DS;
						$aFile ['full_path'] = $aFile ['path'];
						break;

					default :
					    if($fileExt != "*")
					    {
					        if(!is_array($fileExt))
					        {
					            $fileExt = array($fileExt); 
					        }
					        $ext = $this->getExt($hFile); 
					        if(!in_array($ext, $fileExt))
					        {
					            continue;
					        }
					    }
						$sFullPath = $sPath . $hFile;
						if (is_dir ( $sFullPath )) {
							$aFile ['type'] = "folder";
						} else {
							$aFile ['type'] = "file";
							$aFile ['time'] = filectime ( $sFullPath );
							$aFile ['size'] = @filesize ( $sFullPath );
							$aFile ['file_size_view'] = $this->convertFileSize ( $aFile ['size'] );
						}
						$aFile ['full_path'] = $sFullPath;
						break;
				}
				$aFile ['perm'] = $this->getPermision ( $aFile ['full_path'] );
				$aResult [$aFile ['type']] [] = $aFile;
			}
		}
		return array_merge ( $aResult ['folder'], $aResult ['file'] );
	}
	/**
	 * Get File|Folder Permission
	 * @param string $mPath
	 * @return string
	 */
	public function getPermision($mPath) {
		return substr ( sprintf ( '%o', fileperms ( $mPath ) ), - 4 );
	}
	/**
	 * Convert file size from bytes to larger
	 * @param string $bytes
	 * @param string $sType
	 * @return string
	 */
	public function convertFileSize($bytes, $sType = "") {
		$label = array (
				'B',
				'KB',
				'MB',
				'GB',
				'TB',
				'PB'
		);
		for($i = 0; $bytes >= 1024 && $i < (count ( $label ) - 1); $bytes /= 1024, $i ++)
			;
		return (round ( $bytes, 2 ) . " " . $label [$i]);
	}
	/**
	 * Get Max file size upload in bytes
	 * @param string $bByteReturn
	 * @return number|mixed
	 */
	public function getMaximumFileUploadSize($bByteReturn = false) {
		$mValue = min ( ini_get ( 'post_max_size' ), ini_get ( 'post_max_size' ) );
		if ($bByteReturn) {
			$mValue = (( int ) $mValue) * 1024;
		}
		return $mValue;
	}
	/**
	 * get file extension
	 * @param string $file
	 */
	public function getExt($file)
	{
	    $aParts = explode('.',$file);
	    if(count($aParts) > 1)
	    {
	        return $aParts[count($aParts)];
	    }
	    return "";
	}
	
	
}

?>