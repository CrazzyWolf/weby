<?php

class ReviewsController extends DatabaseConnection
{
	public function __construct(array $data)
	{
		if(isset($_GET["article"]) == false)
		{
			Sessions::redirect("index.php");
		}

		parent::__construct();

		$this->data = $data;

		$this->data["reviewsTable"] = $this->db->getReviews($_GET["article"]);

		foreach (range(1, 3) as $number)
		{
			$stars = array();
			$ratings = array();

			foreach($this->data["reviewsTable"] as $review)
			{
				$rating = $review["rating_" . $number];
				if($rating != 0)
				{
					$id = $review["article_id"];
					if(empty($ratings[$id]))
					{
						$stars[$id] = 0;
						$ratings[$id] = 0;
					}
					$stars[$id] += $rating;
					$ratings[$id]++;
				}
			}

			foreach($this->data["reviewsTable"] as $key => $review)
			{
				$id = $review["article_id"];
				if(isset($stars[$id]))
				{
					$this->data["rating_" . $number] = $stars[$id] / $ratings[$id];
				}
			}
		}


		foreach($this->data["reviewsTable"] as $index => $review)
		{
			$user = $this->db->getUser($review["reviewer_id"]);
			$this->data["reviewsTable"][$index]["reviewer_name"] = $user["first_name"] . " " . $user["last_name"];
		}

		MyApplication::renderInTwig("showReviews.twig", $this->data);
	}
}