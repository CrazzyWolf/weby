<?php

require_once DIRECTORY_CONTROLLERS . "DatabaseConnection.class.php";

class ProfileController extends DatabaseConnection
{
	public function __construct(array $data)
	{
		parent::__construct();
		$this->data = $data;

		if(isset($_POST['submit']))
		{
			if(empty($_POST['email']))
			{
				$this->data["error"] = "Email can't be empty";
			}
			else if(empty($_POST['first_name']))
			{
				$this->data["error"] = "First name can't be empty";
			}
			else if(empty($_POST['last_name']))
			{
				$this->data["error"] = "Last name can't be empty";
			}
			else if(empty($_POST['password']))
			{
				$this->data["error"] = "Password can't be empty";
			}
			else if($_POST['password'] != $_POST['repeatPassword'])
			{
				$this->data["error"] = "Passwords doesn't match";
			}
			else
			{
				$this->updateProfile($_POST['email'], $_POST['first_name'],
				                         $_POST['last_name'], $_POST['password']);
			}
		}

		$this->data["email"] = $_SESSION['email'];
		$this->data["first_name"] = $_SESSION['first_name'];
		$this->data["last_name"] = $_SESSION['last_name'];

		MyApplication::renderInTwig("profile.twig", $this->data);
	}

	public function updateProfile($email, $first_name, $last_name, $password)
	{
		$result = $this->dbUsers->updateProfile(Sessions::getID(), $email,
		                                   $first_name, $last_name, $password);
		if(is_bool($result))
		{
			if($result)
			{
				$this->data["success"] = "Profile was updated";
			}
			else
			{
				$this->data["error"] = "Error while trying to login";
			}
		}
		else
		{
			$this->data["error"] = $result;
		}
	}
}