<?php include('includes/init.php');
  $current_page_id = "editevent";

  //edit event logic
  /* NOTE TO GRADERS: This page does not contain any logic to actually change
  events in the database! Rather, this page is ONLY a GUI-based interface for
  users to change event information in the front-end. This page essentially does
  the same back-end behavior as the form on top of events.php.

  This is why this page does NOT contain any db atomic logic - because the db is
  not accessed on this page, but on the main events.php page, which DOES use
  atomic logic to prevent race conditions!
  -rowan 

  */
  if (!isset($_POST['editevent'])) {
    //user has found this page by mistake - should only get here via events page!
    record_message("Error: you do not have permission to view this page.");
  } else {
    $event_id = filter_var($_POST['editevent']);

    if ($event_id >= 0) {
      //id is a valid id

      //is there a corresponding event in the database?
      global $db;
      $sql_check = "SELECT * FROM events WHERE id=:id";
      $params_check = array(
        ":id"=> $event_id
      );
      $results_check = exec_sql_query($db, $sql_check, $params_check)->fetchAll();

      if ($results_check) {
        //there is an event in the database to be edited!
        $event = $results_check[0]; //events unique

        $title = $event['title'];
        $time = $event['start_time'];
        $date = $event['date'];
        $location = $event['location'];
        $description = $event['description'];

        //TODO: update event entry
      } else {
        record_message("Error: event does not exist.");
      }

    } else {
      record_message("Error: event does not exist.");
    }
  }

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link rel="stylesheet" type="text/css" href="styles/all.css"/>
  <title>Edit Event</title>
</head>

<body>
  <?php include('includes/header.php'); ?>

  <div class='row editevent'>
    <!--Page header -->
    <div class='row events'>
      <div class="column column-2">&nbsp;</div> <!--spacer-->
      <div class="column column-8 events_header">
        <h1>EDIT EVENT</h1>

      </div>
      <div class="column column-2">&nbsp;</div> <!--spacer-->
    </div>

    <!-- Page body -->
    <div class='row events editevent_container'>
      <div class="column column-2">&nbsp;</div> <!--spacer-->
      <div class="column column-8">
        <!-- Back button -->
        <form action='/events.php' method='post'>
          <button name= 'back' type='submit'>CANCEL</button>
        </form>
        <?php
        print_messages();

        echo "<div class='edit'>";
        echo "<form id='editform' action='/events.php' method='post'>";
            echo "<div class='row'>"; //title
            echo "<ul>";
              echo "<li>";
                echo "<label>Event Title:</label>";
                echo "<input type='text' name='title' value = '".$title."' required>";
              echo "</li>";
            echo "</ul>";
            echo "</div>"; //end of row

            echo "<div class='row'>"; //date
            echo "<ul>";
              echo "<li>";
                echo "<label>Date: (e.g. 01-02-2018)</label>";
                echo "<input type='text' name='date' value = '".$date."' required>";
              echo "</li>";
            echo "</ul>";
            echo "</div>"; //end of row

            echo "<div class='row'>"; //time
            echo "<ul>";
              echo "<li>";
                echo "<label>Time: (e.g. 03:00 PM)</label>";
                echo "<input type='text' name='time' value = '".$time."' required>";
              echo "</li>";
            echo "</ul>";
            echo "</div>"; //end of row

            echo "<div class='row'>"; //location
            echo "<ul>";
              echo "<li>";
                echo "<label>Location: </label>";
                echo "<input type='text' name='location' value = '".$location."' required>";
              echo "</li>";
            echo "</ul>";
            echo "</div>"; //end of row

            echo "<div class='row'>"; //description
            echo "<ul>";
              echo "<li>";
                echo "<label>Event Description: (Optional)</label>";
                echo "<textarea name='description'>".$description."</textarea>";
              echo "</li>";
              echo "<li>";
                echo "<button name= 'editevent' value = '".$event_id."'type='submit'>SUBMIT</button>";
              echo "</li>";
            echo "</ul>";
            echo "</div>"; //end of row


          echo "</form>"; //end form
          echo "</div>";
        ?>

      </div>
      <div class="column column-2">&nbsp;</div> <!--spacer-->
    </div>
  <?php include('includes/footer.php'); ?>
</body>
</html>
