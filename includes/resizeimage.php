<?php
// Resize the image to use as a thumbnail on the category page
$image = $_GET['image'];
$filename = "../images/" . $image;
$image = imagecreatefromjpeg($filename);
$imgResized = imagescale($image , 200, 230);
imagejpeg($imgResized);