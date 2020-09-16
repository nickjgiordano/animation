<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
		<script type="text/javascript" src="script.js"></script>
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
		<title>Database Administration | Animation Station | View data</title>
	</head>
	<body onload="scrollPosition()">
		<?php
			require_once('nav.php');
			require_once('admin.php');
			// if table is set, create table, otherwise display welcome message
			if( isset($_REQUEST['table']) )
			{
				// if table uses view, set prefix
				$table = $_REQUEST['table'];
				if(array_search($table, $tables_fk) > -1) {$view = 'view'.$table;} else {$view = $table;}
				
				// select data for page display, using filter and sort preferences
				$query = "SELECT * FROM $view";
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
				
				// create select list row for filtering
				$ncolumns = mysqli_num_fields($result);
				echo '<br /><table class="data"><tr><td colspan="'.($ncolumns+2).'" class="radius_top"></td></tr><tr class="filter_data">';
				while( $col = mysqli_fetch_field($result) )
				{
					$colname = $col->name;
					$coltype = $col->type;
					$result_list = mysqli_query($db, "SELECT $colname FROM $view GROUP BY $colname ORDER BY $colname ASC;")
					or die("Error! Can't load data!");
					echo '<td>';
					?><select id="filter" onchange="filter('<?php echo $table ?>', '<?php echo $colname ?>', this)"><?php
					$colalias = str_replace('_', ' ', $colname);
					echo '<option value="'.$colname.'">'.$colalias.'</option>';
					mysqli_data_seek($result_list, 0);
					while( $row = mysqli_fetch_assoc($result_list) )
					{
						$data = $row[$colname];
						$value = $data;
						if($coltype == 1)
						{
							if($value) {$data = 'yes';} else if(!$value) {$data = 'no';}
						}
						echo '<option value="'.$value.'">'.$data.'</option>';
					}
					echo '</select></td>';
				}
				
				// create sort link row for sorting
				echo '<td colspan="2"></td></tr><tr class="sort">';
				mysqli_field_seek($result, 0);
				while( $col = mysqli_fetch_field($result) )
				{
					$colname = $col->name;
					echo '<td><a onclick="storePosition()" href="data.php?table='.urlencode($table);
					if (isset($_REQUEST['filter']) && $filter == $colname)
					{echo '"><div class="clear_x">x';}
					else
					{
						if( isset($filter) ) {echo '&filter='.urlencode($filter).'&criterion='.urlencode($criterion);}
						echo '&sort='.urlencode($colname).'&order=';
						
						if(isset($sort) && $sort == $colname && $order == 'ASC')
						{echo 'DESC"><div>&#x25B2;';}
						else if(isset($sort) && $sort == $colname && $order == 'DESC')
						{echo 'ASC"><div>&#x25BC;';}
						else
						{echo 'ASC"><div>&#x25AC;';}
					}
					echo '</div></a></td>';
				}
				echo '<td colspan="2"></td></tr>';
				
				// populate table with data
				while( $row = mysqli_fetch_assoc($result) )
				{
					// get data to populate table
					$key = $row['ID'];
					echo '<tr class="row_data">';
					mysqli_field_seek($result, 0);
					while ( $col = mysqli_fetch_field($result) )
					{
						$colname = $col->name;
						$coltype = $col->type;
						$data = $row[$colname];
						if($coltype == 1)
						{
							if($data) {$data = 'yes';} else if(!$data) {$data = 'no';}
						}
						echo '<td class="column">'.$data.'</td>';
					}
					// create edit link
					echo '<td class="edit"><a onclick="storePosition()" href="edit.php?table='.urlencode($table).'&key='.urlencode($key) .
					'"><div>EDIT</div></a></td><td class="delete';
					// create delete link
					if( isset($_REQUEST['delete_fail']) && $_REQUEST['delete_fail'] == $key ) {echo '_fail';}
					echo '"><a onclick="storePosition()" href="delete.php?table='.urlencode($table).'&key='.urlencode($key);
					if( isset($filter) )
					{echo '&filter='.urlencode($filter).'&criterion='.urlencode($criterion);}
					if( isset($sort) )
					{echo '&sort='.urlencode($sort).'&order='.urlencode($order);}
					echo '"><div>';
					if( isset($_REQUEST['delete_fail']) && $_REQUEST['delete_fail'] == $key ) {echo 'FOREIGN<br />KEY<br />USED';} else {echo 'DELETE';}
					echo '</div></a></td></tr>';
				}
				
				// create table footer, including add link
				$nrows = mysqli_num_rows($result);
				echo '<tr><td colspan="'.($ncolumns+2).'" class="empty_row">&nbsp;</td></tr>' .
				'<tr class="table_footer"><td colspan ="'.$ncolumns.'" class="table_info">Displaying '.$nrows.' record(s)';
				if( isset($filter) )
				{
					echo ' &nbsp; | &nbsp; <span>filtered by '.$filter_alias.' = '.$criterion.'</span>' .
					' &nbsp; | &nbsp; <a onclick="storePosition()" href="data.php?table='.urlencode($table).'" class="clear">clear filter</a>';
				}
				echo '</td><td colspan="2" class="add"><a href="add.php?table=' .
				urlencode($table).'"><div>ADD NEW</div></a></td></tr><tr><td colspan="'.($ncolumns+2).'" class="radius_bottom">&nbsp;</td></tr></table>';
				
				// free results and close database
				if( isset($result_list) ) {mysqli_free_result($result_list);}
				mysqli_free_result($result);
				mysqli_close($db);
			}
			else
			{
				echo '<div class="content_wrapper"><div class="content">' .
				'<p>Welcome to the Admin Area! From here, you can edit the database directly.</p>' .
				'<p>Click the links above to navigate the different tables!</p>' .
				'</div></div>';
			}
		?>
	</body>
</html>