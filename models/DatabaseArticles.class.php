<?php

class DatabaseArticles
{
	private $pdo;

	public function __construct($pdo)
	{
		$this->pdo = $pdo;
	}

	public function sendArticle($title, $text, $user_id)
	{
		$statement = $this->pdo->prepare("INSERT INTO articles (name, published, author_id, content) 
											VALUES (:name, :published, :author_id, :content)");
		$statement->execute(array(':name' => $title,
		                          ':published' => false,
		                          ':author_id' => $user_id,
		                          'content' => $text));
	}

	public function getAllArticles()
	{
		$statement = $this->pdo->prepare("SELECT * FROM articles");
		$statement->execute();
		return $statement->fetchAll();
	}

	public function getAllPublishedArticles()
	{
		$statement = $this->pdo->prepare("SELECT * FROM articles WHERE published = true");
		$statement->execute();
		return $statement->fetchAll();
	}

	public function getAllArticleByID($id)
	{
		$statement = $this->pdo->prepare("SELECT * FROM articles WHERE id = :id");
		$statement->execute(array(':id' => $id));
		$result = $statement->fetchAll();
		return isset($result[0]) ? $result[0] : false;
	}

	public function publishArticle($article_id, $published)
	{
		$statement = $this->pdo->prepare("UPDATE articles SET published = :published WHERE id = :id");
		$statement->execute(array(':id' => $article_id, ':published' => $published));
	}

	public function deleteArticle($article_id)
	{
		$statement = $this->pdo->prepare("DELETE FROM articles WHERE id = :id");
		$statement->execute(array(':id' => $article_id));
	}

	public function isArticleOwnedBy($user_id, $article_id)
	{
		$statement = $this->pdo->prepare("SELECT * FROM articles WHERE author_id = :author_id AND id = :id");
		$statement->execute(array(':author_id' => $user_id,
		                          ':id' => $article_id));
		return $row = $statement->fetch();
	}
}

?>