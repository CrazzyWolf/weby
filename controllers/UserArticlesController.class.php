<?php

class UserArticlesController extends DatabaseConnection
{
	public function __construct(array $data)
	{
		parent::__construct();

		if(isset($_GET["remove"]) && $this->dbArticles->isArticleOwnedBy(Sessions::getID(), $_GET["remove"]))
		{
			$this->dbArticles->deleteArticle($_GET["remove"]);
			Sessions::redirect("index.php?web=userArticles");
		}

		$this->data = $data;

		$this->data["articlesTable"] = $this->dbUsers->getUserArticles(Sessions::getID());

		foreach($this->data["articlesTable"] as $key => $article)
		{
			$author = $this->dbUsers->getUser($article["author_id"]);
			$this->data["articlesTable"][$key]["author"] = $author["first_name"] . " " . $author["last_name"];
		}

		parent::overallRating();

		MyApplication::renderInTwig("userArticles.twig", $this->data);
	}
}