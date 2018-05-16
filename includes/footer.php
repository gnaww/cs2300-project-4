<div class='footer'>
  <div class='row'>
    <div class='column column-4 center'>
        <p><b>© <?php echo date("Y")?> Bread Making Club | Cornell University</b></p>
    </div>
    <div class='column column-4'>&nbsp;</div> <!--spacer-->
    <div class='column column-4'>
      <?php
        if (!$current_user) { ?>
          <a href='login.php'>Admin Login</a>
      <?php
        }
      ?>
    </div>
  </div>
</div>
