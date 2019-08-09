<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/function.php');
if(!empty($_GET['action'])) {
  if($_GET['action'] == 'checklogin') {
    if(empty($_SESSION["usr"]) or empty($_SESSION['psw']) or empty($_SESSION['login'])) {
      echo '0';
    }else {
      echo 1;
    }
  }elseif($_GET['action'] == 'chkchked') {
    if(!empty($_COOKIE['rmbme']) && $_COOKIE['rmbme'] == 1) {
      echo 1;
    }else {
      echo 0;
    }
  }elseif($_GET['action'] == 'checklg') {
    if(!empty($_POST['usernme'])) {
      if(strlen($_POST['usernme']) < 21) {
        if(str_is_special($_POST['usernme']) == false) {
          if(!empty($_POST['passwrd'])) {
            if(strlen($_POST['passwrd']) < 129) {
              if(!empty($_POST['captcha_code'])) {
                if ($securimage->check($_POST['captcha_code']) == true) {
                  $sstr = "SELECT * FROM `users` WHERE `username` LIKE '".$_POST['usernme']."'";
                  $select = $db->prepare($sstr);
                  $select->execute(array($_POST['usernme']));
                  $member = $select->fetch(PDO::FETCH_ASSOC);
                  if(!empty($member['username']) && $member['username'] == $_POST['usernme']) {
                    if(password_verify($_POST['passwrd'],$member['password'])) {
                      $_SESSION['login'] = 1;
                      $_SESSION['usr'] = $_POST['usernme'];
                      $_SESSION['psw'] = $_POST['passwrd'];
                      logm('info','Login',"帳號 ".$_POST['usernme']." 已成功登入! UA: ".$_SERVER['HTTP_USER_AGENT'].' SESSID '.$_COOKIE['PHPSESSID']);
                      if(!empty($_POST['rmbme'])) {
                        setcookie("rmbme", 1, time()+86400);
                        setcookie("usr", $_POST['usernme'], time()+86400);
                      }
                      echo 10;
                    }else {
                      echo 8;
                    }
                  }else {
                    echo 8;
                  }
                }else {
                  echo 7;
                }
              }else {
                echo 6;
              }
            }else {
              echo 5;
            }
          }else {
            echo 4;
          }
        }else {
          echo 3;
        }
      }else {
        echo 2;
      }
    }else {
      echo 1;
    }
  }elseif($_GET['action'] == 'getDownloadlist') { 
    $select = $db->prepare("SELECT * FROM `files` WHERE `public` LIKE 'yes'");
    $select->execute(array('yes'));
    $dlist = $select->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="table-responsive">
<table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">檔案名稱</th>
      <th scope="col">檔案大小</th>
      <th scope="col">新增時間</th>
      <?php if(checkLogin()) { if(userinfo($_SESSION['usr'],'groups') == 'admin') { ?><th scope="col">建立者</th><?php } } ?>
      <?php if(checkLogin()) { if(userinfo($_SESSION['usr'],'groups') == 'admin') { ?><th scope="col">建立IP</th><?php } } ?>
      <th scope="col">動作</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($dlist as $row) { ?>
    <tr>
      <th scope="row"><?php echo $row['id']; ?></th>
      <td><?php echo base64_decode($row['filename']); ?></td>
      <td><?php echo formatBytes($row['filesize']); ?></td>
      <td><?php echo $row['timestamp']; ?></td>
      <?php if(checkLogin()) { if(userinfo($_SESSION['usr'],'groups') == 'admin') { ?><th scope="col"><?php echo $row['username']; ?></th><?php } } ?>
      <?php if(checkLogin()) { if(userinfo($_SESSION['usr'],'groups') == 'admin') { ?><th scope="col"><?php echo $row['UploadIP']; ?></th><?php } } ?>
      <td>
        <a href="download.php?id=<?php echo $row['filequid']; ?>" class="btn btn-outline-primary" role="button" aria-pressed="true" target="_blank">下載</a>
        <?php if(!empty($_SESSION['login']) && $_SESSION['login'] == 1 && !empty($_SESSION['usr'])) {if($_SESSION['usr'] == $row['username'] or userinfo($_SESSION['usr'],'groups') == 'admin') { ?><a href="#" class="btn btn-outline-warning" role="button" aria-pressed="true" onclick="delfil('<?php echo $row['filequid']; ?>')">刪除</a><?php } } ?>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>
</div>
  <?php 
  }elseif($_GET['action'] == 'getnonpDownloadlist') {
    if(checkLogin()) {
      $select = $db->prepare("SELECT * FROM `files` WHERE `public` LIKE 'no'");
      $select->execute(array('no'));
      $dlist = $select->fetchAll(PDO::FETCH_ASSOC);
      ?>
<div class="table-responsive">
<table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">檔案名稱</th>
      <th scope="col">檔案大小</th>
      <th scope="col">新增時間</th>
      <?php if(checkLogin()) { if(userinfo($_SESSION['usr'],'groups') == 'admin') { ?><th scope="col">建立者</th><?php } } ?>
      <?php if(checkLogin()) { if(userinfo($_SESSION['usr'],'groups') == 'admin') { ?><th scope="col">建立IP</th><?php } } ?>
      <th scope="col">動作</th>
    </tr>
  </thead>
  <tbody>
<?php foreach($dlist as $row) { ?>
    <tr>
      <th scope="row"><?php echo $row['id']; ?></th>
      <td><?php echo base64_decode($row['filename']); ?></td>
      <td><?php echo formatBytes($row['filesize']); ?></td>
      <td><?php echo $row['timestamp']; ?></td>
      <?php if(checkLogin()) { if(userinfo($_SESSION['usr'],'groups') == 'admin') { ?><th scope="col"><?php echo $row['username']; ?></th><?php } } ?>
      <?php if(checkLogin()) { if(userinfo($_SESSION['usr'],'groups') == 'admin') { ?><th scope="col"><?php echo $row['UploadIP']; ?></th><?php } } ?>
      <td>
        <a href="download.php?id=<?php echo $row['filequid']; ?>" class="btn btn-outline-primary" role="button" aria-pressed="true" target="_blank">下載</a>
        <?php if($_SESSION['usr'] == $row['username'] or userinfo($_SESSION['usr'],'groups') == 'admin') { ?><a href="#" class="btn btn-outline-warning" role="button" aria-pressed="true" onclick="delfil('<?php echo $row['filequid']; ?>')">刪除</a><?php } ?>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>
</div>
    <?php }else {
      echo '404 NotFound.';
    }
  }elseif($_GET['action'] == 'delfile' && !empty($_GET['id']) && str_is_specialdn($_GET['id']) == false && !empty($_SESSION['login']) && $_SESSION['login'] = 1 && cidcheck('get') == true) {
     $select = $db->prepare("SELECT * FROM `files` WHERE `filequid` LIKE '".$_GET['id']."'");
     $select->execute(array($_GET['id']));
     $file = $select->fetchAll(PDO::FETCH_ASSOC);
     if(!empty($file[0]['id'])) {
       if(is_file($_SERVER['DOCUMENT_ROOT'].'/backend/files/'.$file[0]['filequid'])) {
         if($file[0]['username'] == $_SESSION['usr'] or userinfo($_SESSION['usr'],'groups') == 'admin') {
           unlink($_SERVER['DOCUMENT_ROOT'].'/backend/files/'.$file[0]['filequid']);
           logm('info','DELETE','用戶 '.$_SESSION['usr'].' 刪除了一個檔案 '.$file[0]['filename'].' QUID: '.$file[0]['filequid']);
           $db -> exec("DELETE FROM `".$DB_NAME."`.`files` WHERE `files`.`id` = ".$file[0]['id']."");
           echo 1;
         }
       }
     }else {
       echo 0;
     }
  }elseif($_GET['action'] == 'changepublic' && !empty($_SESSION['login']) && $_SESSION['login'] = 1 && cidcheck('get') == true) {
    if($_GET['value'] == '1') {
      $_SESSION['publicbox'] = 1;
      echo 1;
    }else {
      $_SESSION['publicbox'] = 0;
      echo 0;
    }
  }elseif($_GET['action'] == 'getpublic' && !empty($_SESSION['login']) && $_SESSION['login'] = 1) {
    if(!empty($_SESSION['publicbox'])) {
      if($_SESSION['publicbox'] == '1') {
        echo 1;
      }else {
        echo 0;
      }
    }else {
      echo 0;
    }
  }elseif($_GET['action'] == 'logout' && !empty($_SESSION['login']) && $_SESSION['login'] = 1 && cidcheck('get') == true) {
    logm('info','Login',"帳號 ".$_SESSION['usr']." 已成功登出! UA: ".$_SERVER['HTTP_USER_AGENT'].' SESSID '.$_COOKIE['PHPSESSID']);
    if(!empty($_SESSION['usr'])) {
      unset($_SESSION['usr']);
    }
    if(!empty($_SESSION['psw'])) {
      unset($_SESSION['psw']);
    }
    if(!empty($_SESSION['login'])) {
      unset($_SESSION['login']);
    }
    echo 1;
  }elseif($_GET['action'] == 'getpanel'  && !empty($_SESSION['login']) && $_SESSION['login'] = 1) { ?>
    <div class="row">
  <div class="col-3">
    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
      <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Overview</a>
      <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">更改密碼</a>
      <?php if(checkLogin()) { if(userinfo($_SESSION['usr'],'groups') == 'admin') { ?><a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">用戶管理</a><?php } } ?>
      <?php if(checkLogin()) { if(userinfo($_SESSION['usr'],'groups') == 'admin') { ?><a class="nav-link" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">系統設置</a><?php } } ?>
    </div>
  </div>
  <div class="col-9">
    <div class="tab-content" id="v-pills-tabContent">
      <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
        歡迎 <?php echo userinfo($_SESSION['usr'],'name'); ?> 登入本系統！您的IP位址為 <?php echo ipadr(); ?><br>
        您的服務單位屬於 <?php echo userinfo($_SESSION['usr'],'department'); ?><br>
        您的登入身分為 <?php if(userinfo($_SESSION['usr'],'groups') == 'admin') { echo '系統管理者'; }else { echo '一般使用者'; } ?><br>
        請從左側選單選取您需要的項目
      </div>
      <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
        目前登入的使用者帳號為 <?php echo $_SESSION['usr']; ?> ，名稱為 <?php echo userinfo($_SESSION['usr'],'name'); ?>
        <hr>
        <form id='cpasswd'>
  <div class="form-group row">
    <label for="staticusername" class="col-sm-2 col-form-label">帳號</label>
    <div class="col-sm-10">
      <input type="text" readonly class="form-control-plaintext" id="staticusername" value="<?php echo $_SESSION['usr']; ?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-2 col-form-label">原密碼</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="inputPassword" placeholder="請輸入原始密碼" name="inputPassword">
    </div>
    <div class="form-group col-md-6">
      <label for="inputpass41">新密碼</label>
      <input type="password" class="form-control" id="inputpass41" placeholder="請輸入新密碼" name="inputpass41">
    </div>
    <div class="form-group col-md-6">
      <label for="inputcpass4">確認新密碼</label>
      <input type="password" class="form-control" id="inputcpass4" placeholder="請再次輸入新密碼" name="inputcpass4">
    </div>
    <div class="form-group">
              <label for="coddeee">驗證碼</label>
              <div class="row">
                <div class="col">
                  <img id="captcha1" src="/securimage/securimage_show.php" alt="CAPTCHA Image" />
                </div>
                <div class="col">
                  <input type="text" class="form-control" name="captcha_code" placeholder="請輸入驗證碼" id="captcha_a">
                  <a href="#" onclick="document.getElementById('captcha1').src = '/securimage/securimage_show.php?' + Math.random(); return false">[ 重新給我一張 ]</a><br>
                  <button type="button" class="btn btn-primary" onclick="cpss()" id="cpasb">確認更改 <ion-icon name="send"></ion-icon></button>
                </div>
              </div>
            </div>
  </div>
          
</form>
      </div>
      <?php if(checkLogin()) { if(userinfo($_SESSION['usr'],'groups') == 'admin') { ?><div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
      <div class="row">
        <div class="col">
          用戶管理
        </div>
        <div class=col>
          <button type="button" class="btn btn-primary" onclick="$('#adudiv').show();" id="addub">新增使用者 <ion-icon name="add"></ion-icon></button>
        </div>
      </div>
      <hr>
      <div id="adudiv" style="display:none">
        <form id="aduform">
  <div class="form-group">
    <label for="addusr">使用者帳號</label>
    <input type="text" class="form-control" id="addusr" placeholder="請輸入使用者帳號" name="addusr">
  </div>
  <div class="form-group">
    <label for="addpsw">使用者密碼</label>
    <input type="password" class="form-control" id="addpsw" placeholder="請輸入使用者密碼" name="addpsw">
  </div>
          <div class="form-group">
    <label for="addname">使用者名稱</label>
    <input type="text" class="form-control" id="addname" placeholder="請輸入使用者名稱" aria-describedby="usssname" name="addname">
    <small id="usssname" class="form-text text-muted">輸入自己所在的姓名 ex:高X恩</small>
  </div>
          <div class="form-group">
    <label for="adddepa">服務單位</label>
    <input type="text" class="form-control" id="adddepa" placeholder="請輸入服務單位" aria-describedby="department" name="adddepa">
    <small id="department" class="form-text text-muted">輸入自己所在的服務單位 ex:新北市立瑞芳國中 教務處</small>
  </div>
          <li class="list-group-item">
              系統管理員權限
              <label class="switch ">
          <input type="checkbox" class="default" id='adminOP' aria-describedby="adminOPHelp" name="adminOP">
          <span class="slider round"></span>
        </label>
              <small id="adminOPHelp" class="form-text text-muted">若勾選這個選項，使用者將成為系統管理員帳號（不建議）</small>
            </li>
          <div class="row">
            <div class="col">
              <button type="button" class="btn btn-primary btn-lg btn-block" onclick="adduser();"><ion-icon name="add"></ion-icon> 新增</button>
            </div>
            <div class="col">
              <button type="button" class="btn btn-danger btn-lg btn-block" onclick="$('#adudiv').hide();"><ion-icon name="undo"></ion-icon> 取消</button>
            </div>
          </div>  
</form>
      </div>
      <div class="table-responsive">
      <table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">名稱</th>
      <th scope="col">帳號</th>
      <th scope="col">單位</th>
      <th scope="col">權限</th>
      <th scope="col">操作</th>
    </tr>
  </thead>
  <tbody>
    <?php $statement = $db->query('SELECT * FROM `users`'); 
     foreach($statement as $row){ ?>
    <tr>
      <th scope="row"><?php echo $row['id']; ?></th>
      <th scope="row"><?php echo $row['name']; ?></th>
      <th scope="row"><?php echo $row['username']; ?></th>
      <th scope="row"><?php echo $row['department']; ?></th>
      <th scope="row"><?php echo $row['groups']; ?></th>
      <?php if($_SESSION['usr'] !== $row['username']) { ?> 
      <th scope="row">
        <button type="button" class="btn btn-danger" onclick="deluser('<?php echo $row['uid']; ?>')"><ion-icon name="close"></ion-icon> 刪除</button>
        <button type="button" class="btn btn-info" onclick="rstpwd('<?php echo $row['uid']; ?>')"><ion-icon name="unlock"></ion-icon> 重設密碼</button>
      </th>
    <?php }else { ?>
      <th scope="row">目前帳號</th>
     <?php } }
    ?>
  </tbody>
</table>
      </div>
      <b>註:重設密碼的密碼為 *tdpassw0rd*，請盡速更改密碼！</b>
      </div><?php } } ?>
      <?php if(checkLogin()) { if(userinfo($_SESSION['usr'],'groups') == 'admin') { ?><div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
      目前無提供該項功能！
      </div><?php } } ?>
    </div>
  </div>
</div>
  <?php }elseif($_GET['action'] == 'chpasswd' && !empty($_SESSION['login']) && $_SESSION['login'] = 1 && cidcheck('get') == true) {
    if(!empty($_POST['inputPassword'])) {
      if(strlen($_POST['inputPassword']) < 129) {
        if(!empty($_POST['inputpass41'])) {
          if(!empty($_POST['inputcpass4'])) {
            if($_POST['inputpass41'] == $_POST['inputcpass4']) {
              if($_POST['inputPassword'] !== $_POST['inputpass41']) {
                if(!empty($_POST['captcha_code'])) {
                  if ($securimage->check($_POST['captcha_code']) == true) {
                    $sstr = "SELECT * FROM `users` WHERE `username` LIKE '".$_SESSION['usr']."'";
                    $select = $db->prepare($sstr);
                    $select->execute(array($_SESSION['usr']));
                    $member = $select->fetch(PDO::FETCH_ASSOC);
                    if(password_verify($_POST['inputPassword'],$member['password'])) {
                      $db -> exec("UPDATE `".$DB_NAME."`.`users` SET `password` = '".password_hash($_POST['inputpass41'], PASSWORD_DEFAULT)."' WHERE `users`.`id` = ".$member['id'].";");
                      logm('info','changePassword','用戶 '.$member['username'].' 更改密碼成功！');
                      if(!empty($_SESSION['usr'])) {
                            unset($_SESSION['usr']);
                          }
                          if(!empty($_SESSION['psw'])) {
                            unset($_SESSION['psw']);
                          }
                      if(!empty($_SESSION['login'])) {
                        unset($_SESSION['login']);
                      }
                      echo 10;
                    }else {
                      echo 9;
                    }
                  }else {
                    echo 8;
                  }
                }else {
                  echo 7;
                }
              }else {
                echo 6;
              }
            }else {
              echo 5;
            }
          }else {
            echo 4;
          }
        }else {
          echo 3;
        }
      }else {
        echo 2;
      }
    }else {
      echo 1;
    }
  }elseif($_GET['action'] == 'adduser' && !empty($_SESSION['login']) && $_SESSION['login'] = 1 && cidcheck('get') == true && userinfo($_SESSION['usr'],'groups') == 'admin') {
    if(!empty($_POST['addusr'])) {
      if(str_is_special($_POST['addusr']) == false) {
        if(strlen($_POST['addusr']) < 21) {
          if(!empty($_POST['addpsw'])) {
            if(strlen($_POST['addusr']) < 129) {
              if(!empty($_POST['addname'])) {
                if(str_is_special($_POST['addname']) == false) {
                  if(strlen($_POST['addname']) < 21) {
                    if(!empty($_POST['adddepa'])) {
                      if(str_is_special($_POST['adddepa']) == false) {
                        if(strlen($_POST['adddepa']) < 51) {
                          if(!empty($_POST['adminOP'])) {
                            $adminOP ='admin';
                          }else {
                            $adminOP = 'normal';
                          }
                          $select = $db->prepare("SELECT * FROM `users` WHERE `username` LIKE '".$_POST['addusr']."'");
                          $select->execute(array($_POST['addusr']));
                          $mem = $select->fetchAll(PDO::FETCH_ASSOC);
                          if(empty($mem[0]['id'])) {
                            $db -> exec("INSERT INTO `".$DB_NAME."`.`users` (`id`, `uid`, `name`, `username`, `password`, `department`, `groups`) VALUES (NULL, '".guid()."', '".$_POST['addname']."', '".$_POST['addusr']."', '".password_hash($_POST['addpsw'], PASSWORD_DEFAULT)."', '".$_POST['adddepa']."', '".$adminOP."');");
                            logm('info','addUser','管理員 '.$_SESSION['usr'].' 新增了一個使用者 '.$_POST['addusr'].' 名稱: '.$_POST['addname'].' 單位: '.$_POST['adddepa'].' 權限: '.$adminOP);
                            echo 20;
                          }else {
                            echo 12;
                          }
                        }else {
                          echo 11;
                        }
                      }else {
                        echo 10;
                      }
                    }else {
                      echo 9;
                    }
                  }else {
                    echo 8;
                  }
                }else {
                  echo 7;
                }
              }else {
                echo 6;
              }
            }else {
              echo 5;
            }
          }else {
            echo 4;
          }
        }else {
          3;
        }
      }else {
        echo 2;
      }
    }else {
      echo 1;
    }
  }elseif($_GET['action'] == 'deluser' && !empty($_SESSION['login']) && $_SESSION['login'] = 1 && cidcheck('get') == true && userinfo($_SESSION['usr'],'groups') == 'admin') {
    if(!empty($_GET['id']) && str_is_specialdn($_GET['id']) == false) { 
      $select = $db->prepare("SELECT * FROM `users` WHERE `uid` LIKE '".$_GET['id']."'");
      $select->execute(array($_GET['id']));
      $mem = $select->fetchAll(PDO::FETCH_ASSOC);
      if(!empty($mem[0]['id'])) {
        if($_SESSION['usr'] !== $mem[0]['username']) {
          echo 1;
          logm('info','deleteUser','管理員 '.$_SESSION['usr'].' 成功刪除了使用者 '.$mem[0]['username']);
          $db -> exec("DELETE FROM `".$DB_NAME."`.`users` WHERE `users`.`id` = ".$mem[0]['id']."");
        }else {
          echo 0;
        }
      }else {
        echo 0;
      }
    }else {
      echo 0;
    }
    
  }elseif($_GET['action'] == 'rstpwd' && !empty($_SESSION['login']) && $_SESSION['login'] = 1 && cidcheck('get') == true && userinfo($_SESSION['usr'],'groups') == 'admin') {
    if(!empty($_GET['id']) && str_is_specialdn($_GET['id']) == false) {
      $select = $db->prepare("SELECT * FROM `users` WHERE `uid` LIKE '".$_GET['id']."'");
      $select->execute(array($_GET['id']));
      $mem = $select->fetchAll(PDO::FETCH_ASSOC);
      if(!empty($mem[0]['id'])) {
        if($_SESSION['usr'] !== $mem[0]['username']) {
          echo 1;
          logm('info','rstPSWD','管理員 '.$_SESSION['usr'].' 成功重設了使用者 '.$mem[0]['username'].' 的密碼');
          $db -> exec("UPDATE `".$DB_NAME."`.`users` SET `password` = '".password_hash('*tdpassw0rd*', PASSWORD_DEFAULT)."' WHERE `users`.`id` = ".$mem[0]['id'].";");
        }else {
          echo 0;
        }
      }else {
        echo 0;
      }
    }else {
      echo 0;
    }
  }
}
?>