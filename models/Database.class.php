<?php

class Database
{
	private $pdo;

	public function connectToDB()
	{
		try
		{
			$this->pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
			//$this->pdo->exec("set names utf32");
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e)
		{
			echo "Connection failed: " . $e->getMessage();
		}
	}

	public function login($email, $password): bool
	{
		$statement = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
		$statement->execute(array(':email' => $email));
		if($row = $statement->fetch())
		{
			if(password_verify($password, $row["password"]) != 1)
			{
				return false;
			}
			$_SESSION["id"] = $row["id"];
			$_SESSION["email"] = $row["email"];
			$_SESSION["first_name"] = $row["first_name"];
			$_SESSION["last_name"] = $row["last_name"];
			$_SESSION["rights"] = $row["rights"];
		}
		return $row ? true : false;
	}

	public function register($email, $first_name, $last_name, $password)
	{
		$statement = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
		$statement->execute(array(':email' => $email));
		if($row = $statement->fetch())
		{
			return "E-mail address is already used";
		}

		$hash = password_hash($password, PASSWORD_BCRYPT);

		$statement = $this->pdo->prepare("INSERT INTO users (email, first_name, last_name, password, rights) 
											VALUES (:email, :first_name, :last_name, :password, :rights)");
		$statement->execute(array(':email' => $email,
		                          ':first_name' => $first_name,
		                          ':last_name' => $last_name,
		                          ':password' => $hash,
		                          'rights' => "aut"));

		return $this->login($email, $password);
	}

	public function updateProfile($id, $email, $first_name, $last_name, $password)
	{
		$statement = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
		$statement->execute(array(':email' => $email));
		if($row = $statement->fetch())
		{
			if($row["email"] != $email)
			{
				return "E-mail address is already used";
			}
		}

		$hash = password_hash($password, PASSWORD_BCRYPT);

		$statement = $this->pdo->prepare("UPDATE users SET email = :email, first_name = :first_name, 
                 							last_name = :last_name, password = :password WHERE id = :id");
		$statement->execute(array(':id' => $id,
								  ':email' => $email,
		                          ':first_name' => $first_name,
		                          ':last_name' => $last_name,
		                          ':password' => $hash));

		Sessions::logout();
		return $this->login($email, $password);
	}

	public function getAllUsers()
	{
		$statement = $this->pdo->prepare("SELECT * FROM users");
		$statement->execute();
		return $statement->fetchAll();
	}

	public function setRights($id, $rights)
	{
		$statement = $this->pdo->prepare("UPDATE users SET rights = :rights WHERE id = :id");
		$statement->execute(array(':id' => $id, ':rights' => $rights));
	}

	public function deleteUser($id)
	{
		$statement = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
		$statement->execute(array(':id' => $id));
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

	public function getAllArticleByID($id)
	{
		$statement = $this->pdo->prepare("SELECT * FROM articles WHERE id = :id");
		$statement->execute(array(':id' => $id));
		$result = $statement->fetchAll();
		return isset($result[0]) ? $result[0] : false;
	}

	public function getUserArticles($user_id)
	{
		$statement = $this->pdo->prepare("SELECT * FROM articles WHERE author_id = :author_id");
		$statement->execute(array(':author_id' => $user_id));
		return $statement->fetchAll();
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

	public function getUser($id)
	{
		$statement = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
		$statement->execute(array(':id' => $id));
		return $statement->fetch();
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