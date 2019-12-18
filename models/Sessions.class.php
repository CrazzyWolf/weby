<?php

class Sessions
{
	public static function isLogged() : bool
	{
		return isset($_SESSION["id"]) && !$_SESSION["id"] == "";
	}

	public static function isAdmin() : bool
	{
		return $_SESSION["rights"] == "adm";
	}

	public static function isReviewer() : bool
	{
		return $_SESSION["rights"] == "rev";
	}

	public static function getID()
	{
		return $_SESSION["id"];
	}

	public static function logout()
	{
		session_unset();
	}

	public static function redirect($url)
	{
		header("Location: " . $url);
		die();
	}
}