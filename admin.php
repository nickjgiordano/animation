<!DOCTYPE html>
<html>
	<head></head>
	<body>
		<?php
			// get list of tables and views to create arrays
			$result = mysqli_query($db, 'SHOW tables;');
			$tables = array();
			$tables_fk = array();
			while( $row = mysqli_fetch_row($result) )
			{
				$tablename = $row[0];
				if(substr($tablename, 0, 4) != 'view' && substr($tablename, 0, 4) != 'form' && substr($tablename, 0, 4) != 'list')
				{array_push( $tables, ucfirst($tablename) );}
				if(substr($tablename, 0, 4) == 'view' || substr($tablename, 0, 4) == 'form')
				{array_push( $tables_fk, ucfirst( substr($tablename, 4) ) );}
			}
			// create menu items containing table names
			echo '<div class="menu"><div class="separator"></div>';
			for($i = 0 ; $i < count($tables) ; $i++)
			{
				echo '<a onclick="storePosition()" href="data.php?table='.$tables[$i].'"><div class="menu_item';
				if (isset($_REQUEST['table']) && $_REQUEST['table'] == $tables[$i]) {echo '_selected';}
				echo '">'.$tables[$i].'</div></a><div class="separator"></div>';
			}
			echo '</div>';
		?>
	</body>
</html>