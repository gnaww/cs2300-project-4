<?php include('includes/init.php');
$current_page_id = "index";
$editmode = FALSE;

// Get current homepage information
$records = exec_sql_query($db, "SELECT * FROM homepage", array())->fetchAll(PDO::FETCH_ASSOC);
$homepage = $records[0];

if (isset($_POST['edit_homepage'])) {
  // Change into edit mode on page
  $editmode = TRUE;
}

if (isset($_POST['save_changes'])) {
  $about = trim(filter_input(INPUT_POST, 'about_content', FILTER_SANITIZE_STRING));
  $history = trim(filter_input(INPUT_POST, 'history_content', FILTER_SANITIZE_STRING));
  $valid_content = TRUE;

  // Make sure that About and History aren't empty
  if (strlen($about) < 1) {
    $valid_content = FALSE;
    record_message("Please input content for the about section.");
  }
  else if (strlen($history) < 1) {
    $valid_content = FALSE;
    record_message("Please input content for the history section.");
  }

  // If there was a new background image uploaded
  $image = $_FILES["background_file"];
  if ($image['error'] == UPLOAD_ERR_OK) {
    // Delete old background image from server
    $old_bg_filename = "images/" . $homepage["background"] . "." . $homepage["background_ext"];
    // Get new background image information
    $image_ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
    $filename = strtolower(pathinfo($image['name'], PATHINFO_FILENAME));
    $desc = trim(filter_input(INPUT_POST, 'background_description', FILTER_SANITIZE_STRING));
    $source = trim(filter_input(INPUT_POST, 'background_source', FILTER_SANITIZE_STRING));

    $sql = "UPDATE homepage SET background_ext=:img_ext, background_desc=:dsc, source=:src WHERE id=1";
    $params = array(
      ':img_ext' => $image_ext,
      ':dsc' => $desc,
      ':src' => $source
    );
    $db->beginTransaction();
    if (exec_sql_query($db, $sql, $params)) {
      $temp_filename = $image["tmp_name"];
      unlink($old_bg_filename); // Delete old file so it doesn't take up space uselessly
      if (move_uploaded_file($temp_filename, "images/bg.$image_ext")){ // Upload new background file to server
        record_message("Successfully changed homepage background.");
        $db->commit();
      }
    } else {
      record_message("Failed to change homepage background.");
    }
  }

  if ($valid_content) {
    $db->beginTransaction();

    $params = array(':about' => $about,
                    ':history' => $history);
    $sql = "UPDATE homepage SET about=:about, history=:history WHERE id=1";
    $result = exec_sql_query($db, $sql, $params);
    if ($result) {
      record_message("Successfully saved homepage content changes.");
      $db->commit();
    }
    else {
      record_message("Failed to save homepage content changes.");
    }
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="styles/all.css" media="all" />

  <link rel="stylesheet" type="text/css" href="styles/all.css"/>
  <!-- Library Source: http://tomasdostal.com/projects/html5ImagePreview/
       Edits to library JavaScript code made by William -->
  <script src="scripts/html5.image.preview.min.js"></script>
  <script src="scripts/jquery-3.2.1.min.js"></script>
  <script src="scripts/index.js"></script>
  <title>Home</title>
</head>

<body>
  <?php
  include("includes/header.php");
  // Get updated homepage information
  $records = exec_sql_query($db, "SELECT * FROM homepage", array())->fetchAll(PDO::FETCH_ASSOC);
  $homepage = $records[0];

  if ($editmode) { ?>
    <div class='row home'>
      <div class="column column-12 edit_home">
        <form action='/index.php' method='post'>
          <button name='cancel' type='submit'>CANCEL CHANGES</button>
        </form>
      </div>
    </div>
  <?php
  }
  else if ($current_user == "admin") { ?>
    <div class='row home'>
      <div class="column column-12 edit_home">
        <form action='/index.php' method='post'>
          <button name='edit_homepage' type='submit'>EDIT HOMEPAGE</button>
        </form>
      </div>
    </div>
  <?php
  }
  ?>
  <?php
  print_messages();
  ?>
  <div class='row home'>
    <div class="column column-2">&nbsp;</div> <!--spacer-->
    <div class="column column-8">
      <?php
      if ($editmode) { ?>
        <div class="home_form">
          <form action='/index.php' method='post' enctype='multipart/form-data'>
            <h2>Current Background:</h2>
            <img class="old_background" src='<?php echo "images/" . $homepage["background"] . "." . $homepage["background_ext"]; ?>' alt='<?php echo $homepage["background_desc"]; ?>'>
            <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
            <ul>
              <li>
                <label>Upload New Background:</label>
                <input type="file" name="background_file" id="bg_file" accept="image/*" onchange="previewImage(this,[256],2); updateForm();">
                <div class="imagePreview"></div>
              </li>
              <li>
                <label>Background Source:</label> <input type="text" name="background_source" placeholder="Image source" id="bg_src">
              </li>
              <li>
                <label>Background Description:</label> <input type="text" name="background_description" placeholder="Write a short description." id="bg_dsc">
              </li>
            </ul>
            <br>
            <button name= 'save_changes' type='submit'>SAVE CHANGES</button>
        </div>
      <?php
      }
      else { // Display mode?>
        <div class="home_image">
          <img src='<?php echo "images/" . $homepage["background"] . "." . $homepage["background_ext"]; ?>' class="background" alt='<?php echo $homepage["background_desc"]; ?>'>
          <h1>Cornell Bread Club</h1>
          <a href="<?php echo $homepage["source"]; ?>">Image Source</a>
        </div>
      <?php
      }
      ?>
    </div>
    <div class="column column-2">&nbsp;</div> <!--spacer-->
  </div>

  <div class='row home'>
    <div class="column column-2">&nbsp;</div> <!--spacer-->
    <div class="column column-8 home_about">
      <?php
      if ($editmode) { ?>
        <h1>ABOUT</h1>
          <textarea name='about_content' cols='70' rows='10' required><?php echo $homepage["about"]; ?></textarea>
          <br>
          <button name= 'save_changes' type='submit'>SAVE CHANGES</button>
      <?php
      }
      else { // Display mode ?>
        <h1>ABOUT</h1>
        <p><?php echo $homepage["about"]; ?></p>
      <?php
      }
      ?>
    </div>
    <div class="column column-2">&nbsp;</div> <!--spacer-->
  </div>

  <div class='row home'>
    <div class="column column-2">&nbsp;</div> <!--spacer-->
    <div class="column column-8 home_about">
      <?php
      if ($editmode) { ?>
        <h1>History</h1>
          <textarea name='history_content' cols='70' rows='10' required><?php echo $homepage["history"]; ?></textarea>
          <br>
          <button name= 'save_changes' type='submit'>SAVE CHANGES</button>
        </form>
      <?php
      }
      else { // Display mode ?>
        <h1>HISTORY</h1>
        <p><?php echo $homepage["history"]; ?></p>
      <?php
      }
      ?>
    </div>
    <div class="column column-2">&nbsp;</div> <!--spacer-->
  </div>
  <div class="row spacing"></div> <!--spacer to keep footer at bottom-->

  <?php include('includes/footer.php'); ?>
</body>
</html>
