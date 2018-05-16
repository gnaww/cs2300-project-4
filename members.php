<?php include('includes/init.php');
$current_page_id = "members";

// AJAX for search results
ob_start();
include('members_search.php');
$search_results = ob_get_clean();
ob_end_flush();

if (isset($_POST['delete_member'])) {
  $member_id = filter_input(INPUT_POST, 'delete_member', FILTER_VALIDATE_INT);
  $valid_member = TRUE;

  $db->beginTransaction();
  // Make sure member being deleted is valid club member
  if (is_int($member_id)) {
    $params = array(':id' => $member_id);
    $records = exec_sql_query($db, "SELECT * FROM members WHERE id=:id", $params)->fetchAll(PDO::FETCH_ASSOC);
    if (!$records) {
      record_message("Nonexistent member being deleted.");
      $valid_member = FALSE;
    }
  }
  else {
    record_message("Invalid member being deleted");
    $valid_member = FALSE;
  }

  if ($valid_member) {
    $sql = "DELETE FROM members WHERE id = :id";
    $params = array(':id' => $member_id);
    $result = exec_sql_query($db, $sql, $params);

    if ($result) {
      record_message("Successfully deleted member!");
      $db->commit();
    }
    else {
      record_message("Failed to delete member");
    }
  }
}

if (isset($_POST['submit_member'])) {
  $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
  $first_name = strtolower(trim($first_name));
  $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
  $last_name = strtolower(trim($last_name));
  $netid = filter_input(INPUT_POST, 'netid', FILTER_SANITIZE_STRING);
  $netid = trim($netid);
  $valid_data = TRUE;

  // Name regex: https://salesforce.stackexchange.com/questions/41153/best-regex-for-first-last-name-validation
  $name_regex = "/^[^±!@£$%^&*_+§¡€#¢§¶•ªº«\/<>?:;|=.,0-9]{1,}$/";

  // Check for valid name/NetID
  if (!preg_match($name_regex, $first_name) || !preg_match($name_regex, $last_name)) {
    $valid_data = FALSE;
    record_message("Invalid name.");
  }
  else if (!preg_match('/^[a-z]+[0-9]+$/', $netid)) {
    $valid_data = FALSE;
    record_message("Invalid NetID.");
  }

  $db->beginTransaction();
  // Check for unique NetID for new user
  $params = array(':netid' => $netid);
  $records = exec_sql_query($db, "SELECT * FROM members WHERE netid=:netid", $params)->fetchAll(PDO::FETCH_ASSOC);

  if ($records) {
    $valid_data = FALSE;
    record_message("Member with same NetID already exists!");
  }

  // Add new club member if everything is good
  if ($valid_data) {
    $first_name = ucfirst($first_name);
    $last_name = ucfirst($last_name);
    $sql = "INSERT INTO members (first_name, last_name, netid) VALUES (:first, :last, :netid)";
    $params = array(':first' => $first_name,
                    ':last' => $last_name,
                    ':netid' => $netid);
    $result = exec_sql_query($db, $sql, $params);
    if ($result) {
      record_message("Successfully added member!");
      $db->commit();
    }
    else {
      record_message("Failed to add member.");
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

  <script src="scripts/jquery-3.2.1.min.js"></script>
  <script src="scripts/members.js"></script>
  <title>Member's List</title>
</head>

<body>
  <?php include('includes/header.php'); ?>

  <?php
    // Only display page if user is logged in as admin
    if ($current_user == "admin") { ?>
      <div class='row members'>
        <div class="column column-2">&nbsp;</div> <!--spacer-->
        <div class="column column-8 members_header">
          <h1>MEMBERS</h1>
        </div>
        <div class="column column-2">&nbsp;</div> <!--spacer-->
      </div>
      <div class="row members">
        <div class="column column-3">&nbsp;</div>
        <div class="column column-6 members_form">
          <h1>Add New Member</h1>
          <form action="members.php" method="post">
            <ul>
              <li><label>First Name:</label> <input type="text" name="first_name" class="first_name_input" required></li>
              <li><label>Last Name:</label> <input type="text" name="last_name" class="last_name_input" required></li>
              <li><label>NetID:</label> <input type="text" name="netid" class="netid_input" required></li>
            </ul>
            <button name="submit_member" type="submit">SUBMIT</button>
          </form>
        </div>
        <div class="column column-3">&nbsp;</div>
      </div>
      <div class="row members">
        <div class="column column-3">&nbsp;</div>
        <div class="column column-6 members_search">
          <h1>Search Members</h1>
          <form id="search-members" action="members.php" method="get">
            <select name="search_field" required>
              <option value="" selected disabled>Select Search Field</option>
              <option value="name">Name</option>
              <option value="netid">NetID</option>
            </select>
            <input type="text" name="search_input" required>
            <button name="search_member" type="submit">SEARCH</button>
          </form>
        </div>
        <div class="column column-3">&nbsp;</div>
      </div>
      <?php
      print_messages();
      ?>
      <div class="row members">
        <div class="column column-2">&nbsp;</div>
          <div class="column column-8 members_table" id="search-results">
            <?php
              echo $search_results;
            ?>
          </div>
        <div class="column column-2">&nbsp;</div>
      </div>

      <div class="row members">
        <div class="column column-2">&nbsp;</div>
        <div class="column column-8 members_table">
          <h1>List of Club Members</h1>
          <?php
          $sql = "SELECT * FROM members ORDER BY last_name";
          $records = exec_sql_query($db, $sql, array())->fetchAll(PDO::FETCH_ASSOC);;
          if ($records) { ?>
            <table>
            <tr>
              <th>Last Name</th>
              <th>First Name</th>
              <th>NetID</th>
              <th></th>
            </tr>
            <?php print_members($records); ?>
            </table>
            <?php
          }
          else {
            echo "<h2>No club members found.</h2>";
          }
        ?>
        </div>
        <div class="column column-2">&nbsp;</div>
      </div>
  <?php
    }
    else {
      echo "<div class='warning_message'>
              <h1>Admin privilege required to access this page.</h1>
            </div>";
    }
  ?>
  <div class="row members">
    <div class="column column-12 members_spacer">&nbsp;</div>
  </div>
  <?php include('includes/footer.php'); ?>
</body>
</html>
