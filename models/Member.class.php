<?php
class Member{
	private $_member_id;
	private $_last_name;
	private $_first_name;
	private $_mail;
	private $_picture;
	private $_password;
	private $_adress;
	private $_phone_number;
	private $_account_number;
	private $_reponsability;
	private $_accepted; //if the member is accepted by an admin 1 else 0
	private $_contributed; //if the member had paid his contribution 1 else 0

	public function __construct($_member_id, $_last_name, $_first_name, $_mail, $_picture, $_password, $_adress, $_phone_number, $_account_number, $_reponsability, $_accepted, $_contributed){
		$this->_member_id = $_member_id;
		$this->_last_name = $_last_name;
		$this->_first_name = $_first_name;
		$this->_mail = $_mail;
		$this->_picture = $_picture;
		$this->_password = $_password;
		$this->_adress = $_adress;
		$this->_phone_number = $_phone_number;
		$this->_account_number = $_account_number;
		$this->_reponsability = $_reponsability;
		$this->_accepted = $_accepted;
		$this->_contributed = $_contributed;
	}

	public function get_member_id(){
		return $this->_member_id;
	}

	public function get_last_name(){
		return $this->_last_name;
	}

	public function get_first_name(){
		return $this->_first_name;
	}

	public function get_mail(){
		return $this->_mail;
	}

	public function get_picture(){
		return $this->_picture;
	}

	public function get_password(){
		return $this->_password;
	}

	public function get_adress(){
		return $this->_adress;
	}

	public function get_phone_number(){
		return $this->_phone_number;
	}

	public function get_account_number(){
		return $this->_account_number;
	}

	public function get_responsability(){
		return $this->_reponsability;
	}

	public function get_accepted(){
		return $this->_accepted;
	}

	public function get_contributed(){
		return $this->_contributed;
	}

}

?>
