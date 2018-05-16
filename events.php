<?php
include('includes/init.php');
include('includes/event.php');
include('includes/eventmanager.php');
$MAX_EVENTS = 10;
$current_page_id = "events";
global $db;

//User logic -------------------------------------------------------------------
if ($current_user == "admin") { //TODO: update to allow multiple admins
  $showEditMode = TRUE;
} else {
  $showEditMode = FALSE;
}

//TODO: add database transactions!!!!
//Add new event or edit logic --------------------------------------------------
if (isset($_POST['newevent']) || isset($_POST['editevent'])) {
  $db->beginTransaction();
  //user attempted to make new event OR user has just attempted to edit an event.

  //filter input
  $title_input = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
  $date_input = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
  $time_input = filter_var($_POST['time'], FILTER_SANITIZE_STRING);
  $location_input = filter_var($_POST['location'], FILTER_SANITIZE_STRING);
  $description_input = filter_var($_POST['description'], FILTER_SANITIZE_STRING);

  if (isset($_POST['editevent'])) {
    //contains the database id so that we can edit the same event as before.
    $event_id = filter_var($_POST['editevent'], FILTER_SANITIZE_STRING);
    if ($event_id <0 ) {
      record_message("Error: event you're trying to edit doesn't exist.");
      $event_id = NULL;
    }
  } else {
    $event_id = NULL;
  }
  //are all fields non-null? (description optional)
  if ($title_input &&
      $date_input &&
      $time_input &&
      $location_input) {

      //are all fields valid? this uses regex in an oblique way bc easier
      //Source: https://stackoverflow.com/questions/13746332/using-filter-var-to-verify-date?utm_medium=organic&utm_source=google_rich_qa&utm_campaign=google_rich_qa

      if (!DateTime::createFromFormat('m-d-Y', $date_input)){
        //date not formatted properly
        record_message("Error: date must be entered in format 01-15-2018.");
      }
      else if (!DateTime::createFromFormat('h:i A', $time_input)) {
        //time not formatted properly
        record_message("Error: time must be entered in format 01:30 PM.");

      } else {
        //date and time formatted properly!
        if (!$description_input) {
          $description_input = '';
        }
        //create new database entry
        global $db;
        if ((isset($_POST['newevent']))) {
          $sql_new = "INSERT INTO events (title, date, start_time, location, description) VALUES (:title, :date, :time, :location, :description);";
          $params_new = array(
            ":title" => $title_input,
            ":date" => $date_input,
            ":time"=> $time_input,
            ":location"=>$location_input,
            ":description"=>$description_input,

          );
        } else if (isset($_POST['editevent'])){
          $sql_new = "UPDATE events SET title=:title, date=:date, start_time=:time, location=:location, description=:description WHERE id=:id;";
          $params_new = array(
            ":title" => $title_input,
            ":date" => $date_input,
            ":time"=> $time_input,
            ":location"=>$location_input,
            ":description"=>$description_input,
            ":id"=>$event_id
          );
        }

        $results_new = exec_sql_query($db, $sql_new, $params_new);

        if (!$results_new) {
          record_message("Error: failed to create new event. Try again.");
          $db->rollBack(); //don't want to keep these changes.
        } else {
          if (isset($_POST['editevent'])) {
            //record message to user indicating event was successfully updated.
            record_message("Event successfully updated!");
          } else if (isset($_POST['newevent'])){
            //user added new event - indicate successful.
            record_message("Event successfully added!");
          }
        }
      }
  } else {
    record_message("Error: one or more required fields is empty.");
  }
  $db->commit(); //commit any changes that occured.
}


//TODO: add database transactions!!!
//Delete event logic -----------------------------------------------------------
if (isset($_POST['deleteevent'])) {

  $db->beginTransaction();
  //user wants to delete an event

  $id = filter_var($_POST['deleteevent'], FILTER_SANITIZE_STRING);

  if ($id >= 0) {
    //user submitted valid index

    //does this event exist in the database?
    global $db;
    $sql_delete_check = "SELECT * FROM events WHERE id=:id";
    $params_delete_check = array(
      ':id' => $id
    );
    $results_delete_check = exec_sql_query($db, $sql_delete_check, $params_delete_check)->fetchAll();
    if ($results_delete_check) {
      //event exists
      $sql_delete = "DELETE FROM events WHERE id=:id";
      $params_delete = array(
        ':id' => $id
      );
      $results_delete = exec_sql_query($db, $sql_delete, $params_delete);
      if (!$results_delete) {
        record_message("Error: event failed to be deleted. Try again.");
        $db->rollBack();
      } else {
        record_message("Event successfully deleted!");
      }

    } else {
      record_message("Error: event you're trying to delete does not exist.");
    }
  } else {
    record_message("Error: invalid event index.");
  }
  $db->commit(); //frees up space.
}


//Manage events ----------------------------------------------------------------
//get all events from database
$sql_events = "SELECT * FROM events";
$params_events = array();
global $db;
$results_events = exec_sql_query($db, $sql_events, $params_events)-> fetchAll();

$eventsList = array(); //holds all events from database

//Put database data into Event objects
foreach ($results_events as $event) {

  //note: description and recipe can be null! Avoiding null errors
  if ($event['description']) {
    $description = $event['description'];
  } else {$description = '';}

  if ($event['recipe']) {
    $recipe = $event['recipe'];
  } else {$recipe = '';}
  $event = new Event($event['id'],$event['title'], $event['date'], $event['start_time'], $event['location'], $description, $recipe);
  $eventsList[] = $event; //add to list of events
}

