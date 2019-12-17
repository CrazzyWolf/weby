<?php

class ReviewController extends DatabaseConnection
{
	public function __construct(array $data)
	{
		parent::__construct();

		if(isset($_GET["remove"]))
		{
			$this->db->deleteArticle($_GET["remove"]);
			Sessions::redirect("index.php?web=review");
		}
		else if(isset($_GET["publish"]))
		{
			$this->db->publishArticle($_GET["publish"], true);
			Sessions::redirect("index.php?web=review");
		}
		else if(isset($_GET["unpublish"]))
		{
			$this->db->publishArticle($_GET["unpublish"], false);
			Sessions::redirect("index.php?web=review");
		}

		$this->data = $data;
		$this->data["articlesTable"] = $this->db->getAllArticles();

		parent::overallRating();

		MyApplication::renderInTwig("review.twig", $this->data);
	}
}