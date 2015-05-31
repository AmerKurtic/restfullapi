<?php

abstract class API
{
	protected $method = '';
	protected $endpoint = '';
	protected $verb = '';
	protected $args = Array();
	protected $file = Null;
	protected $serverIP = "localhost";
    protected $username = "root";
    protected $password = "";
    protected $db = "api";
    protected $con;
    public $json;

	public function __CONSTRUCT($request)
	{
		header("Content-Type: application/json");

		$this->con = new mysqli($this->serverIP,$this->username,$this->password,$this->db);
		if($this->con->connect_error)
		{
			die("Connection failed: ". $this->con->connect_error);
		}

		$this->args = explode('/', rtrim($request, '/'));
		$this->endpoint = array_shift($this->args);

				$this->request = $this->_cleanInputs($_POST);
		if(array_key_exists(0, $this->args) && !is_numeric($this->args[0]))
		{
			$this->verb = array_shift($this->args);
		}

		$this->method = $_SERVER['REQUEST_METHOD'];

		if($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER))
		{
			if($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE')
			{
				$this->method = 'DELETE';
			}
			else if($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT')
			{
				$this->method = 'PUT';
			}
			else
			{
				throw new Exception("Invalid Header");
			}
		}

		switch($this->method)
		{
			case 'DELETE':
				$this->file = file_get_contents("php://input");
				$this->request = $this->_cleanInputs($_GET);
				$this->_deleteHandler();
				break;
			case 'POST':

				$this->file = file_get_contents("php://input");
				$this->request = $this->_cleanInputs($_GET);
				$this->_postHandler();
				break;
			case 'PUT':
				$this->request = $this->_cleanInputs($_GET);
				$this->file = file_get_contents("php://input");
				$this->_putHandler();
				break;
			case 'GET':
				$this->request = $this->_cleanInputs($_GET);
				$this->_getHandler();
				break;
			default:
				$this->response('Invalid Method',405);	
				break;
		}
	}


	private function _postHandler()
	{
		if($this->verb == "domains")
		{
			if(array_key_exists(0, $this->args) && !is_numeric($this->args[0]))
			{
				$action = array_shift($this->args);
				if($action == "create")
				{
					if($stmt = $this->con->prepare("SELECT `uID` FROM `apikeys` WHERE `APIKEY` = ?"))
					{
						$stmt->bind_param("s",$this->request['apiKey']);
						$stmt->execute();
						$stmt->bind_result($apikey);
						$stmt->store_result();
						if($stmt->num_rows > 0)
						{
							$stmt->fetch();

						}
						$domain = json_decode($this->file);
						if($stmt = $this->con->prepare("INSERT INTO `domain` (`domain`,`uID`,`startDate`,`contractTerm`,`montlyPrice`) values (?,?,?,?,?)"))
						{
							$stmt->bind_param("sssss",$domain->domain,$apikey,$domain->startDate,$domain->contractTerm,$domain->montlyPrice);
							$stmt->execute();
						}
						else
						{
							throw new Exception("Failed to create domain", 1);	
						}
					}
					
				}
				else
				{
					throw new Exception("No action given", 1);
					
				}
			}
				
		}
		else
		{
			throw new Exception("Error Processing Request", 1);
		}
	}

	private function _getHandler()
	{
		if($this->verb == "domains")
		{
			if(array_key_exists(0, $this->args) && !is_numeric($this->args[0]))
			{
				$domain = array_shift($this->args);
				if($stmt = $this->con->prepare("SELECT `domain`,`startDate`,`contractTerm`,`montlyPrice` FROM `domain` WHERE `domain` = ?"))
				{
					$stmt->bind_param("s",$domain);
					$stmt->execute();
					$stmt->bind_result($domain,$startDate,$contractTerm,$montlyPrice);
					$stmt->store_result();
					if($stmt->num_rows > 0)
					{
						$stmt->fetch();
						$domain = array("domain"=>$domain,"startDate"=>$startDate,"contractTerm"=>$contractTerm,"montlyPrice"=>$montlyPrice);
						$domains = array("domain" => $domain);
						$this->json = json_encode($domains);
					}
					else
					{
						$this->_response(404);
						throw new Exception("Domain not found", 1);	
					}
				}
			}
			elseif($stmt = $this->con->prepare("SELECT `domain`,`startDate`,`contractTerm`,`montlyPrice` FROM `domain`JOIN `apikeys` ON domain.uID=apikeys.uID WHERE apikeys.APIKEY = ?"))
			{
				$stmt->bind_param("s",$this->request['apiKey']);
				$stmt->execute();
				$stmt->bind_result($domain,$startDate,$contractTerm,$montlyPrice);
				$stmt->store_result();
				if($stmt->num_rows > 0)
				{
					$domains = array();
					while($stmt->fetch())
					{
						$domain = array("domain"=>$domain,"startDate"=>$startDate,"contractTerm"=>$contractTerm,"montlyPrice"=>$montlyPrice);
						$domains[] = array("domain" => $domain);
					}
					$this->json = json_encode($domains);
				}
			}
		}
		else
		{
			throw new Exception("Error Processing Request", 1);
		}
	}

	private function _putHandler()
	{
		if($this->verb == "domains")
		{
			if(array_key_exists(0, $this->args) && !is_numeric($this->args[0]))
			{
				$domain = array_shift($this->args);
				$update = json_decode($this->file, true);
				$update = $this->_cleanInputs($update);
				$sql = "UPDATE `domain` SET";
				$len = count($update);
				$i = 1;
				
				foreach ($update as $key => $value) 
				{
					$sql .= "`".$key."` = '".$value ."' ";	
					if($i != $len)
					{
						$sql .= ", ";
					}
					$i++;
				}
				$sql .= "WHERE `domain` = '$domain'";
				if(!$this->con->query($sql))
				{
					throw new Exception("Error Processing Request", 1);
				}

				if($stmt = $this->con->prepare("SELECT `domain`,`startDate`,`contractTerm`,`montlyPrice` FROM `domain` WHERE `domain` = ?"))
				{
					$stmt->bind_param("s",$domain);
					$stmt->execute();
					$stmt->bind_result($domain,$startDate,$contractTerm,$montlyPrice);
					$stmt->store_result();
					if($stmt->num_rows > 0)
					{
						$stmt->fetch();
						$domain = array("domain"=>$domain,"startDate"=>$startDate,"contractTerm"=>$contractTerm,"montlyPrice"=>$montlyPrice);
						$domains = array("domain" => $domain);
						$this->json = json_encode($domains);
					}
					else
					{	$this->_response(404);
						throw new Exception("Domain not found", 1);
					}
				}

			}
			else
			{
				$this->_response(404);
				throw new Exception("No domain provided", 1);	
			}
		}
		else
		{
			throw new Exception("Error Processing Request", 1);
		}
	}

	public function _cleanInputs($data)
	{
		$clean_input = Array();
		if(is_array($data))
		{
			foreach($data as $key => $value)
			{
				$clean_input[$key] = $this->_cleanInputs($value);
			}
		}
		else
		{
			$clean_input = trim(strip_tags($data));
		}
		return $clean_input;
	}

	public function proccessAPI()
	{
		if (method_exists($this, $this->endpoint))
		{
			return $this->_response($this->{$this->endpoint}($this->args));
		}
		return $this->_response("No Endpoint: $this->endpoint", 404);
	}

	private function _response($status = 200)
	{
		header("HTTP/1.1" . $status);
	}

	private function _requestStatus($code)
	{
		$status = array
		(
			200 => 'OK',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			500 => 'Internal Server Error',
		);
		return($status[$code])?$status[$code]:$status[500];
	}

	public function __DESTRUCT()
	{
		$this->con->close();
	}

}

?>