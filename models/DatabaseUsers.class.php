<?php

class DatabaseUsers
{
	private $pdo;

	public function __construct($pdo)
	{
		$this->pdo = $pdo;
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

	public function getUserArticles($user_id)
	{
		$statement = $this->pdo->prepare("SELECT * FROM articles WHERE author_id = :author_id");
		$statement->execute(array(':author_id' => $user_id));
		return $statement->fetchAll();
	}

	public function getUser($id)
	{
		$statement = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
		$statement->execute(array(':id' => $id));
		return $statement->fetch();
	}
}

?>