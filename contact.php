<?php include('includes/init.php');

$current_page_id = "contact";

if (isset($_POST['send_email'])) {
  $valid_contact = TRUE;

  if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['subject']) && isset($_POST['content'])) {
    $sender_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $sender_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $subject = trim(filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING));
    $content = trim(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING));

    if (!preg_match("/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/", $sender_email)) {
      record_message("Invalid email address.");
      $valid_contact = FALSE;
    }
    else if (!preg_match("/^[^±!@£$%^&*_+§¡€#¢§¶•ªº«\/<>?:;|=.,0-9]{1,}$/", $sender_name)) {
      // Name regex: https://salesforce.stackexchange.com/questions/41153/best-regex-for-first-last-name-validation
      record_message("Invalid name.");
      $valid_contact = FALSE;
    }
    else if (strlen($subject) < 1) {
      record_message("Invalid email subject.");
      $valid_contact = FALSE;
    }
    else if (strlen($content) < 1) {
      record_message("Invalid email message.");
      $valid_contact = FALSE;
    }
  }
  else {
    $valid_contact = FALSE;
    record_message("Please fill out all required fields.");
  }

  if ($valid_contact) {
    $headers = "From: $sender_email";
    $content = "From: $sender_name \n \n $content";
    $content = wordwrap($content, 70);

    if (mail("wow7@cornell.edu", $subject, $content, $headers)) {
      record_message("Thank you for contacting Cornell Bread Club!");
    }
    else {
      record_message("Email failed to send, please try again.");
    }
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link rel="stylesheet" type="text/css" href="styles/all.css"/>
  <title>Contact Us</title>
</head>

<body>
  <?php include('includes/header.php'); ?>

  <!-- Contact header -->
  <div class='row contact'>
    <div class="column column-2">&nbsp;</div> <!--spacer-->
    <div class="column column-8 contact_header">
      <h1>CONTACT US</h1>
    </div>
    <div class="column column-2">&nbsp;</div> <!--spacer-->

  </div>
  <?php
  print_messages();
  ?>
  <!-- Contact form -->
  <div class='row contact'>
  <div class="column column-2">&nbsp;</div> <!--spacer-->
  <div class="column column-8 contact_body">
    <form action='/contact.php' method='post'>
      <label>Your Name</label>
      <br>
      <input type='text' name='name' required>
      <br><br>
      <label>Your Email Address</label>
      <br>
      <input type='text' name='email' required>
      <br><br>
      <label>Subject</label>
      <br>
      <input type='text' name='subject' required>
      <br><br>
      <label>Your Message</label>
      <br>
      <textarea name='content' cols='20' rows='10' required></textarea>
      <br>
      <button name='send_email' type='submit'>SUBMIT</button>
    </form>
  </div>
  <div class="column column-2">&nbsp;</div> <!--spacer-->
  </div>

  <?php include('includes/footer.php'); ?>
</body>
</html>
