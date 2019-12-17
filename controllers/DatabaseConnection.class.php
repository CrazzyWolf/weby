<?php

require_once DIRECTORY_MODELS . "Sessions.class.php";

abstract class DatabaseConnection
{
	protected $db;
	protected $data;

	protected function __construct()
	{
		require_once(DIRECTORY_MODELS . "Database.class.php");
		$this->db = new Database();
		$this->db->connectToDB();
	}

	protected function overallRating()
	{
		$reviews = $this->db->getAllReviews();

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