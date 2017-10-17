<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Mynamespace\SimpleImage;

class ResizeImage extends AbstractHelper
{
    public function __invoke($dir, $filename)
    {
        $image_src = $dir.DIRECTORY_SEPARATOR.$filename;
        $image = new SimpleImage();
        $image->load($image_src);                     
        $image_height = $image->get_height();
        $image_width = $image->get_width();
        
        // 512px
        if($image_height > $image_width) {
            if($image_height > 512) {
                $image->fit_to_height(512);
                $image->save($image_src);
            }
        } elseif($image_height < $image_width) {
            if($image_width > 512) {
                $image->fit_to_width(512);
                $image->save($image_src);
            }
        } else {
            if($image_height > 512) {
                $image->resize(512, 512);
                $image->save($image_src);
            }
        }
        
        // 256px
        if($image_height > $image_width) {
            if($image_height > 256) {
                $image->fit_to_height(256);
                $image->save($dir.DIRECTORY_SEPARATOR.'l_'.$filename);
            }
        } elseif($image_height < $image_width) {
            if($image_width > 256) {
                $image->fit_to_width(256);
                $image->save($dir.DIRECTORY_SEPARATOR.'l_'.$filename);
            }
        } else {
            if($image_height > 256) {
                $image->resize(256, 256);
                $image->save($dir.DIRECTORY_SEPARATOR.'l_'.$filename);
            }
        }
        
        // 128px
        if($image_height > $image_width) {
            if($image_height > 128) {
                $image->fit_to_height(128);
                $image->save($dir.DIRECTORY_SEPARATOR.'m_'.$filename);
            }
        } elseif($image_height < $image_width) {
            if($image_width > 128) {
                $image->fit_to_width(128);
                $image->save($dir.DIRECTORY_SEPARATOR.'m_'.$filename);
            }
        } else {
            if($image_height > 128) {
                $image->resize(128, 128);
                $image->save($dir.DIRECTORY_SEPARATOR.'m_'.$filename);
            }
        }
        
        // 64px
        if($image_height > $image_width) {
            if($image_height > 64) {
                $image->fit_to_height(64);
                $image->save($dir.DIRECTORY_SEPARATOR.'xs_'.$filename);
            }
        } elseif($image_height < $image_width) {
            if($image_width > 64) {
                $image->fit_to_width(64);
                $image->save($dir.DIRECTORY_SEPARATOR.'xs_'.$filename);
            }
        } else {
            if($image_height > 64) {
                $image->resize(64, 64);
                $image->save($dir.DIRECTORY_SEPARATOR.'xs_'.$filename);
            }
        }
        
        // 32px
        if($image_height > $image_width) {
            if($image_height > 32) {
                $image->fit_to_height(32);
                $image->save($dir.DIRECTORY_SEPARATOR.'s_'.$filename);
            }
        } elseif($image_height < $image_width) {
            if($image_width > 32) {
                $image->fit_to_width(32);
                $image->save($dir.DIRECTORY_SEPARATOR.'s_'.$filename);
            }
        } else {
            if($image_height > 32) {
                $image->resize(32, 32);
                $image->save($dir.DIRECTORY_SEPARATOR.'s_'.$filename);
            }
        }
    }
}
