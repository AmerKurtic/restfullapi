<?php

class APIKEY
{
	protected $serverIP = "localhost";
    protected $username = "root";
    protected $password = "";
    protected $db = "api";
    protected $con;
    public $uID;

	public function __CONSTRUCT()
	{
		$this->con = new mysqli($this->serverIP,$this->username,$this->password,$this->db);
			
		if($this->con->connect_error)
		{
			die("Connection failed: ". $this->con->connect_error);
		}
	}

	public function verifyKey($key,$origin)
	{
		if($stmt = $this->con->prepare("SELECT * FROM `APIKEYS` WHERE `APIKEY`=? AND `ORIGIN`=?"))
		{
			$stmt->bind_param("ss",$key,$origin);
			$stmt->execute();
			$stmt->bind_result($apikey,$origin,$uID);
			$stmt->store_result();
			if($stmt->num_rows > 0)
			{
				$this->uID = $uID;
				return true;
			}
			return false;
		}
		
	}

	public function __DESTRUCT()
	{
		$this->con->close();
	}
}

?>