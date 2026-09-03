<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$ID = $_POST['id'];
$folder = $_POST['folder'];
$x= true;
$directory = "../../../usbs/".$folder."/";

	
        if(delete_directory($directory)){
            if($CLD_CON->Execute("DELETE FROM usbs WHERE id=$ID")){
                echo "OK";
            }else{
                echo "ERROR1";
            }
        }else{
            echo "ERROR2";
        }
        
        

function delete_directory($dirname) {
         if (is_dir($dirname))
           $dir_handle = opendir($dirname);
	 if (!$dir_handle)
	      return false;
	 while($file = readdir($dir_handle)) {
	       if ($file != "." && $file != "..") {
	            if (!is_dir($dirname."/".$file))
	                 unlink($dirname."/".$file);
	            else
	                 delete_directory($dirname.'/'.$file);
	       }
	 }
	 closedir($dir_handle);
	 rmdir($dirname);
	 return true;
}
?>
