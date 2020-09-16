<?php
	require_once('connect.php');
	// update record with new Rating
	$key = $_REQUEST['key'];
	$rating = $_REQUEST['Rating'];
	$result = mysqli_query($db, "UPDATE Watchlist SET Rating = $rating WHERE ID = $key;");
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