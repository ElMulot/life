<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Blog\Service;

/**
 * @package		Blog\Service
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class ImageManager 
{       

    private $config;
    
    public function __construct ($config)
    {
    	$this->config = $config;
    }
    
    public function getSaveToDir()
    {
    	return $this->config['application_settings']['image_dir'];
    }

    public function getImageMaxWidth()
    {
    	return $this->config['application_settings']['image_max_width'];
    }
    
    public function getThumbnailMaxWidth()
    {
    	return $this->config['application_settings']['thumbnail_max_width'];
    }

    /**
     * Returns the path to the saved image file
     * @param string $fileName Image file name (without path part)
     * @return string Path to image file
     */
    public function getImagePath($fileName) 
    {
        // Take some precautions to make file name secure
        str_replace("/", "", $fileName);  // Remove slashes
        str_replace("\\", "", $fileName); // Remove back-slashes
                
        // Return concatenated directory name and file name.
        return $this->getSaveToDir() . $fileName;
    }
    
    /**
     * Returns the path to no_image.jpg
     * @return string Path to image file
     */
    public function getNoImagePath()
    {
    	return $this->config['application_settings']['no_image_path'];
    }
    
    /**
     * Retrieves the file information (size, MIME type) by image path
     * @param string $filePath Path to the image file
     * @return array File information
     */
    public function getImageFileInfo($filePath) 
    {
        // Try to open file        
        if (!is_readable($filePath)) {            
            return false;
        }
                
        // Get file size in bytes.
        $fileSize = filesize($filePath);

        // Get MIME type of the file.
        $finfo = finfo_open(FILEINFO_MIME);
        $mimeType = finfo_file($finfo, $filePath);
        if($mimeType===false)
            $mimeType = 'application/octet-stream';
        
        return [
            'size' => $fileSize,
            'type' => $mimeType 
        ];
    }
    
    /**
     * Returns the image file content. On error, returns boolean false
     * @param string $filePath Path to image file
     * @return string|false
     */
    public function getImageFileContent($filePath) 
    {
        return file_get_contents($filePath);
    }

    /**
     * Returns the resized image
     * @param string $filePath Path to image file
     * @return string Resulting file name
     */ 
    public function getImage($filePath) {
    	return $this->resizeImage($filePath, $this->getImageMaxWidth());
    }
    
    /**
     * Returns the resized image as thumbnail
     * @param string $filePath Path to image file
     * @return string Resulting file name
     */
    public function getThumbnail($filePath) {
    	return $this->resizeImage($filePath, $this->getThumbnailMaxWidth());
    }
    

    /**
     * Resizes the image, keeping its aspect ratio
     * @param string $filePath Path to image file
     * @param int $desiredWidth Desired width
     * @return string Resulting file name
     */
    public  function resizeImage($filePath, $desiredWidth) 
    {
        // Get original image dimensions.
        list($originalWidth, $originalHeight) = getimagesize($filePath);

        // Calculate aspect ratio
        $aspectRatio = $originalWidth/$originalHeight;
        // Calculate the resulting height
        $desiredHeight = $desiredWidth/$aspectRatio;

        // Resize the image
        $resultingImage = imagecreatetruecolor($desiredWidth, $desiredHeight);
        $originalImage = imagecreatefromjpeg($filePath);
        imagecopyresampled($resultingImage, $originalImage, 0, 0, 0, 0, 
        		$desiredWidth, $desiredHeight, $originalWidth, $originalHeight);

        // Save the resized image to temporary location
        $tmpFileName = tempnam("/tmp", "img");
        imagejpeg($resultingImage, $tmpFileName, 80);
        
        // Return the path to resulting image.
        return $tmpFileName;
    }
}



