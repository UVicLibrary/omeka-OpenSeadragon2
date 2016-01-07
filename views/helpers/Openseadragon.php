<?php
/**
 * OpenSeadragon2
 * 
 * @copyright Copyright 2015 University of Victoria
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package OpenSeadragon2\View\Helper
 */
class OpenSeadragon2_View_Helper_Openseadragon extends Zend_View_Helper_Abstract
{
    protected $_supportedExtensions = array('bmp', 'gif', 'ico', 'jpeg', 'jpg', 
                                            'png', 'tiff', 'tif');

    /**
     * Return a OpenSeadragon image viewer for the provided files.
     * 
     * @param File|array $files A File record or an array of File records.
     * @param int $width The width of the image viewer in pixels.
     * @param int $height The height of the image viewer in pixels.
     * @return string|null
     */
    public function openseadragon($files)
    {
        if (!is_array($files)) {
            $files = array($files);
        }

        // Filter out invalid images.
        $images = array();
        $imageNames = array();
        foreach ($files as $file) {
            // A valid image must be a File record.
            if (!($file instanceof File)) {
                continue;
            }
            // A valid image must have a supported extension.
            $extension = pathinfo($file->original_filename, PATHINFO_EXTENSION);
            if (!in_array(strtolower($extension), $this->_supportedExtensions)) {
                continue;
            }
            $images[] = $file;
            $imageNames[explode(".",$file->filename)[0]] =  openseadragon_dimensions($file, 'original');
        }

        // Return if there are no valid images.
        if (!$images) {
            return;
        }

        return $this->view->partial('common/openseadragon.php', array(
            'images' => $images,
            'imageNames' => $imageNames
        ));
    }
}
