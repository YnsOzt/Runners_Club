<?php
#Modified from original file: https://gist.github.com/pamelafox-coursera/5359246

class Calendar {
    protected $events;
    protected $title;
    protected $author;
	/**
	*Default value is Europe/Brussels
	*/
	private $timeZone;

	public static function createCalendar($calendarEventsList, $title, $author, $timeZone='Europe/Brussels'){
		$parameter = array(
		  'events' => $calendarEventsList,
          'title' => $title,
          'author' => $author,
		  'timeZone' => $timeZone
        );
		return new Calendar($parameter);
	}
    public function __construct($parameters) {
        $parameters += array(
          'events' => array(),
          'title' => 'Calendar',
          'author' => 'Calender Generator',
		      'timeZone' => 'Europe/Brussels'
        );
        $this->events = $parameters['events'];
        $this->title  = $parameters['title'];
        $this->author = $parameters['author'];
		    $this->timeZone = $parameters['timeZone'];
    }
    /**
     *
     * Call this function to download the invite.
     */
    public function generateDownload() {
        $generated = $this->generateString();
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' ); //date in the past
        header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); //tell it we just updated
        header('Cache-Control: no-store, no-cache, must-revalidate' ); //force revaidation
        header('Cache-Control: post-check=0, pre-check=0', false );
        header('Pragma: no-cache' );
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: inline; filename="calendar.ics"');
        header("Content-Description: File Transfer");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . strlen($generated));
        print $generated;
    }
    /**
     *
     * The function generates the actual content of the ICS
     * file and returns it.
     *
     * @return string|bool
     */
    public function generateString() {
        $content = "BEGIN:VCALENDAR\r\n"
                 . "VERSION:2.0\r\n"
                 . "PRODID:-//" . $this->author . "//NONSGML//EN\r\n"
                 . "X-WR-CALNAME:" . $this->title . "\r\n"
                 . "X-WR-TIMEZONE:$this->timeZone" . "\r\n"
                 . "CALSCALE:GREGORIAN\r\n";
        foreach($this->events as $event) {
            $content .= $event->generateString();
        }
	    $content .= "END:VCALENDAR";
        return $content;
	}
}
?>
