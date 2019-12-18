<?php

require_once DIRECTORY_MODELS . "Sessions.class.php";

abstract class DatabaseConnection
{
	protected $dbArticles;
	protected $dbReviews;
	protected $dbUsers;
	protected $data;

	private $pdo;

	protected function __construct()
	{
		require_once(DIRECTORY_MODELS . "DatabaseArticles.class.php");
		require_once(DIRECTORY_MODELS . "DatabaseReviews.class.php");
		require_once(DIRECTORY_MODELS . "DatabaseUsers.class.php");

		$this->connectToDB();
		$this->dbArticles = new DatabaseArticles($this->pdo);
		$this->dbReviews = new DatabaseReviews($this->pdo);
		$this->dbUsers = new DatabaseUsers($this->pdo);
	}

	private function connectToDB()
	{
		try
		{
			$this->pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
			//$this->pdo->exec("set names utf32");
			$this->pdo->exec("set names utf32_czech");
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		}
		catch(PDOException $e)
		{
			echo "Connection failed: " . $e->getMessage();
		}
	}


	protected function overallRating()
	{
		$reviews = $this->dbReviews->getAllReviews();

		$stars = array();
		$ratings = array();

		foreach($reviews as $review)
		{
			if($review["rating_3"] != 0)
			{
				$id = $review["article_id"];
				if(empty($stars[$id]))
				{
					$stars[$id] = 0;
				}
				if(empty($ratings[$id]))
				{
					$ratings[$id] = 0;
				}
				$stars[$id] += $review["rating_3"];
				$ratings[$id]++;
			}
		}

		foreach($this->data["articlesTable"] as $key => $article)
		{
			$id = $article["id"];
			if(isset($stars[$id]) && isset($ratings[$id]))
			{
				$this->data["articlesTable"][$key]["rating"] = $stars[$id] / $ratings[$id];
			}
		}
	}
}