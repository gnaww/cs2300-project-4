<?php include('includes/init.php');

$current_page_id = "login";

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link rel="stylesheet" type="text/css" href="styles/all.css"/>
  <title>Log In</title>
</head>

<body>
  <?php include('includes/header.php'); ?>

  <?php
    // User is logged in
    if (!$current_user) { ?>
      <div class='row login'>
        <?php
        print_messages();
        ?>
        <div class="column column-2">&nbsp;</div> <!--spacer-->
        <div class="column column-8 login_header">
          <h1>Administrator Login</h1>
        </div>
        <div class="column column-2">&nbsp;</div> <!--spacer-->
      </div>

      <div class='row login'>
      <div class="column column-2">&nbsp;</div> <!--spacer-->
      <div class="column column-8 login_body">
        <form action='/login.php' method='post'>
          <div class='row'>
            <ul>
              <li>
                <label>Username:</label>
                <input type='text' name='username' required>
              </li>
            </ul>
          </div>
          <div class='row'>
            <ul>
              <li>
                <label>Password:</label>
                <input type='password' name='password' required>
              </li>
              <li>
                <button name= 'login' type='submit'>SUBMIT</button>
              </li>
            </ul>
          </div>
          </form>
      </div>
      <div class="column column-2">&nbsp;</div> <!--spacer-->
      </div>
  <?php
    }
    else { // User is already logged in
      echo "<div class='warning_message'>
              <h1>Successfully logged in as $current_user.</h1>
            </div>";
    }
  ?>
  <?php include('includes/footer.php'); ?>
</body>
</html>
