<?php

/*Class used to maintain arrays of events sorted by date
Should only be one instance at a time.
*/
class EventManager {

  private $MAX_EVENTS = 10; //maximum number of events to display in a section.

  //define associative arrays to hold events
  private $futureEvents = array();
  private $pastEvents = array();
  private $allEvents = array(); //holds all events, for reference.

  private $date = NULL; //holds DateTime of page load

  public function __construct($eventsArray) {
    $temp_eventsArray = $eventsArray;
    $temp_futureArray = array();
    $temp_pastArray = array();
    foreach ($eventsArray as $event) {

      $date = $event->getDateAsMS();
      $temp_eventsArray[$date]=$event; //index is date number.

      //sort events by whether they've occured
      if ($event->isEventUpcoming() === TRUE) {
        //future
        $temp_futureArray[] = $event;
      }
      else {
        //past
        $temp_pastArray[] = $event;
      }
    }

    //update class parameters
    $this->allEvents = $temp_eventsArray;
    $this->pastEvents = $temp_pastArray;
    $this->futureEvents = $temp_futureArray;

    $this->sortEvents(); //split into future/past events.

  }

  //TODO: implement!
  private function sortEvents() {
    //NOTE: ksort: low to high. krsort: high to low. Based on keys.
    $past = $this->pastEvents;
    $future = $this->futureEvents;

    //past event sorting:
    //want to sort past events most recent first then older (big to small)
    krsort($past);

    //future event sorting:
    //want to sort future events closest first then futher away (small to big)
    ksort($future);
  }

  public function getPastEvents() {
    return $this->pastEvents;
  }

  public function getFutureEvents() {
    return $this->futureEvents;
  }



}
?>
