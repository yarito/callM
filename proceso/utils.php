<?
//Funcion de lectura de archivos
function getFiles($directory) {
  // Try to open the directory
  if($dir = opendir($directory)) {
    // Create an array for all files found
    $tmp = Array();
    // Add the files
    while($file = readdir($dir)) {
      // Make sure the file exists
      if($file != "." && $file != ".." && $file[0] != '.') {
        // If it's a directiry, list all files within it
        if(is_dir($directory . "/" . $file)) {
          $tmp2 = getFiles($directory . "/" . $file);
          if(is_array($tmp2)) {
            $tmp = array_merge($tmp, $tmp2);
          }
        } else {
          array_push($tmp, $file);
        }
      }
    }

    // Finish off the function
    closedir($dir);
    return $tmp;
  }
}

function fixtext($TEXT){
$TEXT=trim(str_replace("'","''",$TEXT));
$TEXT=trim(str_replace(chr(92).chr(92),"",$TEXT));
return $TEXT;


} 



?>
