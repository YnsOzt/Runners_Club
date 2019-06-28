<?php

class Training_Plan{

	private $_plan_id;
  private $_goal;
  private $_plan_name;
  private $_date_start;
  private $_date_end;

	public function __construct($_plan_id, $_goal, $_plan_name, $_date_start, $_date_end){
    $this->_plan_id = $_plan_id;
    $this->_goal = $_goal;
    $this->_plan_name = $_plan_name;

		preg_match('/^(.*)\-(.*)-(.*)$/', $_date_start,$result);
		$this->_date_start = $result[3]."/".$result[2]."/".$result[1];

		preg_match('/^(.*)\-(.*)-(.*)$/', $_date_end,$result2);
    $this->_date_end = $result2[3]."/".$result2[2]."/".$result2[1];
	}

  public function get_plan_id(){
    return $this->_plan_id;
  }

  public function get_goal(){
    return $this->_goal;
  }

  public function get_plan_name(){
    return $this->_plan_name;
  }

  public function get_date_start(){
    return $this->_date_start;
  }

  public function get_date_end(){
    return $this->_date_end;
  }

}

?>
