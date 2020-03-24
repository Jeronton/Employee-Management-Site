<!-- 
    Utility functions to be used throughout the project.
    Author: Jeremy Grift
    Created: March 23, 2020
    Last Updated: March 23, 2020
 -->
 <?php 
    /*
    * Validates that the image is indeed an image.
    * 
    * temporarypath ~ the temporary path of the image, used to get meme type.
    * newpath ~ the path the image is to be uploaded to, used to get extension.
    * Returns: True if file is an image, false otherwise.
    */
    function validateImage($temporarypath, $newpath){
        $valid_mimes = ['image/gif', 'image/jpeg', 'image/png'];
        $valid_extensions = ['gif', 'jpg', 'jpeg', 'png'];

        $mime = mime_content_type($temporarypath);
        $extension = pathinfo($newpath, PATHINFO_EXTENSION);

        return in_array( strtolower($mime), $valid_mimes) && in_array(strtolower($extension), $valid_extensions);
    }

    /*
    * Builds the upload path for the an file.
    *
    * filename: the name and extension of the file.
    * folder: The folder to put file into, default 'uploads'.
    * Returns: The upload path.
    */
    function buildUploadPath($filename, $folder = 'uploads'){
        $pathsegments = [dirname(__FILE__), $folder, $filename];
        return join(DIRECTORY_SEPARATOR, $pathsegments);
    }
 ?>