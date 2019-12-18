<?php

require_once "settings.inc.php";
require_once DIRECTORY_CONTROLLERS . "DatabaseConnection.class.php";

class MyApplication extends DatabaseConnection
{
	private $webPages;
	private $mustBeLoggedPages;
	private $mustNotBeLoggedPages;

	public function __construct()
	{
		$this->webPages = array
		(
			//controllery s priponou .class.php
			"login" => "LoginController",
			"register" => "RegisterController",
			"admin" => "AdminController",
			"submitArticle" => "SubmitArticleController",
			"submitReview" => "SubmitReviewController",
			"userArticles" => "UserArticlesController",
			"review" => "ReviewController",
			"showReviews" => "ReviewsController",
			"profile" => "ProfileController",

			//twig sablony s priponou .twig
			"404" => "404",
			"403" => "403",
		);
		$this->mustBeLoggedPages = array
		(
			"admin",
			"submitArticle",
			"submitReview",
			"userArticles",
			"review",
			"showReviews",
			"profile"
		);
		$this->mustNotBeLoggedPages = array
		(
			"login",
			"register",
		);
	}

	public function appStart()
	{
		$data = array();
		session_start();
		$accessAllowed = true;

		if(isset($_GET["logout"]))
		{
			Sessions::logout();
			$accessAllowed = false;
		}
		else if(isset($_GET["web"]))
		{
			if(Sessions::isLogged())
			{
				$disallowedPages = $this->mustNotBeLoggedPages;

				if(Sessions::isAdmin() == false)
				{
					array_push($disallowedPages, "admin");
					if(Sessions::isReviewer() == false)
					{
						array_push($disallowedPages, "reviews");
						array_push($disallowedPages, "submitReview");
					}
				}
			}
			else
			{
				$disallowedPages = $this->mustBeLoggedPages;
			}

			if(in_array($_GET["web"], $disallowedPages))
			{
				$accessAllowed = false;
			}
		}
		else
		{
			$accessAllowed = false;
		}

		if($accessAllowed && array_key_exists($_GET["web"], $this->webPages))
		{
			$webKey = $this->webPages[$_GET["web"]];
			if(array_key_exists($webKey, $this->webPages))
			{
				$this->renderInTwig($webKey . ".twig", $data);
			}
			else
			{
				require_once DIRECTORY_CONTROLLERS . "$webKey.class.php";
				new $webKey($data);
			}
		}
		else
		{
			require_once DIRECTORY_CONTROLLERS . "ArticlesController.class.php";
			new ArticlesController($data);
		}
	}

	public static function renderInTwig(string $template, array $data)
	{
		require_once 'composer/vendor/autoload.php';

		$loader = new \Twig\Loader\FilesystemLoader(DIRECTORY_VIEWS);

		$twig = new \Twig\Environment($loader, ['debug' => true]);

		$twig->addExtension(new \Twig\Extension\DebugExtension());

		if(Sessions::isLogged())
		{
			$data["first_name"] = $_SESSION["first_name"];
			$data["last_name"] = $_SESSION["last_name"];
			$data["rights"] = $_SESSION["rights"];
		}

		echo $twig->render($template, $data);

		echo "<script>
			    document.getElementById('$template').className = \"collapse-item active\";
			 </script>";
	}
}

$app = new MyApplication();
$app->appStart();

?>