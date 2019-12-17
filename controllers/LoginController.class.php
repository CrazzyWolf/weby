<?php

require_once DIRECTORY_CONTROLLERS . "DatabaseConnection.class.php";

class LoginController extends DatabaseConnection
{
	public function __construct(array $data)
	{
		parent::__construct();
		$this->data = $data;

		if(isset($_POST['submit']))
		{
			$this->data["email"] = $_POST["email"];
			if(empty($_POST['email']))
			{
				$this->data["error"] = "Email can't be empty";
			}
			else if(empty($_POST['password']))
			{
				$this->data["error"] = "Password can't be empty";
			}
			else
			{
				$this->verifyAndLogin($_POST['email'], $_POST['password']);
			}
		}

		MyApplication::renderInTwig("login.twig", $this->data);
	}

	public function verifyAndLogin($email, $password)
	{
		if($this->db->login($email, $password))
		{
			Sessions::redirect("index.php");
		}
		else
		{
			$this->data["error"] = "Wrong username or password";
		}
	}
}