<?php

require("imagine/image/imagineinterface.php");
require("imagine/image/manipulatorinterface.php");
require("imagine/image/imageinterface.php");
require("imagine/image/boxinterface.php");
require("imagine/image/box.php");
require("imagine/gd/imagine.php");
require("imagine/gd/image.php");
require("imagine/image/pointinterface.php");
require("imagine/image/point.php");

class Thumbnail
{
    protected $_filename;
    
    public function __construct($options = array())
    {
        if (isset($options["file"]))
        {
            $file = $options["file"];
            $path = dirname(BASEPATH)."/uploads";
            
            $width = 64;
            $height = 64;
            
            $name = $file->name;
            $filename = pathinfo($name, PATHINFO_FILENAME);
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            
            if ($filename && $extension)
            {
                $thumbnail = "{$filename}-{$width}x{$height}.{$extension}";
                
                if (!file_exists("{$path}/{$thumbnail}"))
                {
                    $imagine = new Imagine\Gd\Imagine();
                    
                    $size = new Imagine\Image\Box($width, $height);
                    $mode = Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
                    
                    $imagine
                        ->open("{$path}/{$name}")
                        ->thumbnail($size, $mode)
                        ->save("{$path}/{$thumbnail}");
                }
                
                $this->_filename = $thumbnail;
            }
        }
    }
    
    public function getFilename()
    {
        return $this->_filename;
    }
}