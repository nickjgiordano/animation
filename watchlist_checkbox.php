<?php
	require_once('connect.php');
	// if user hasn't watched movie, add record; otherwise delete record
	$watched = $_REQUEST['Watched'];
	if($watched == 'no')
	{
		$userid = $_REQUEST["Account_ID"];
		$movieid = $_REQUEST["Movie_ID"];
		$result = mysqli_query($db, "INSERT INTO Watchlist (Account_ID, Movie_ID) VALUES ('$userid', '$movieid');");
	}
	else
	{
		$key = $_REQUEST['key'];
		$result = mysqli_query($db, "DELETE FROM Watchlist WHERE ID = '$key';");
	}
	// create link to redirect user, maintaining filters and sorts
	$header = 'Location: index.php?';
	if( isset($_REQUEST['filter']) )
	{
		$filter = $_REQUEST['filter'];
		$criterion = $_REQUEST['criterion'];
		$header = $header.'&filter='.urlencode($filter).'&criterion='.urlencode($criterion);
	}
	if( isset($_REQUEST['sort']) )
	{
		$sort = $_REQUEST['sort'];
		$order = $_REQUEST['order'];
		$header = $header.'&sort='.urlencode($sort).'&order='.urlencode($order);
	}
	// redirect user
	if( empty( mysqli_error($db) ) )
	{header($header);}
	else
	{header($header.'&fail');}
?>