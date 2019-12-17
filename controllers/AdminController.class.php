<?php

require_once DIRECTORY_CONTROLLERS . "DatabaseConnection.class.php";

class AdminController extends DatabaseConnection
{
	public function __construct(array $data)
	{
		parent::__construct();
		$this->data = $data;

		if(isset($_POST['submit']))
		{
			$this->data["saved"] = true;
			foreach($_POST as $id => $rights)
			{
				if($rights != "")
				{
					$this->db->setRights($id, $rights);
					if($id == $_SESSION["id"])
					{
						$_SESSION["rights"] = $rights;
					}
				}
			}
		}
		else if(isset($_GET["remove"]))
		{
			$this->data["deleted"] = true;
			$this->db->deleteUser($_GET["remove"]);
		}

		$this->data["adminTable"] = $this->db->getAllUsers();

		MyApplication::renderInTwig("admin.twig", $this->data);
	}
}