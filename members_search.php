<?php
include_once("includes/init.php");
$valid_search = FALSE;

if (isset($_GET['search_field']) && isset($_GET['search_input'])) {
  $search_input = trim(filter_input(INPUT_GET, 'search_input', FILTER_SANITIZE_STRING));
  $search_field = filter_input(INPUT_GET, 'search_field', FILTER_SANITIZE_STRING);
  $valid_search = TRUE;

  if (strlen($search_input) == 0) { // Make sure nonempty search query
    $valid_search = FALSE;
    array_push($messages, "Please enter valid search parameters.");
  }
  else if (!in_array($search_field, array("name", "netid"))) { // Validate search categories
    $valid_search = FALSE;
    array_push($messages, "Please enter valid search parameters.");
  }
}

if ($valid_search) {
  echo "<h1>Search Results</h1>";
  if ($search_field == "netid") {
    $sql = "SELECT * FROM members WHERE netid LIKE '%' || :id || '%' ORDER BY last_name";
    $params = array(":id" => $search_input);
  }
  else if ($search_field == "name") {
    $sql = "SELECT * FROM members WHERE first_name LIKE '%' || :name || '%' OR last_name LIKE '%' || :name || '%' ORDER BY last_name";
    $params = array(":name" => $search_input);
  }
  $records = exec_sql_query($db, $sql, $params)->fetchAll(PDO::FETCH_ASSOC);

  // Print results
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
    echo "<h2>No search results.</h2>";
  }
}