$manager = new EventManager($eventsList); //manages two arrays of events.


?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link rel="stylesheet" type="text/css" href="styles/all.css"/>
  <title>Events</title>
</head>

<body>
  <?php include('includes/header.php'); ?>

  <!--Page header -->
  <div class='row events'>
    <div class="column column-2">&nbsp;</div> <!--spacer-->
    <div class="column column-8 events_header">
      <h1>EVENTS</h1>
    </div>
    <div class="column column-2">&nbsp;</div> <!--spacer-->
  </div>

  <!-- Page contents -->
  <div class='row events'>

    <?php
      if ($showEditMode === TRUE) {
        echo "<div class='column column-2'>&nbsp;</div>";
        echo "<div class='column column-8 '>";
        //display any error messages
        print_messages();
          echo "<div class='row new_event_body'>"; //set up body div



            //TODO: make add event form here
            //Add new event form
            echo "<form action='/events.php' method='post'>";
                echo "<div class='row'>"; //title
                echo "<ul>";
                  echo "<li>";
                    echo "<label>Event Title:</label>";
                    echo "<input type='text' name='title' required>";
                  echo "</li>";
                echo "</ul>";
                echo "</div>"; //end of row

                echo "<div class='row'>"; //date
                echo "<ul>";
                  echo "<li>";
                    echo "<label>Date: (e.g. 01-02-2018)</label>";
                    echo "<input type='text' name='date' required>";
                  echo "</li>";
                echo "</ul>";
                echo "</div>"; //end of row

                echo "<div class='row'>"; //time
                echo "<ul>";
                  echo "<li>";
                    echo "<label>Time: (e.g. 03:00 PM)</label>";
                    echo "<input type='text' name='time' required>";
                  echo "</li>";
                echo "</ul>";
                echo "</div>"; //end of row

                echo "<div class='row'>"; //location
                echo "<ul>";
                  echo "<li>";
                    echo "<label>Location: </label>";
                    echo "<input type='text' name='location' required>";
                  echo "</li>";
                echo "</ul>";
                echo "</div>"; //end of row

                echo "<div class='row'>"; //description
                echo "<ul>";
                  echo "<li>";
                    echo "<label>Event Description: (Optional)</label>";
                    echo "<textarea name='description'></textarea>";
                  echo "</li>";
                  echo "<li>";
                    echo "<button id='neweventbutton' name= 'newevent' type='submit'>CREATE NEW EVENT</button>";
                  echo "</li>";
                echo "</ul>";
                echo "</div>"; //end of row


              echo "</form>"; //end form
          echo "</div>";
        echo "</div>";
        echo "<div class='column column-2'>&nbsp;</div>";
      }
    ?>

  </div>
  <div class='row events'>
    <div class="column column-2">&nbsp;</div> <!--spacer-->
    <div class="column column-8 ">

      <?php

        $past = $manager->getPastEvents();
        $future = $manager->getFutureEvents();
        echo "<div class='row events_body'>"; //set up div

        //future events
        echo "<h2>Upcoming Events</h2>";
        $output_future = array_slice($future, 0, $MAX_EVENTS); //get up to $MAX_EVENTS number events.
        foreach ($output_future as $event) {
          echo "<div class='row'>";
          echo "<div class='column column-1'>&nbsp;</div>";
          echo "<div class='events_detail column column-10'>";

          $event->displayEvent(); //display event info
          $event_id = $event->getID(); //allows us to update correct event.

          if ($showEditMode === TRUE) {
            //display form with EDIT/DELETE option

            //edit form
            echo "<form action='/editevent.php' method='post'>";
            //hidden inputs with event information.
            echo "<button name= 'editevent' value = '".$event_id."' type='submit'>EDIT</button>";
            echo "</form>";

            //delete form
            echo "<form action='/events.php' method='post'>";
            //hidden inputs with event information.
            echo "<button name= 'deleteevent' value = '".$event_id."' type='submit'>DELETE</button>";
            echo "</form>";
          }

          echo "</div>";
          echo "<div class='column column-1'>&nbsp;</div>";
          echo "</div>";
        }

        //past events
        echo "<h2>Past Events</h2>";
        $output_past = array_slice($past, 0, $MAX_EVENTS);
        foreach($output_past as $event) {
          echo "<div class='row'>";
          echo "<div class='column column-1'>&nbsp;</div>";
          echo "<div class='events_detail column column-10'>";

          $event->displayEvent(); //display event info
          $event_id = $event->getID(); //allows us to update correct event.

          if ($showEditMode === TRUE) {
            //display form with EDIT/DELETE option

            //edit form
            echo "<form action='/editevent.php' method='post'>";
            //hidden inputs with event information.
            echo "<button name= 'editevent' value = '".$event_id."' type='submit'>EDIT</button>";
            echo "</form>";

            //delete form
            echo "<form action='/events.php' method='post'>";
            //hidden inputs with event information.
            echo "<button name= 'deleteevent' value = '".$event_id."' type='submit'>DELETE</button>";
            echo "</form>";
          }

          echo "</div>";
          echo "<div class='column column-1'>&nbsp;</div>";
          echo "</div>";
        }

        //TODO: if this is edit mode, add EDIT and DELETE buttons
        //with hidden form where info is event info.

        echo "</div>";
      ?>

    </div>
    <div class="column column-2">&nbsp;</div> <!--spacer-->
  </div>

  <div class="row spacing"></div> <!--spacer to keep footer at bottom-->
  <?php include('includes/footer.php'); ?>
</body>
</html>
