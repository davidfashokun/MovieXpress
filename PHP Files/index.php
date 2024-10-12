<?php

$EMAIL_ID = 613818673; // 9-digit integer value (i.e., 123456789)
$API_KEY = "1b22fb91"; // API key (string) provided by Open Movie DataBase (i.e>

session_start(); // Connect to the existing session

require_once '/home/common/php/dbInterface.php'; // Add database functionality
require_once '/home/common/php/mail.php'; // Add email functionality
require_once '/home/common/php/p4Functions.php'; // Add Project 4 base functions

processPageRequest(); // Call the processPageRequest() function

// DO NOT REMOVE OR MODIFY THE CODE OR PLACE YOUR CODE ABOVE THIS LINE

function addMovieToCart($imdbID)
{
    $movieId = movieExistsInDB($imdbID);

    if ($movieId === 0) {
	$result = file_get_contents('http://www.omdbapi.com/?apikey='.$GLOBALS['API_KEY'].'&i='.$imdbID.'&type=movie&r=json');
	$movieInfo = json_decode($result, true);
        $movieId = addMovie(
            $imdbID,
            $movieInfo['Title'],
            $movieInfo['Year'],
            $movieInfo['Rated'],
            $movieInfo['Runtime'],
            $movieInfo['Genre'],
            $movieInfo['Actors'],
            $movieInfo['Director'],
            $movieInfo['Writer'],
            $movieInfo['Plot'],
            $movieInfo['Poster']
        );
    }

    $userId = $_SESSION['userId'];
    addMovieToShoppingCart($userId, $movieId);
    header('Location: index.php');
    exit;
}

function displayCart()
{
    $userId = $_SESSION['userId'];
    $movies = getMoviesInCart($userId);

    require_once './templates/cart_form.html';
}

function processPageRequest()
{
    if (!isset($_SESSION['displayName'])) {
        header('Location: logon.php');
        exit;
    }

    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'add' && isset($_GET['imdb_id'])) {
            addMovieToCart($_GET['imdb_id']);
        } elseif ($_GET['action'] === 'remove' && isset($_GET['movie_id'])) {
            removeMovieFromCart($_GET['movie_id']);
        }
    } else {
        displayCart();
    }
}

function removeMovieFromCart($movieID)
{
    $userId = $_SESSION['userId'];
    removeMovieFromShoppingCart($userId, $movieID);
    header('Location: index.php');
    exit;
}
?>
