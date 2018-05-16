<?php
global $current_user;
//Set up all pages
$pages = array(
      'index' => "HOME",
      'events' => "EVENTS",
      'gallery' => "GALLERY",
      'contact' => "CONTACT US"
    );

$messages = array();

// Messages functionality.
// Record a message to display to the user.
function record_message($message) {
  global $messages;
  array_push($messages, $message);
}

// Deliver messages to user
function print_messages() {
  global $messages;
  foreach ($messages as $message) {
    echo "<p class='message'><strong>" . htmlspecialchars($message) . "</strong></p>\n";
  }
}

// Print out club member information
function print_members($members) {
  foreach ($members as $member) {
    echo "<tr>";
    echo "<td>" . $member['last_name'] . "</td>";
    echo "<td>" . $member['first_name'] . "</td>";
    echo "<td>" . $member['netid'] . "</td>";
    echo "<td class='delete_member'>
            <form action='members.php' method='post'>
              <button name='delete_member' value='" . $member['id'] . "' type='submit' onclick='return confirm(\"Are you sure you want to delete this member?\")'>
              <img src='/images/x.png' alt='Delete'></button>
            </form>
          </td>";
    echo "</tr>";
  }
  // Button image source: https://www.shareicon.net/out-sign-out-close-no-deny-x-delete-119646
}


// Create gallery
function create_gallery($images) {
  global $current_user;
  foreach($images as $image) {
    $file = $image['id'] . '.' . $image['file_ext'];
    echo "<div class = 'photo'>";
      if ($current_user == "admin") {
        echo "<a target='_blank' href='uploads/images/$file'>
                <img class ='admin' src='uploads/images/$file' alt='$file'>
              </a>
              <div class='gallery_edit'>
                <form action='gallery.php' method='post'>
                  <button class='delete' type='submit' name='delete_img' value='" . $image['id'] . "'>DELETE</button>
                </form>
              </div>";
      } else {
        echo "<a target='_blank' href='/uploads/images/$file'>
                <img class = 'notadmin' src='uploads/images/$file' alt='$file'>
              </a>";
      }
    echo "</div>";
  }
}

function exec_sql_query($db, $sql, $params = array()) {
  $query = $db->prepare($sql);
  if ($query and $query->execute($params)) {
    return $query;
  }
  return NULL;
}

// YOU MAY COPY & PASTE THIS FUNCTION WITHOUT ATTRIBUTION.
// open connection to database
function open_or_init_sqlite_db($db_filename, $init_sql_filename) {
  if (!file_exists($db_filename)) {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_init_sql = file_get_contents($init_sql_filename);
    if ($db_init_sql) {
      try {
        $result = $db->exec($db_init_sql);
        if ($result) {
          return $db;
        }
      } catch (PDOException $exception) {
        // If we had an error, then the DB did not initialize properly,
        // so let's delete it!
        unlink($db_filename);
        throw $exception;
      }
    }
  } else {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
  }
  return NULL;
}

// Log in user
function login($username, $password) {
  global $db;
  global $messages;
  if ($username && $password) {
    $sql = "SELECT * FROM users WHERE username = :uname";
    $params = array(':uname' => $username);
    $records = exec_sql_query($db, $sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    if ($records) {
      $user = $records[0];
      if (password_verify($password, $user['password'])) {
        // generate new session
        session_regenerate_id();
        $_SESSION['current_user'] = $username;
        return $username;
      }
      else {
        record_message("Incorrect username or password.");
      }
    }
    else {
      record_message("Invalid username or password.");
    }
  }
  else {
    record_message("Please enter a username and password.");
  }
  return NULL;
}

// Log out user
function logout() {
  global $current_user;
  $current_user = NULL;

  unset($_SESSION['current_user']);
  session_destroy();
}

// Return currently logged in user
function current_user() {
  if (isset($_SESSION['current_user'])) {
    return $_SESSION['current_user'];
  }
  return NULL;
}

// open connection to database
$db = open_or_init_sqlite_db("website.sqlite", "init/init.sql");

session_start();
if (isset($_POST['login'])) {
  $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
  $username = trim($username);
  $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
  $current_user = login($username, $password);
} else {
  $current_user = current_user();
}

if (isset($_POST['logout'])) {
  logout();
  if ($current_user = NULL) {
    record_message("Successfully logged out");
  }
  if (basename($_SERVER['PHP_SELF'], ".php") == 'members') {
    header("Location: login.php");
    exit;
  }
}

// Only show members page in nav bar to logged in admin
if ($current_user == "admin") {
  $pages['members'] = "MEMBERS";
}


?>
