  <?php
    require_once $_SERVER['DOCUMENT_ROOT']."/user.php";
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT']."/header.php";
    //echo dirname(__FILE__); //Current absolute path
    //$_SERVER['DOCUMENT_ROOT']; //Server root path
    if($_SERVER['REQUEST_METHOD'] === 'GET' && (!isset($_SESSION['currentUser']) || $_SESSION['currentUser'] == NULL)) {
  ?>
  <body class='container'>
    <form class='panel panel-inverse vertical-center modal-dialog modal-sm' action='/' method='post'>
      <div class='panel-heading' style='font-size:15px;text-align:center;'><b>Личный кабинет</b></div>
      <div class='list-group panel-body'>
        <div class='input-group'>
          <span class='input-group-addon'>Логин:&nbsp;&nbsp;&nbsp;</span>
          <input type='text' name='login' class='form-control input-sm' required autofocus/>
        </div><br>
        <div class='input-group'>
          <span class='input-group-addon'>Пароль:</span>
          <input type='password' name='passwd' class='form-control input-sm' prequired/>
        </div>
      </div>
      <div class='panel-footer'><input type='submit' class='btn btn-success btn-block' value='Войти' style='font-size:14px;'/></div>
    </form>
  <?php } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' || ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['currentUser']))) {
      if($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_SESSION['currentUser']) || $_SESSION['currentUser'] == NULL)) {
        $user = User::login(htmlspecialchars($_POST['login']), htmlspecialchars($_POST['passwd']));
        $_SESSION['currentUser'] = $user;
      }
      if(isset($_SESSION['currentUser'])) {
        $_SESSION['currentKid'] = $_SESSION['currentUser']->kid;
        header("Location: /lk.php");
      } else {
        if(!isset($_SESSION['psql']) || !$_SESSION['psql'])
          pg_close($_SESSION['psql']);
        session_destroy();
        header("Location: /");
      }
      die();
    }
    include_once $_SERVER['DOCUMENT_ROOT']."/footer.php";
    ?>
