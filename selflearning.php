<?php
    $filename = "images/cat.jpg";
    $image = imagecreatefromjpeg($filename);
    $imgResized = imagescale($image , 200, 300);
    imagejpeg($imgResized, 'images/catresized.jpg');

    // Temp name is where the file is saved temporarily on the computer as we haven't uploaded it yet
    if (isset($_POST['submit'])){
        $file = $_FILES['file'];
        print_r($_FILES);
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];

        $fileExts = explode('.', $fileName);
        $fileExt = strtolower(end($fileExts));

        $allowedExt = array('jpg', 'jpeg', 'png');

        if (in_array($fileExt, $allowedExt)) {
            if ($fileError === 0) {
                if ($fileSize < 5000000){
                    // uses time in miliseconds to create a unique number
                    $fileNameNew = uniqid('', true).".".$fileExt;
                    $fileDestination = 'images/' . $fileNameNew;
                    move_uploaded_file($fileTmpName, $fileDestination);
                } else {
                    echo "your file is too big";
                }
            } else {
                echo "there was an error uploading your file";
            }

        } else {
            echo "you cannot files of this type";
        }

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <img src="">
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file">
        <button type="submit" name="submit"> SUBMIT</button>

    </form>
    
</body>
</html>