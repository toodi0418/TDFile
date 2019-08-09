<?php //connect to Database
session_start(); 
include_once $_SERVER['DOCUMENT_ROOT'] . '/securimage/securimage.php';
$securimage = new Securimage();
include_once($_SERVER['DOCUMENT_ROOT'].'/fm-config.php');
if(!empty($sitename)) {
  $sitename = 'TDFile';
}
function ipadr() {
  if(!empty($_SERVER["HTTP_CF_CONNECTING_IP"])) {
    return $myip = $_SERVER["HTTP_CF_CONNECTING_IP"];
  }
  else if(!empty($_SERVER['HTTP_CLIENT_IP'])){
   return $myip = $_SERVER['HTTP_CLIENT_IP'];
}else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
   return $myip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}else{
   return $myip= $_SERVER['REMOTE_ADDR'];
}
}
try {

	$db = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", "$DB_USER", "$DB_PASW", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	
	$db-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  logm($type = "error",$t2 = "MySQL",$msg = $e->getMessage());
}
function logm($type,$t2,$msg) {
 if(!empty($type) && !empty($msg) && !empty($t2)) {
  if(is_file($_SERVER['DOCUMENT_ROOT'].'/server.log')) {
    $logfext = true;
  }
   $file = fopen($_SERVER['DOCUMENT_ROOT'].'/server.log',"a+");
   if(!empty($logfext) && $logfext = true) {
     fwrite($file,"\n".json_encode(array("type" => $type , "t2" => $t2 , "msg" => $msg , "time" => date("Y/m/d H:i:s") , "IP" => ipadr())));
   }else {
     fwrite($file,json_encode(array("type" => $type , "t2" => $t2 , "msg" => $msg , "time" => date("Y/m/d H:i:s") , "IP" => ipadr())));
   }
    fclose($file);
 }

}

function guid($namespace = '') {  
  static $guid = '';
  $uid = uniqid("", true);
  @$data = $namespace;
  @$data .= $_SERVER['REQUEST_TIME'];
  @$data .= $_SERVER['HTTP_USER_AGENT'];
  @$data .= $_SERVER['LOCAL_ADDR'];
  @$data .= $_SERVER['LOCAL_PORT'];
  @$data .= $_SERVER['REMOTE_ADDR'];
  @$data .= $_SERVER['REMOTE_PORT'];
  @$hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
  $guid = '{' . 
      substr($hash, 0, 8) .
      '-' .
      substr($hash, 8, 4) .
      '-' .
      substr($hash, 12, 4) .
      '-' .
      substr($hash, 16, 4) .
      '-' .
      substr($hash, 20, 12) .
      '}';
  return $guid;
 }
cidcheck('get');
function cidcheck($method) {
  if(empty($_SESSION['cid'])) {
    $_SESSION['cid'] = guid();
    return true;
  }else {
    if($method == 'get') {
      if(!empty($_GET['cid']) && $_GET['cid'] == $_SESSION['cid']) {
        return true;
      }else {
        return false;
      }
    }else if($method == 'post') {
      if(!empty($_POST['cid']) && $_GET['cid'] == $_SESSION['cid']) {
        return true;
      }else {
        return false;
      }
    }
      
    }
}
function str_is_special($l1)
{
    $l2 = "&,',\",<,>,!,%,#,$,@,=,?,/,(,),[,],{,},.,+,*,_";
    $I2 = explode(',', $l2);
    $I2[] = ",";

    foreach ($I2 as $v) {
       if (strpos($l1, $v) !== false) {
           return true;
       }
    }
    return false;
}
function str_is_specialdn($l1)
{
    $l2 = "&,',\",<,>,!,%,#,$,@,=,?,/,(,),[,],.,+,*,_";
    $I2 = explode(',', $l2);
    $I2[] = ",";

    foreach ($I2 as $v) {
       if (strpos($l1, $v) !== false) {
           return true;
       }
    }
    return false;
}
/**
 * Define the number of blocks that should be read from the source file for each chunk.
 * For 'AES-128-CBC' each block consist of 16 bytes.
 * So if we read 10,000 blocks we load 160kb into memory. You may adjust this value
 * to read/write shorter or longer chunks.
 */
define('FILE_ENCRYPTION_BLOCKS', 10000);

/**
 * Encrypt the passed file and saves the result in a new file with ".enc" as suffix.
 * 
 * @param string $source Path to file that should be encrypted
 * @param string $key    The key used for the encryption
 * @param string $dest   File name where the encryped file should be written to.
 * @return string|false  Returns the file name that has been created or FALSE if an error occured
 */
