<?php

require_once('apikey.php');
require_once('api.php');

class MyBeesWeb extends API
{
	protected $User;

	public function __CONSTRUCT($request,$origin)
	{
		parent::__CONSTRUCT($request);

		$apiKey = new APIKEY();
		if(!array_key_exists('apiKey', $this->request))
		{
			throw new Exception("No API Key provided");
		}
		else if(!$apiKey->verifyKey($this->request['apiKey'],$origin))
		{
			throw new Exception("Invalid API Key");
		}

		echo $this->json;
	}
}

?>