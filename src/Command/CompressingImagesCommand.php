<?php

namespace App\Command;

use App\Services\Tools;
use GdImage;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompressingImagesCommand extends Command
{

    protected static $defaultName = 'compress:images';


    public function __construct( Tools $tools)
    {
        $this->tools = $tools;
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->setDescription('Compression des images')
            ->setHelp('Compression des images du dossier public/upload')
        ;
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $count = 0;
        foreach($finder->files()->in('public/uploads') as $image)
        {
            dd($image);
            $gdImage = new GdImage($image->getPathName());
            imagejpeg($gdImage, $image->getPathName(), 100);
            // // dd($image);
            // $dir = $image->getPathName();
            // // dd($dir);
            // // $count ++;
            // $scaledImage =$this->scaleImageFileToBlob($image);
            // $this->tools->saveFile($scaledImage, $dir);
            dd('ok');
        }
        dd($count);
        return 1;
    }
    function scaleImageFileToBlob($file) {

        $source_pic = $file;
        $max_width = 200;
        $max_height = 200;
    
        [$width, $height, $image_type] = getimagesize($file);
    
        switch ($image_type)
        {
            case 1: $src = imagecreatefromgif($file); break;
            case 2: $src = imagecreatefromjpeg($file);  break;
            case 3: $src = imagecreatefrompng($file); break;
            default: return '';  break;
        }
    
        $x_ratio = $max_width / $width;
        $y_ratio = $max_height / $height;
    
        if( ($width <= $max_width) && ($height <= $max_height) ){
            $tn_width = $width;
            $tn_height = $height;
            }elseif (($x_ratio * $height) < $max_height){
                $tn_height = ceil($x_ratio * $height);
                $tn_width = $max_width;
            }else{
                $tn_width = ceil($y_ratio * $width);
                $tn_height = $max_height;
        }
    
        $tmp = imagecreatetruecolor($tn_width,$tn_height);
    
        /* Check if this image is PNG or GIF, then set if Transparent*/
        if(($image_type == 1) || ($image_type==3))
        {
            imagealphablending($tmp, false);
            imagesavealpha($tmp,true);
            $transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
            imagefilledrectangle($tmp, 0, 0, $tn_width, $tn_height, $transparent);
        }
        imagecopyresampled($tmp,$src,0,0,0,0,$tn_width, $tn_height,$width,$height);
    
        /*
         * imageXXX() only has two options, save as a file, or send to the browser.
         * It does not provide you the oppurtunity to manipulate the final GIF/JPG/PNG file stream
         * So I start the output buffering, use imageXXX() to output the data stream to the browser,
         * get the contents of the stream, and use clean to silently discard the buffered contents.
         */
        ob_start();
    
        switch ($image_type)
        {
            case 1: imagegif($tmp); break;
            case 2: imagejpeg($tmp, NULL, 100);  break; // best quality
            case 3: imagepng($tmp, NULL, 50); break; // no compression
            default: echo ''; break;
        }
        $final_image = ob_get_contents();
    
        ob_end_clean();
        return $final_image;    
    }

}