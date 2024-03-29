<?php

class ReviewController extends DatabaseConnection
{
	public function __construct(array $data)
	{
		parent::__construct();

		if(Sessions::isAdmin())
		{
			if(isset($_GET["remove"]))
			{
				$this->dbArticles->deleteArticle($_GET["remove"]);
				Sessions::redirect("index.php?web=review");
			}
			else if(isset($_GET["publish"]))
			{
				$this->dbArticles->publishArticle($_GET["publish"], true);
				Sessions::redirect("index.php?web=review");
			}
			else if(isset($_GET["unpublish"]))
			{
				$this->dbArticles->publishArticle($_GET["unpublish"], false);
				Sessions::redirect("index.php?web=review");
			}
		}

		$this->data = $data;
		$this->data["articlesTable"] = $this->dbArticles->getAllArticles();

		foreach($this->data["articlesTable"] as $key => $article)
		{
			$author = $this->dbUsers->getUser($article["author_id"]);
			$this->data["articlesTable"][$key]["author"] = $author["first_name"] . " " . $author["last_name"];
		}

		parent::overallRating();

		MyApplication::renderInTwig("review.twig", $this->data);
	}
}