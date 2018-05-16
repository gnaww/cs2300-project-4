
<header>
  <nav id="menu">
    <ul>
      <?php
        $page_title = '';
        foreach ($pages as $filename => $pagename) {
          if ($current_page_id == $filename) {
            echo "<li id='current_page'><a href='".$filename.".php'>".$pagename."</a></li>";
            $page_title = $pagename;
          }
          else {
            echo "<li><a href='".$filename.".php'>".$pagename."</a></li>";
          }
        }
      ?>
    </ul>
    <?php
      // If logged in display welcome message/log out button
      if ($current_user) { ?>
        <span class='welcome_message'>HELLO, <?php echo strtoupper($current_user); ?>!</span>
        <form action='<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>' method='post'>
          <button class='logout' name='logout' type='submit'>LOG OUT</button>
        </form>
    <?php
      }
    ?>
  </nav>
</header>
