<?php


class Upload
{

    public static function uploadPost(array $files) : bool {
        $file = $files['picture']['tmp_name'];
        $sourceProperties = getimagesize($file);
        $fileNewName = explode(".", $files['picture']['name'])[0];
        $folderPathDash =  'pictures/dashboard/';
        $folderPathThumb = 'pictures/thumbnail/';
        $folderPathFull = 'pictures/full/';
        $fullpathDash = getcwd() . "/" . $folderPathDash;
        $fullpathThumb = getcwd() . "/".$folderPathThumb;
        $fullpath = getcwd() . "/" . $folderPathFull;
        $ext = pathinfo($files['picture']['name'], PATHINFO_EXTENSION);
        $imageType = $sourceProperties[2];


        switch ($imageType) {


            case IMAGETYPE_PNG:
                $imageResourceId = imagecreatefrompng($file);
                $targetLayer = self::imageResizeThump($imageResourceId, $sourceProperties[0], $sourceProperties[1]);
                imagepng($targetLayer, $fullpathThumb . $fileNewName . ".". $ext);
                $imageResourceId1 = imagecreatefrompng($file);
                $targetLayer1 = self::imageResizeDash($imageResourceId1, $sourceProperties[0], $sourceProperties[1]);
                imagepng($targetLayer1, $fullpathDash . $fileNewName . ".". $ext);

                break;


            case IMAGETYPE_JPEG:
                $imageResourceId = imagecreatefromjpeg($file);
                $targetLayer = self::imageResizeThump($imageResourceId, $sourceProperties[0], $sourceProperties[1]);
                imagejpeg($targetLayer, $fullpathThumb . $fileNewName .".". $ext);
                $imageResourceId1 = imagecreatefromjpeg($file);
                $targetLayer1 =self:: imageResizeDash($imageResourceId1, $sourceProperties[0], $sourceProperties[1]);
                imagejpeg($targetLayer1, $fullpathDash . $fileNewName .".".  $ext);

                break;


            default:
                echo "Invalid Image type.";
                return false;
        }


        move_uploaded_file($file, $fullpath . $fileNewName . "." . $ext);
        return true;
    }
    private static function imageResizeThump($imageResourceId, $width, $height)
{


    $targetWidth = 500;
    $targetHeight = 250;


    $targetLayer = imagecreatetruecolor($targetWidth, $targetHeight);
    imagecopyresampled($targetLayer, $imageResourceId, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);


    return $targetLayer;

}

private static function imageResizeDash($imageResourceId, $width, $height)
{



    if($height>(2*$width)){
        $targetWidth = 750;
        $targetHeight = 1500;
    }else{
        $targetWidth = 1500;
        $targetHeight = 750;
    }


    $targetLayer = imagecreatetruecolor($targetWidth, $targetHeight);
    imagecopyresampled($targetLayer, $imageResourceId, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);


    return $targetLayer;

}

}