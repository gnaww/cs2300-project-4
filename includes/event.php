
<?php

class Event {
  //no id field bc will change dynamically in database!
  private $title = NULL;
  private $date = NULL; //DateTime object
  private $location = NULL;
  private $description = ''; //optional
  private $recipe = ''; //optional
  private $id = NULL;

  /* Create a new event */
  function __construct($id_input, $title_input, $date_input, $start_time_input, $location_input, $description_input=NULL, $recipe_input=NULL) {
    //initialize parameters
    //format reference: http://php.net/manual/en/function.date.php
    $dateObject = DateTime::createFromFormat('m-d-Y h:i A', $date_input.' '.$start_time_input);
    $this->id = $id_input;
    $errors = DateTime::getLastErrors();
    $this->date = $dateObject;
    $this->title = $title_input;
    $this->location = $location_input;
    if ($description_input) {
      $this->description = $description_input;
    }
    if ($recipe_input) {
      $this-> recipe = $recipe_input;
    }




  }

  /* Getter/setter code from: https://stackoverflow.com/questions/4478661/getter-and-setter */

  /* Get value of a property*/
  public function __get($property) {
    if (property_exists($this, $property)) {
      return $this->property;
    }
  }

  /* Set value of a property */
  public function __set($property, $value) {
    if (property_exists($this, $property)) {
      $this->property = $value;
    }

    return $this;
  }

  /*Return a nice string representation of the date/time */
  public function getDateString() {
    $dateObject = $this->date;
    //format reference: http://php.net/manual/en/function.date.php
    // Want: Hour:Minute AM Month Day, Year
    $prettyDate = $dateObject->format('F j, g:i A');
    return $prettyDate;
  }

  public function getDateObject() {
    return $this->date;
  }

  public function getID() {
    return $this->id;
  }

  public function getDateAsMS() {
    //NOTE: get Date Object in terms of ms since 1970 - allows comparison
    //via PHP library array functions. Lower is earlier.
    $date = $this->date->format('U');
    return $date;
  }

  /*Returns boolean representing whether the event is occuring in the future or the past */
  public function isEventUpcoming() {
    $current_time = new DateTime('NOW'); //get current time and date
    //TODO: fix -> use getDateAsMS
    $current_time_MS = $current_time->format('U'); //MS since 1970
    $event_time_MS = $this->getDateAsMS();

    $interval = $event_time_MS - $current_time_MS;

    if ($interval >=0) {
      //time of event is in future
      return TRUE;
    } else {
      //time of event has passed
      return FALSE;
    }
  }

  public function displayEvent() {

    //set variables using event object
    $title = $this->title;
    $description = $this->description;
    $recipe = $this->recipe;
    $location = $this->location;
    $dateString = $this->getDateString();


        echo "<h3>".$title."</h3>";
        echo "<h3 class='event_time'>".$dateString."</h3>";

        //description and recipe could be null
        if ($description) {
          echo "<p>".$description."</p>";
        }

        if ($recipe) {
          echo "<p><a href=".$recipe.">Recipe </a></p>";
        }

        echo "<h4>Location: ".$location." </h4>";


  }
}


?>
