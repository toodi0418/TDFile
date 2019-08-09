<?php include_once($_SERVER['DOCUMENT_ROOT'].'/function.php');
?>
<!--
 
                        _oo0oo_
                       o8888888o
                       88" . "88
                       (| -_- |)
                       0\  =  /0
                     ___/`---'\___
                   .' \\|     |  '.
                  / \\|||  :  |||  \
                 / _||||| -:- |||||- \
                |   | \\\  -   / |   |
                | \_|  ''\---/''  |_/ |
                \  .-\__  '-'  ___/-. /
              ___'. .'  /--.--\  `. .'___
           ."" '<  `.___\_<|>_/___.' >' "".
          | | :  `- \`.;`\ _ /`;.`/ - ` : | |
          \  \ `_.   \_ __\ /__ _/   .-` /  /
      =====`-.____`.___ \_____/___.-`___.-'=====
                        `=---='
 
 
      ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 
                佛祖保佑         永无BUG
 -->
 
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title><?php echo htmlspecialchars($sitename,ENT_QUOTES) ?>檔案服務</title>

  <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">

  <!-- Custom styles -->
  <link href="css/jquery.dm-uploader.min.css" rel="stylesheet">
  <link href="css/ionicons.min.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">
  <style>
    .jumbotron {
      background-color: #ffffff;
      margin-bottom: -1rem;
    }

    ;
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark"><a class="navbar-brand" href="#"><?php echo htmlspecialchars($sitename,ENT_QUOTES) ?>檔案服務</a><button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="navbarColor01">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active"><a class="nav-link" href="#">首頁 <span class="sr-only">(current)</span></a></li>
      </ul>
      <form class="form-inline"><button type="button" class="btn btn-outline-warning" id="cl" style="display:none" data-toggle="modal" data-target="#LoginModal"><ion-icon name="person"></ion-icon> 登入</button><button type="button" class="btn btn-outline-info" id="ct" style="display:none" data-toggle="modal" data-target="#ManageModal"><ion-icon name="build"></ion-icon> 管理</button><button type="button" class="btn btn-outline-warning" id="lo" style="display:none" onclick="logout()"><ion-icon name="arrow-round-forward"></ion-icon> 登出</button></form>
    </div>
  </nav>
  <main role="main" class="container">
    <div class="jumbotron w-100">
      <h1 class="display-4"><?php echo htmlspecialchars($sitename,ENT_QUOTES) ?>檔案服務</h1>
      <p class="lead">一個由<?php echo htmlspecialchars($sitename,ENT_QUOTES) ?>提供的檔案服務</p>
      <hr class="my-4">
      <p>在這裡，您可以上傳檔案，具有權限之使用者則可以下載檔案，很簡單吧!</p>
      <div class="row">
        <div class="col">
          <a class="btn btn-primary btn-lg btn-block" href="#" role="button">
            <ion-icon name="cloud-upload"></ion-icon> 上傳檔案</a>
        </div>
        <div class="col">
          <a class="btn btn-success btn-lg btn-block" href="#" role="button" data-toggle="modal" data-target=".DownloadModal">
            <ion-icon name="cloud-download"></ion-icon> 下載檔案</a>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6 col-sm-12">

        <!-- Our markup, the important part here! -->
        <div id="drag-and-drop-zone" class="dm-uploader p-5">
          <h3 class="mb-5 mt-5 text-muted">請把檔案拖曳到這裡</h3>

          <div class="btn btn-primary btn-block mb-5">
            <span>或者點選這裡來選取檔案</span>
            <input type="file" title='Click to add Files' />
          </div>
          <li class="list-group-item">
              公開檔案
              <label class="switch ">
          <input type="checkbox" class="default" id='publfi' aria-describedby="captchaHelp" name="publfi">
          <span class="slider round"></span>
        </label>
              <small id="captchaHelp" class="form-text text-muted">若勾選這個選項，任何人都可下載檔案（限登入使用）</small>
            </li>
        </div>
        <!-- /uploader -->

      </div>
      <div class="col-md-6 col-sm-12">
        <div class="card h-100">
          <div class="card-header">
            檔案列表
          </div>

          <ul class="list-unstyled p-2 d-flex flex-column col" id="files">
            <li class="text-muted text-center empty">目前沒有任何檔案被上傳</li>
          </ul>
        </div>
      </div>
    </div>
    <!-- /file list -->

  </main>
  <!-- /container -->

  <footer class="text-center">
    版權所有 &copy; Toodi Kao &middot; 授權給<?php echo htmlspecialchars($sitename,ENT_QUOTES) ?>
    <p><a href="https://www.instagram.com/toodi0418/"> <ion-icon name="logo-instagram"></ion-icon> Instagram</a>、<a href="https://www.facebook.com/profile.php?id=100028157617275"> <ion-icon name="logo-facebook"></ion-icon> Facebook</a> <a href="https://www.tooditech.com/"><ion-icon name="globe"></ion-icon> Website</a></p>
    <!-- <b>本系統由 Toodi Kao，開發並授權</b> -->
  </footer>

  <!-- LoginModal -->
  <div class="modal fade" id="LoginModal" tabindex="-1" role="dialog" aria-labelledby="LoginModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="LoginModalTitle">
            <ion-icon name="contact"></ion-icon> 使用者登入</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning" role="alert" id="lowr">
            這裡是一個警告消息放置的一個地方！
          </div>
          <form name="isform" id="isform">
            <div class="form-group">
              <label for="exampleInputEmail1">使用者名稱</label>
              <input type="email" class="form-control" id="usernme" placeholder="請輸入使用者名稱" name="usernme">
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">密碼</label>
              <input type="password" class="form-control" id="passwrd" placeholder="請輸入使用者密碼" name="passwrd">
            </div>
            <div class="form-group">
              <label for="coddeee">驗證碼</label>
              <div class="row">
                <div class="col">
                  <img id="captcha" src="/securimage/securimage_show.php" alt="CAPTCHA Image" />
                </div>
                <div class="col">
                  <input type="text" class="form-control" name="captcha_code" placeholder="請輸入驗證碼" id="captcha_v">
                  <a href="#" onclick="document.getElementById('captcha').src = '/securimage/securimage_show.php?' + Math.random(); return false">[ 重新給我一張 ]</a>
                </div>
              </div>
            </div>
            <li class="list-group-item">
              記住我
              <label class="switch ">
          <input type="checkbox" class="default" id='rmbme' aria-describedby="captchaHelp" name="rmbme">
          <span class="slider round"></span>
        </label>
              <small id="captchaHelp" class="form-text text-muted">請勿在公共電腦中勾選這個選項，以確保您的帳號安全</small>
            </li>
        </div>
        <button type="button" class="btn btn-primary" onclick="lcheck()" id="logb">登入 <ion-icon name="send"></ion-icon></button>
        </form>
      </div>
      <!--
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
       -->
    </div>
  </div>
<div class="modal fade DownloadModal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="LoginModalTitle">
            <ion-icon name="cloud-download"></ion-icon> 檔案下載</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        </div>
      <div class="modal-body">
        <div class="alert alert-secondary" role="alert" id="dmsg" style="display:none">
  這裡是放置消息的地方！
</div>
        <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item active" aria-current="page">公開檔案</li>
  </ol>
</nav>
            <div id="downloadArea">
      
    </div>
        <div id="nonpublic" style="display:none">
          <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item active" aria-current="page">非公開檔案</li>
  </ol>
</nav>
          <div id="nonpdownloadArea">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  <div class="modal fade ManageModal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" id="ManageModal">
  <div class="modal-dialog modal-xl">
    
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="ManageModalTitle">
            <ion-icon name="hammer"></ion-icon> 管理面板</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        </div>
      <div class="modal-body">
        <div class="alert alert-secondary" role="alert" id="amsg" style='display:none'>
  此處僅有管理員才可進入，請確認您的權限！
</div>
        <div id='cpanel'>
        </div>
      </div>
    </div>
  </div>
</div>
  </div>

  <script src="js/jquery-3.3.1.min.js"></script>
  <script src="js/bootstrap.min.js"></script>

  <script src="js/jquery.dm-uploader.min.js"></script>
  <script src="js/ionicons.js"></script>
  <script src="demo-ui.js"></script>
  <script src="demo-config.js"></script>
  

  <!-- File item template -->
  <script type="text/html" id="files-template">
    <li class="media">
      <div class="media-body mb-1">
        <p class="mb-2">
          <strong>%%filename%%</strong> - Status: <span class="text-muted">Waiting</span>
        </p>
        <div class="progress mb-2">
          <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
          </div>
        </div>
        <hr class="mt-1 mb-1" />
      </div>
    </li>
  </script>
  <!-- Debug item template -->
  <script type="text/html" id="debug-template">
    <li class="list-group-item text-%%color%%"><strong>%%date%%</strong>: %%message%%</li>
  </script>
  <script>
    function getcid() {
      cid = '<?php echo $_SESSION['cid']; ?>';
      return cid;
    }
  </script>
  <script src="site.js"></script>
</body>

</html>