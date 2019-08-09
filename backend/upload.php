<?php
set_time_limit(0);
include_once($_SERVER['DOCUMENT_ROOT'].'/function.php');

/*
  This is a ***DEMO*** , the backend / PHP provided is very basic. You can use it as a starting point maybe, but ***do not use this on production***. It doesn't preform any server-side validation, checks, authentication, etc.

  For more read the README.md file on this folder.

  Based on the examples provided on:
  - http://php.net/manual/en/features.file-upload.php
*/

header('Content-type:application/json;charset=utf-8');

try {
    if (
        !isset($_FILES['file']['error']) ||
        is_array($_FILES['file']['error'])
    ) {
        throw new RuntimeException('Invalid parameters.');
    }

    switch ($_FILES['file']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }
    $fname = $_FILES['file']['name'];
    $quid = guid();
    $pass = guid();
    $filepath = sprintf("I am who I am.");

    if (!move_uploaded_file(
        $_FILES['file']['tmp_name'],
        $filepath
    )) {
        throw new RuntimeException('Failed to move uploaded file.');
    }
  encryptFile($filepath, $pass, $_SERVER['DOCUMENT_ROOT'].'/backend/files/'.$quid);
if(file_exists($filepath)){
  unlink($filepath);//將檔案刪除
}
logm("info","Upload","上傳了檔案 $fname");
 if(checkLogin()) {
   if(!empty($_SESSION['publicbox']) && $_SESSION['publicbox'] == 1) {
     $publ = 'yes';
   }else {
     $publ = 'no';
   }
   $db -> exec("INSERT INTO `".$DB_NAME."`.`files` (`id`, `username`, `filename`, `filesize`, `filequid`, `filepass`, `public`, `UploadIP`, `timestamp`) VALUES (NULL, '".$_SESSION['usr']."', '".base64_encode(htmlspecialchars($fname,ENT_QUOTES))."', '".$_FILES['file']['size']."', '".$quid."', '".$pass."', '".$publ."', '".ipadr()."', '".date("Y/m/d H:i:s")."');");
 }else {
   $db -> exec("INSERT INTO `".$DB_NAME."`.`files` (`id`, `username`, `filename`, `filesize`, `filequid`, `filepass`, `public`, `UploadIP`, `timestamp`) VALUES (NULL, '', '".base64_encode(htmlspecialchars($fname,ENT_QUOTES))."', '".$_FILES['file']['size']."', '".$quid."', '".$pass."', 'no', '".ipadr()."', '".date("Y/m/d H:i:s")."');");
 }
    // All good, send the response
    echo json_encode([
        'status' => 'ok',
        'path' => $filepath
    ]);
} catch (RuntimeException $e) {
	// Something went wrong, send the err message as JSON
	http_response_code(400);
logm("error","Upload",$e->getMessage());
	echo json_encode([
		'status' => 'error',
		'message' => $e->getMessage()
	]);
}