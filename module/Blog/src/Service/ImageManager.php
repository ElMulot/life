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
    
    /**
     * @return string Path to save directory (configured in application_settings/image_dir)
     */
    public function getSaveToDir()
    {
    	return $this->config['application_settings']['image_dir'];
    }

    /**
     * @return string Unique jpg image name
     */
    public function getImageName()
    {
    	return uniqid('img') .'.jpg';
    }
    
    /**
     * @param string $imageName
     * @return string Concatenated directory name and image name
     */
    public function getImagePath($imageName) 
    {
    	return $this->getSaveToDir() . $imageName;
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
     * Get images properties and convert it into image file through image manager
     * @param string $content
     * @return string
     */
    public function storeImages($content)
    {
    	//while (preg_match('#src="data:image/(jpeg|png);base64,([^"]*)"\salt="([^"]*)"\swidth="([^"]*)"\sheight="([^"]*)#i', $content, $array))
    	while (preg_match('#src="data:image/(jpeg|png|gif);base64,[^\>]*#i', $content, $array))
    	{
    		$imgTag = $array[0];
    		preg_match('#src="data:image/(jpeg|png|gif);base64,([^"]*)#i', $imgTag, $array);
    		$blob = $array[2];
    		preg_match('#alt="([^"]*)#i', $imgTag, $array);
    		$alt = $array[1];
    		preg_match('#width="([^"]*)#i', $imgTag, $array);
    		$width = $array[1];
    		preg_match('#height="([^"]*)#i', $imgTag, $array);
    		$height = $array[1];
    		 
    		$imageName = $this->getImageName();
    		$this->setImageFileContent($imageName, $blob, $width, $height);
    		$content = str_replace($imgTag, 'src="/posts/file?name=' . $imageName . '" alt="' . $alt . '" width="' . $width . '" height="' . $height . '"', $content);
    	}
    	return $content;
    }
    
    /**
     * Save the image file content to a file image
     * @param string $imageName
     * @param string $blob
     * @param int $desiredWidth
     * @param int $desiredHeight
     * @return bool
     */
    public function setImageFileContent($imageName, $blob, $desiredWidth, $desiredHeight)
    {
    	$originalImage = imagecreatefromstring(base64_decode($blob));
    	$originalWidth = imagesx($originalImage);
    	$originalHeight = imagesy($originalImage);
    	
    	$resultingImage = imagecreatetruecolor($desiredWidth, $desiredHeight);
    	imagecopyresampled($resultingImage, $originalImage, 0, 0, 0, 0, $desiredWidth, $desiredHeight, $originalWidth, $originalHeight);
    	return imagejpeg($resultingImage, $this->getImagePath($imageName));
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
}



