<?php

class Event{

	private $_event_id;
	private $_date_start;
	private $_date_end;
	private $_event_name;
	private $_description;
	private $_location;
	private $_url;
	private $_cost;
	private $_lattitude;
	private $_longitude;
	private $_url_picture;

	public function __construct($_event_id, $_date_start, $_date_end, $_event_name, $_description, $_location, $_url, $_cost, $_latitude, $_longitude, $_url_picture){
		$this->_event_id = $_event_id;
		$this->_date_start = $_date_start;
		$this->_date_end = $_date_end;
		$this->_event_name = $_event_name;
		$this->_description = $_description;
		$this->_location = $_location;
		$this->_url = $_url;
		$this->_cost = $_cost;
		$this->_latitude = $_latitude;
		$this->_longitude = $_longitude;
		$this->_url_picture = $_url_picture;
	}

	public function get_event_id(){
		return $this->_event_id;
	}

	public function get_date_start(){
		return $this->_date_start;
	}

	public function get_date_end(){
		return $this->_date_end;
	}

	public function get_event_name(){
		return htmlspecialchars($this->_event_name);
	}

	public function get_description(){
		return $this->_description;
	}

	public function get_location(){
		return $this->_location;
	}

	public function get_url(){
		return $this->_url;
	}

	public function get_cost(){
		return $this->_cost;
	}

	public function get_latitude(){
		return $this->_latitude;
	}

	public function get_longitude(){
		return $this->_longitude;
	}

	public function get_url_picture(){
		return $this->_url_picture;
	}

}

?>
