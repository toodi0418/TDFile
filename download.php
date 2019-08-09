<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/function.php');
if(!empty($_GET['id']) && str_is_specialdn($_GET['id']) == false) {
  $select = $db->prepare("SELECT * FROM `files` WHERE `filequid` LIKE '".$_GET['id']."'");
  $select->execute(array($_GET['id']));
  $file = $select->fetchAll(PDO::FETCH_ASSOC);
  if(!empty($file[0]['id'])) {
    if($file[0]['public'] == 'yes') {
      echo download($file[0]['filequid']);
    }else {
      if(!empty($_SESSION['login']) && $_SESSION['login'] = 1) {
        echo download($file[0]['filequid']);
      }else {
        echo '404 NotFound.';
      }
    }
  }
}else {
  echo '404 NotFound.';
}