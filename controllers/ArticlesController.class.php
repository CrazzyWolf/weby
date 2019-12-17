<?php

class ArticlesController extends DatabaseConnection
{
	public function __construct(array $data)
	{
		parent::__construct();
		$this->data = $data;

		$this->data["articlesTable"] = $this->dbArticles->getAllPublishedArticles();

		parent::overallRating();

		MyApplication::renderInTwig("test.twig", $this->data);
	}
}