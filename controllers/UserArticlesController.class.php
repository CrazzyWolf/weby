<?php

class UserArticlesController extends DatabaseConnection
{
	public function __construct(array $data)
	{
		parent::__construct();

		if(isset($_GET["remove"]) && $this->db->isArticleOwnedBy(Sessions::getID(), $_GET["remove"]))
		{
			$this->db->deleteArticle($_GET["remove"]);
			Sessions::redirect("index.php?web=userArticles");
		}

		$this->data = $data;

		$this->data["articlesTable"] = $this->db->getUserArticles(Sessions::getID());

		parent::overallRating();

		MyApplication::renderInTwig("userArticles.twig", $this->data);
	}
}