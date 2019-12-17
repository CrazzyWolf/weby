<?php

class DatabaseReviews
{
	private $pdo;

	public function __construct($pdo)
	{
		$this->pdo = $pdo;
	}

	public function sendReview($text, $user_id, $article_id, $rating_1, $rating_2, $rating_3)
	{
		$this->removeReview($user_id, $article_id);
		$statement = $this->pdo->prepare("INSERT INTO reviews (reviewer_id, article_id, content, rating_1, rating_2, rating_3) 
											VALUES (:reviewer_id, :article_id, :content, :rating_1, :rating_2, :rating_3)");
		$statement->execute(array(':reviewer_id' => $user_id,
		                          ':article_id' => $article_id,
		                          'content' => $text,
		                          'rating_1' => $rating_1,
		                          'rating_2' => $rating_2,
		                          'rating_3' => $rating_3));
	}

	public function removeReview($reviewer_id, $article_id)
	{
		$statement =
			$this->pdo->prepare("DELETE FROM reviews WHERE reviewer_id = :reviewer_id AND article_id = :article_id");
		$statement->execute(array(':reviewer_id' => $reviewer_id,
		                          ':article_id' => $article_id));
	}

	public function getAllReviews()
	{
		$statement = $this->pdo->prepare("SELECT * FROM reviews");
		$statement->execute();
		return $statement->fetchAll();
	}

	public function getReviews($article_id)
	{
		$statement = $this->pdo->prepare("SELECT * FROM reviews WHERE article_id = :article_id");
		$statement->execute(array(':article_id' => $article_id));
		return $statement->fetchAll();
	}
}

?>