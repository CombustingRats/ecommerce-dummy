<?php
$newpassword = "ilovebubu";
$newhash = password_hash($newpassword, PASSWORD_BCRYPT);
echo $newhash;