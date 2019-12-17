<?php

require_once DIRECTORY_CONTROLLERS . "DatabaseConnection.class.php";

class SubmitArticleController extends DatabaseConnection
{
	public function __construct(array $data)
	{
		parent::__construct();
		$this->data = $data;

		if(isset($_POST['submit']))
		{
			$error = true;
			if(empty($_POST['title']))
			{
				$this->data["error"] = "Title can't be empty";
			}
			else if(empty($_POST['text']))
			{
				$this->data["error"] = "Article can't be empty";
			}
			else
			{
				if(strlen($_POST["title"]) > 100)
				{
					$this->data["error"] = "Title is too long";
				}
				else if(strlen($_POST["text"]) > 65535)
				{
					$this->data["error"] = "Article is too long";
				}
				else
				{
					$error = false;
					$this->dbArticles->sendArticle($_POST['title'], $_POST["text"], Sessions::getID());
					$this->data["success"] = "Article has been sent";
				}
			}
			if($error)
			{
				$this->data["title"] = $_POST["title"];
				$this->data["text"] = $_POST["text"];
			}
		}

		MyApplication::renderInTwig("submitArticle.twig", $this->data);
	}
}