<?php
#Modified from original file: https://gist.github.com/pamelafox-coursera/5359246
class CalendarEvent {
    /**
     *
     * The event ID
     * @var string
     */
    private $uid;
    /**
     * The event start date
     * @var DateTime
     */
    private $start;
    /**
     * The event end date
     * @var DateTime
     */
    private $end;
    /**
     *
     * The event title
     * @var string
     */
    private $summary;
    /**
     * The event description
     * @var string
     */
    private $description;
    /**
     * The event location
     * @var string
     */
    private $location;


	public static function createCalendarEvent($startDateTime, $endDateTime, $summary, $description, $location) {
		$parameter = array(
		  'start' => $startDateTime,
		  'end' => $endDateTime,
		  'summary' => $summary,
          'description' => $description,
          'location' => $location
        );
		return new CalendarEvent($parameter);
    }
    public function __construct($parameters) {
        $parameters += array(
          'summary' => 'Untitled Event',
          'description' => '',
          'location' => ''
        );
        if (isset($parameters['uid'])) {
            $this->uid = $parameters['uid'];
        } else {
            $this->uid = uniqid(rand(0, getmypid()));
        }
        $this->start = $parameters['start'];
        $this->end = $parameters['end'];
        $this->summary = $parameters['summary'];
        $this->description = $parameters['description'];
        $this->location = $parameters['location'];
      return $this;
    }
    /**
     * Get the start time set for the even
     * @return string
     */
    private function formatDate($date) {
        return $date->format("Ymd\THis");
    }
    /* Escape commas, semi-colons, backslashes.
       http://stackoverflow.com/questions/1590368/should-a-colon-character-be-escaped-in-text-values-in-icalendar-rfc2445
     */
    private function formatValue($str) {
        return addcslashes($str, ",\\;");
    }
    public function generateString() {
        $created = new DateTime();
        $content = '';
        $content = "BEGIN:VEVENT\r\n"
                 . "UID:{$this->uid}\r\n"
                 . "DTSTART:{$this->formatDate($this->start)}\r\n"
                 . "DTEND:{$this->formatDate($this->end)}\r\n"
                 . "DTSTAMP:{$this->formatDate($this->start)}\r\n"
                 . "CREATED:{$this->formatDate($created)}\r\n"
                 . "DESCRIPTION:{$this->formatValue($this->description)}\r\n"
                 . "LAST-MODIFIED:{$this->formatDate($this->start)}\r\n"
                 . "LOCATION:{$this->location}\r\n"
                 . "SUMMARY:{$this->formatValue($this->summary)}\r\n"
                 . "SEQUENCE:0\r\n"
                 . "STATUS:CONFIRMED\r\n"
                 . "TRANSP:OPAQUE\r\n"
                 . "END:VEVENT\r\n";
        return $content;
    }
}
?>
