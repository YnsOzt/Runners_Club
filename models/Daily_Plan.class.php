<?php

class Daily_Plan{

	private $_plan_id;
  private $_description;
  private $_date;

	public function __construct($_plan_id, $_description,$_date){
    $this->_plan_id = $_plan_id;
    $this->_description = $_description;
    $this->$_date = $_date;

		preg_match('/^(.*)\-(.*)-(.*)$/', $_date,$result);
		$this->_date = $result[3]."/".$result[2]."/".$result[1];

	}

  public function get_plan_id(){
    return $this->_plan_id;
  }

  public function get_description(){
    return $this->_description;
  }

  public function get_date(){
    return $this->_date;
  }

}

?>
