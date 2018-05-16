<?php include('includes/init.php');

$current_page_id = "gallery";

//delete an image
if (isset($_POST['delete_img'])) {
  $image_id = filter_input(INPUT_POST, 'delete_img', FILTER_SANITIZE_STRING);
  $db->beginTransaction();
  $sql = "SELECT file_ext FROM images WHERE id = :image_id";
  $params = array(':image_id' => $image_id);
  $records = exec_sql_query($db, $sql, $params)->fetchAll(PDO::FETCH_ASSOC);
  if ($records) {
    $image_ext = $records[0]['file_ext'];
    $sql = "DELETE FROM images WHERE id = :image_id";

    if (exec_sql_query($db, $sql, $params)) {
      if(unlink("uploads/images/$image_id.$image_ext")) {
          record_message("Successfully deleted the image");
          $db->commit();
      }
    } else {
          record_message("Unable to delete the image");
    }
  } else {
    record_message("Image to delete does not exist");
  }
}

//upload an image
if(isset($_POST['upload'])) {
  $image = $_FILES["image_file"];
  if ($image['error'] == UPLOAD_ERR_OK) {
    $image_ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
    $filename = strtolower(pathinfo($image['name'], PATHINFO_FILENAME));
    $sql = "INSERT INTO images (file_name, file_ext) VALUES (:filename, :extension)";
    $params = array(
      ':filename' => $filename,
      ':extension' => $image_ext,
    );
    $db->beginTransaction();
    if (exec_sql_query($db, $sql, $params)) {
      $file_id = $db->lastInsertId("id");
      $temp_filename = $image["tmp_name"];
      if (move_uploaded_file($temp_filename, "uploads/images/$file_id.$image_ext")){
        record_message("Successfully uploaded the image");
        $db->commit();
      }
    } else {
      record_message("Failed to upload image to the database");
    }
  } else if ($_FILES["image_file"]['error'] == 4){
      record_message("There was no image to upload");
  } else {
      record_message("Failed to upload the image");
  }
}

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link rel="stylesheet" type="text/css" href="styles/all.css"/>
  <title>Gallery</title>
</head>

<body>
  <?php include('includes/header.php'); ?>

  <div class='row'>
  <div class="column column-2">&nbsp;</div> <!--spacer-->
  <div class="column column-8 gallery_header">
    <h1>PHOTO GALLERY</h1>
  </div>
  <div class="column column-2">&nbsp;</div> <!--spacer-->
  </div>

  <?php
  if($current_user == "admin") { ?>
  <div class='row'>
    <div class="column column-3">&nbsp;</div> <!--spacer-->
    <div class ="column column-6 upload_image">
      <form action="gallery.php" method="post" enctype="multipart/form-data">
        <label>UPLOAD IMAGE</label>
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000"/>
        <input type="file" name="image_file" accept="image/*">
        <button class="upload_button" name="upload" type="submit">ADD IMAGE</button>
      </form>
    </div>
    <div class ="column column-3">&nbsp;</div> <!--spacer-->
  </div>
<?php
  } ?>


<?php print_messages(); ?>

<div class='row'>
  <div class = "column column-12 gallery">
      <?php
      $sql = "SELECT * FROM images";
      $records = exec_sql_query($db, $sql, array())->fetchAll(PDO::FETCH_ASSOC);;
      if (isset($records) and !empty($records)) {
          echo "<div class ='container'>";
          create_gallery($records);
          echo "</div>";
      } else {
          echo "<div class ='container full'>
                  <div id='noimage'>
                    <p><b>The gallery has no photos. Make some memories!</b></p>
                    <img class ='camera' src='images/camera.png' alt='camera'>
                    <a target='_blank' href='https://cdn0.iconfinder.com/data/icons/flat-designed-circle-icon/1000/camera.png'>Image Source</a>
                      <!-- Camera Image Source: https://cdn0.iconfinder.com/data/icons/flat-designed-circle-icon/1000/camera.png -->
                  </div>
                </div>";
      }
      ?>
  </div>
</div>

  <?php include('includes/footer.php'); ?>

</body>
</html>
