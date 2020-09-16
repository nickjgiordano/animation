<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
		<script type="text/javascript" src="script.js"></script>
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
		<title>Homepage | Animation Station | Your watchlist</title>
	</head>
	<body onload="scrollPosition()">
		<?php
			require_once('nav.php');
			// replace database view to include user ID
			$query = "CREATE OR REPLACE VIEW viewUnion AS " .
			"SELECT Movie_Title, Release_Date, Studio_Name, Watchlist.ID AS WatchCount, " .
			"IF(Rating = 0, NULL, Rating) AS Rating, 'yes' AS Watched, " .
			"Rating AS User_Rating, Movie.ID AS Movie_ID, Watchlist.ID AS Watchlist_ID " .
			"FROM (Movie INNER JOIN Studio ON Studio_ID = Studio.ID) " .
			"LEFT JOIN Watchlist ON Movie.ID = Movie_ID " .
			"WHERE ACCOUNT_ID = '$userid' " .
			"UNION " .
			"SELECT Movie_Title, Release_Date, Studio_Name, IF(ACCOUNT_ID = '$userid', NULL, Watchlist.ID), " .
			"IF(ACCOUNT_ID = '$userid', NULL, IF(Rating = 0, NULL, Rating) ), IF(ACCOUNT_ID = '$userid', IF(Watchlist.ID > 0, 'yes', 'no'), 'no'), " .
			"IF(ACCOUNT_ID = '$userid', Rating, 0), Movie.ID, IF(ACCOUNT_ID = '$userid', Watchlist.ID, 0) " .
			"FROM (Movie INNER JOIN Studio ON Studio_ID = Studio.ID) " .
			"LEFT JOIN Watchlist ON Movie.ID = Movie_ID;";
			$result = mysqli_query($db, $query.';') or die("Error! Can't load data!");
			
			// select data for page display, using filter and sort preferences
			$query = 'SELECT * FROM viewMain';
			if( isset($_REQUEST['filter']) )
			{
				$filter = $_REQUEST['filter'];
				$filter_alias = str_replace('_', ' ', $filter);
				$criterion = $_REQUEST['criterion'];
				$query = "$query WHERE $filter = '$criterion'";
			}
			if( isset($_REQUEST['sort']) )
			{
				$sort = $_REQUEST['sort'];
				$order = $_REQUEST['order'];
				$query = "$query ORDER BY $sort $order";
			}
			$result = mysqli_query($db, $query.';') or die("Error! Can't load data!");
			
			// set number of column from view that should be displayed
			$colmax = 7;
			
			// create select list row for filtering
			echo '<br /><table class="watchlist"><tr><td colspan="'.$colmax.'" class="radius_top">&nbsp;</td></tr><tr class="filter">';
			for($i = 0 ; $i < $colmax ; $i++)
			{
				$col = mysqli_fetch_field($result);
				$colname = $col->name;
				$result_list = mysqli_query($db, "SELECT $colname FROM viewMain GROUP BY $colname ORDER BY $colname ASC;")
				or die("Error! Can't load data!");
				echo '<td>';
				?><select id="filter" onchange="filterMain('<?php echo $colname ?>', this)"><?php
				$colalias = str_replace('_', ' ', $colname);
				echo '<option value="'.$colname.'">'.$colalias.'</option>';
				mysqli_data_seek($result_list, 0);
				while( $row = mysqli_fetch_assoc($result_list) )
				{
					$data = $row[$colname];
					if( $colname == 'Average_Rating' && empty($data) ) {}
					else{echo '<option value="'.$data.'">'.$data.'</option>';}
				}
				echo '</select></td>';
			}
			
			// create sort link row for sorting
			echo '</tr><tr class="sort">';
			mysqli_field_seek($result, 0);
			for($i = 0 ; $i < $colmax ; $i++)
			{
				$col = mysqli_fetch_field($result);
				$colname = $col->name;
				echo '<td><a onclick="storePosition()" href="index.php?';
				if (isset($_REQUEST['filter']) && $filter == $colname)
				{echo '"><div class="clear_x">x';}
				else
				{
					if( isset($filter) ) {echo '&filter='.urlencode($filter).'&criterion='.urlencode($criterion);}
					echo '&sort='.urlencode($colname).'&order=';
					
					if(isset($sort) && $sort == $colname && $order == 'ASC')
					{echo 'DESC"><div>&#x25B2;</div></a></td>';}
					else if(isset($sort) && $sort == $colname && $order == 'DESC')
					{echo 'ASC"><div>&#x25BC;';}
					else
					{echo 'ASC"><div>&#x25AC;';}
				}
				echo '</div></a></td>';
			}
			echo '</tr>';
			
			// populate table with data
			while( $row = mysqli_fetch_assoc($result) )
			{
				// get data
				$movie = $row['Movie_Title'];
				$date = $row['Release_Date'];
				$studio = $row['Studio_Name'];
				$watchcount = $row['Watched_by'];
				$ratingavg = $row['Average_Rating'];
				$watched = $row['Watched'];
				$ratinguser = $row['User_Rating'];
				$movieid = $row['Movie_ID'];
				$key = $row['Watchlist_ID'];
				
				echo '<tr class="row">';
				// populate first 4 columns with data
				echo '<td style="min-width: 285px">'.$movie.'</td>';
				echo '<td class="centercol">'.$date.'</td>';
				echo '<td style="min-width: 240px">'.$studio.'</td>';
				echo '<td class="centercol">'.$watchcount.'</td>';
				
				// create star images for Average Rating
				echo '<td class="centercol">';
				for($i = 1 ; $i <= 5 ; $i++)
				{
						echo '<img src="images/star';
						if($ratingavg >= $i) {echo '3';} else {echo '0';}
						echo '.png">';
				}
				echo '</td>';
				
				// create checkbox for Watched column
				echo '<td class="centercol"><input type="checkbox" name="" value="" onclick="storePosition()" ' .
				'onchange="window.location.href=\'watchlist_checkbox.php?key='.urlencode($key).'&Watched='.urlencode($watched) .
				'&Movie_ID='.urlencode($movieid).'&Account_ID='.urlencode($userid);
				if( isset($filter) )
				{echo '&filter='.urlencode($filter).'&criterion='.urlencode($criterion);}
				if( isset($sort) )
				{echo '&sort='.urlencode($sort).'&order='.urlencode($order);}
				echo '\'" ';
				if($watched == 'yes') {echo 'checked';}
				
				// create star image links for User Rating
				echo ' /></td><td class="centercol">';
				if($watched == 'yes')
				{
					for($i = 1 ; $i <= 5 ; $i++)
					{
						echo '<a onclick="storePosition()" href="watchlist_rating.php?key='.urlencode($key).'&Rating='.$i;
						
						if( isset($filter) )
						{echo '&filter='.urlencode($filter).'&criterion='.urlencode($criterion);}
						if( isset($sort) )
						{echo '&sort='.urlencode($sort).'&order='.urlencode($order);}
						
						echo '"><img id="'.$key.'star'.$i.'" onmouseover="ratingChange('.$key.', '.$i .
						')" onmouseout="ratingCancel('.$key.', '.$ratinguser.')" src="images/star';
						if($i > $ratinguser) {echo '1';} else {echo '3';}
						echo '.png"></a>';
					}
				}
				echo '</td></tr>';
			}
			
			// create table footer
			$nrows = mysqli_num_rows($result);
			echo '<tr><td colspan="'.$colmax.'" class="empty_row">&nbsp;</td></tr>' .
			'<tr class="table_footer"><td colspan ="'.$colmax.'" class="table_info">Displaying '.$nrows.' record(s)';
			if( isset($filter) )
			{
				echo ' &nbsp; | &nbsp; <span>filtered by '.$filter_alias.' = '.$criterion.'</span>' .
				' &nbsp; | &nbsp; <a onclick="storePosition()" href="index.php" class="clear">clear filter</a>';
			}
			echo '</td></tr><tr><td colspan="'.$colmax.'" class="radius_bottom">&nbsp;</td></tr></table>';
			
			// free results and close database
			if( isset($result_list) ) {mysqli_free_result($result_list);}
			mysqli_free_result($result);
			mysqli_close($db);
		?>
	</body>
</html>