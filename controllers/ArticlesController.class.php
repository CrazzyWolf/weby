<?php

class ArticlesController extends DatabaseConnection
{
	public function __construct(array $data)
	{
		parent::__construct();
		$this->data = $data;

		$this->data["articlesTable"] = $this->dbArticles->getAllPublishedArticles();

		foreach($this->data["articlesTable"] as $key => $article)
		{
			$author = $this->dbUsers->getUser($article["author_id"]);
			$this->data["articlesTable"][$key]["author"] = $author["first_name"] . " " . $author["last_name"];
		}

		parent::overallRating();

		MyApplication::renderInTwig("test.twig", $this->data);
	}
}