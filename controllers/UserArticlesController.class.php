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

		parent::overallRating();

		MyApplication::renderInTwig("userArticles.twig", $this->data);
	}
}