<?
header("Content-type: image/png");
$image = $_GET['image'];
if(file_exists($image)){
readfile($image);
}else{
readfile("window.png");
}
?>