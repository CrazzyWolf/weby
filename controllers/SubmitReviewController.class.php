<?php

require_once DIRECTORY_CONTROLLERS . "DatabaseConnection.class.php";

class SubmitReviewController extends DatabaseConnection
{
	public function __construct(array $data)
	{
		parent::__construct();
		$this->data = $data;

		if(isset($_GET["article"]))
		{
			$this->data["article"] = $this->db->getAllArticleByID($_GET["article"]);
		}
		if(isset($this->data["article"]) == false || $this->data["article"] == false)
		{
			$this->data["error"] = "Article not found";
		}
		else if(isset($_POST['submit']))
		{
			$error = true;
			if(empty($_GET['article']))
			{
				$this->data["error"] = "Article was not selected";
			}
			else if(empty($_POST['text']))
			{
				$this->data["error"] = "Review can't be empty";
			}
			else
			{
				if(strlen($_POST["text"]) > 65535)
				{
					$this->data["error"] = "Review is too long";
				}
				else
				{
					$error = false;
					$this->db->sendReview($_POST["text"], Sessions::getID(), $_GET["article"],
					                      $_POST["rating_1"], $_POST["rating_2"], $_POST["rating_3"]);
				}
			}
			if($error)
			{
				$this->data["text"] = $_POST["text"];
			}
			else
			{
				Sessions::redirect("index.php?successReview");
			}
		}

		MyApplication::renderInTwig("submitReview.twig", $this->data);
	}
}