function encryptFile($source, $key, $dest)
{
    $key = substr(sha1($key, true), 0, 16);
    $iv = openssl_random_pseudo_bytes(16);

    $error = false;
    if ($fpOut = fopen($dest, 'w')) {
        // Put the initialzation vector to the beginning of the file
        fwrite($fpOut, $iv);
        if ($fpIn = fopen($source, 'rb')) {
            while (!feof($fpIn)) {
                $plaintext = fread($fpIn, 16 * FILE_ENCRYPTION_BLOCKS);
                $ciphertext = openssl_encrypt($plaintext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                // Use the first 16 bytes of the ciphertext as the next initialization vector
                $iv = substr($ciphertext, 0, 16);
                fwrite($fpOut, $ciphertext);
            }
            fclose($fpIn);
        } else {
            $error = true;
        }
        fclose($fpOut);
    } else {
        $error = true;
    }

    return $error ? false : $dest;
}
/**
 * Dencrypt the passed file and saves the result in a new file, removing the
 * last 4 characters from file name.
 * 
 * @param string $source Path to file that should be decrypted
 * @param string $key    The key used for the decryption (must be the same as for encryption)
 * @param string $dest   File name where the decryped file should be written to.
 * @return string|false  Returns the file name that has been created or FALSE if an error occured
 */
function decryptFile($source, $key, $dest)
{
    $key = substr(sha1($key, true), 0, 16);

    $error = false;
    if ($fpOut = fopen($dest, 'w')) {
        if ($fpIn = fopen($source, 'rb')) {
            // Get the initialzation vector from the beginning of the file
            $iv = fread($fpIn, 16);
            while (!feof($fpIn)) {
                $ciphertext = fread($fpIn, 16 * (FILE_ENCRYPTION_BLOCKS + 1)); // we have to read one block more for decrypting than for encrypting
                $plaintext = openssl_decrypt($ciphertext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                // Use the first 16 bytes of the ciphertext as the next initialization vector
                $iv = substr($ciphertext, 0, 16);
                fwrite($fpOut, $plaintext);
            }
            fclose($fpIn);
        } else {
            $error = true;
        }
        fclose($fpOut);
    } else {
        $error = true;
    }

    return $error ? false : $dest;
}
function DownloadFile($source, $key)
{
    $key = substr(sha1($key, true), 0, 16);

    $error = false;
        if ($fpIn = fopen($source, 'rb')) {
            // Get the initialzation vector from the beginning of the file
            $iv = fread($fpIn, 16);
            while (!feof($fpIn)) {
                $ciphertext = fread($fpIn, 16 * (FILE_ENCRYPTION_BLOCKS + 1)); // we have to read one block more for decrypting than for encrypting
                $plaintext = openssl_decrypt($ciphertext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                // Use the first 16 bytes of the ciphertext as the next initialization vector
                $iv = substr($ciphertext, 0, 16);
                echo $plaintext;
            }
            fclose($fpIn);
        } else {
            $error = true;
        }
}
function formatBytes($size) { 
$units = array(' B', ' KB', ' MB', ' GB', ' TB'); 
for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024; 
return round($size, 2).$units[$i]; 
}
function download($id) {
  global $db;
  $select = $db->prepare("SELECT * FROM `files` WHERE `filequid` LIKE '".$id."'");
  $select->execute(array($id));
  $file = $select->fetchAll(PDO::FETCH_ASSOC);
  if(!empty($file[0]['id'])) {
    if(is_file($_SERVER['DOCUMENT_ROOT'].'/backend/files/'.$file[0]['filequid'])) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename='.base64_decode(htmlspecialchars_decode($file[0]['filename'])));
      header('Content-Transfer-Encoding: binary');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . $file[0]['filesize']);
      //ob_clean();
      //flush();
      echo DownloadFile($_SERVER['DOCUMENT_ROOT'].'/backend/files/'.$file[0]['filequid'],$file[0]['filepass']);
    }else {
      return '404 NotFound.';
    }
  }else {
    return '404 NotFound.';
  }
}
function checkLogin() {
  if(empty($_SESSION["usr"]) or empty($_SESSION['psw']) or empty($_SESSION['login'])) {
    return false;
  }else {
    return true;
  }
}
function userinfo($username,$youwants) {
  global $db;
  $select = $db->prepare("SELECT * FROM `users` WHERE `username` LIKE '".$username."'");
  $select->execute(array($username));
  $user = $select->fetchAll(PDO::FETCH_ASSOC);
  if(!empty($user[0]['id'])) {
    if(!empty($youwants)){
      if($youwants == 'groups') {
        return $user[0]['groups'];
      }elseif($youwants == 'name') {
        return $user[0]['name'];
      }elseif($youwants == 'department') {
        return $user[0]['department'];
      }
    }
  }
}

?>