<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Folder extends AbstractHelper
{
    public function __invoke($action, $directory)
    {
        if($action === 'delete') {
            foreach(glob("{$directory}/*") as $file)
            {
                if(is_dir($file)) { 
                    $this->deleteFolder($file);
                } else {
                    unlink($file);
                }
            }
            if($this->existFolder($directory)) rmdir($directory);
        }
    }
    
    public function existFolder($folder)
    {
        // Get canonicalized absolute pathname
        $path = realpath($folder);
    
        // If it exist, check if it's a directory
        if($path !== false AND is_dir($path))
        {
            // Return canonicalized absolute pathname
            return $path;
        }
    
        // Path/folder does not exist
        return false;
    }
    
    function deleteFolder($directory)
    {
        foreach(glob("{$directory}/*") as $file)
        {
            if(is_dir($file)) { 
                $this->deleteFolder($file);
            } else {
                unlink($file);
            }
        }
        if($this->existFolder($directory)) rmdir($directory);
    }        
}
