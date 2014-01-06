<?php

//======================== Config ==========================================================	

$dbuser = 'root';									// your MySQL user name
$dbpass = 'vertrigo';								// your MySQL password
$books_folder = "E:\Books\Books_Science_New";		// path to your collection of books 



// Databases
$db1 = 'mybooks';				// your database
$db2 = 'bookwarrior';			// LibGen database
$db3 = 'bw2009';				// Some other database


$dbtable = 'updated';			// database table



$dbhost = 'localhost';
$N_lines = 3000;					// max number of table lines to display

//======================== The html Head ==========================================================	
echo "<html><head>
<meta http-equiv='content-type' content='text/html; charset=utf-8'>
<title>Library</title>


<style type='text/css'>
	.c { font-family: Tahoma; font-size: 11px; color: #000000; LETTER-SPACING: 0px; }
	A { text-decoration: none; }
	td { padding: 1px; }
	table { border-spacing: 1px 1px; }
</style>

<script type='text/javascript'>
	function f() {document.getElementById('1').focus();}
	window.onload = f;
</script>
</head><body topmargin=0>";


//======================== Connect to MySQL DB ==========================================================	

$con = mysql_connect($dbhost,$dbuser,$dbpass);
if (!$con)
	die("<h1>Error</h1>".mysql_error());

mysql_query("SET session character_set_server = 'UTF8'");
mysql_query("SET session character_set_connection = 'UTF8'");
mysql_query("SET session character_set_client = 'UTF8'");
mysql_query("SET session character_set_results = 'UTF8'");
	
//============================= Check what server has in POST ======================	

if (!empty($_POST))
	{
	//print "You Typed: $req ";
	$req = $_POST['req'];
	$db = $_POST['db'];				//echo $_POST['db'];

	// State Preserve : remember which database was selected.
	if ($db == $db1) {$s1 = 'selected'; $s2 = $s3 = '';}
	else if ($db == $db2) {$s2 = 'selected'; $s1 = $s3 = '';}
	else {$s3 = 'selected'; $s1 = $s2 = '';};
	
	}
else{
	// State Clean:
	$req = '';
	$s1 = $s2 = $s3 = '';
	}

//===================== Form ======================================================

$This_file_name = $_SERVER["PHP_SELF"];		

echo "<form method='post' action='$This_file_name'>

		<select name='db'>
			<option value=$db1 $s1>$db1</option>
			<option value=$db2 $s2>$db2</option>
			<option value=$db3 $s3>$db3</option>
		</select>

		<input type='text' name='req' value=$req>

		<input type='submit' value='Submit'>
		
		</form>";

//======================= Table ================================================

// Table Head
$color1 = '#D0D0D0';
$color2 = '#F6F6FF';
$color3 = '#A0E000';

$reshead = "<table width=100% cellspacing=0 cellpadding=0 border=0 class=c align=center>";	
$navigator = "<tr><th valign=top bgcolor=$color3 colspan=5><font color=$color1><center><b>prevlink |nextlink</b></center></font></th></tr>";
$tabheader = "<tr valign=top bgcolor=$color2><td><b>#</b></td><td><b>Name</b></td><td><b>Author</b></td><td><b>Size</b></td><td><b>Type</b></td></tr>";

echo $reshead;
echo $navigator;
echo $tabheader;

//======================= MSQL Requst ================================================	

if (strlen($req) > 2)					// give me at list 3 symbols in the request. 				
	{							
	mysql_select_db($db,$con);			// select the table to work with

	$from = 0;							// start from 
	$sql_mid = "FROM $dbtable WHERE (Author LIKE '%{$req}%' OR Title LIKE '%{$req}%' )";
	$sql_end = "LIMIT $from, $N_lines";					// $N_lines - max number of lines to display, see config Top
	$sql_req = "SELECT * ".$sql_mid.$sql_end;
	$sql_cnt = "SELECT COUNT(*) ".$sql_mid;
	
	$result = mysql_query($sql_req, $con) or die('Error: ' . mysql_error());			// MySQL quire
	
	$p = strlen($books_folder);
	$i = 1;
	while ($row = mysql_fetch_assoc($result))
		{
		echo f_Generate_Row_in_Table($db, $row, $i, $from, $color2, $p);		
		$i = $i + 1;
		}
	
	mysql_free_result($result);
	}

// End of The Table 	
echo "</tr></table>\n";

//============================= Functions =========================================	

function f_Generate_Row_in_Table($db, $row, $i, $from, $color2, $p)
	{
		$title = stripslashes($row['Title']);
		$author = stripslashes($row['Author']);
		
		// Book Info
		$publisher = stripslashes($row['Publisher']);
		$year = $row['Year'];
		$pages = $row['Pages'];
		$ident = stripslashes($row['Identifier']);				//ISBN of books
		$lang = stripslashes($row['Language']);		
		
		// Vol Info
		$vol = stripslashes($row['VolumeInfo']);
		$edition = stripslashes($row['Edition']);

		// file description
		$ext = stripslashes($row['Extension']);
		$library = stripslashes($row['Library']);
		$size = f_Convert_Size($row['Filesize']);
		$MD5 = $row['MD5'];
		$ID = $row['ID'];
		$Path = '';
		if ($db == 'mybooks'){
			$Path = $row['Path'];
			$Path = '/books'.substr($Path,$p);
		}

		// book info string (the one we see in parentheses)		
		$volstamp = f_Book_Info($lang, $publisher, $year, $pages);
			
		// book Volume string [the one we see in square brackets]	
		$volume = f_Book_Vol($lang, $vol, $edition);

		// You will see Tip: the library, file reference, if you put mouse on the link and wait for couple of sec. 
		$tip = f_Book_Tip($ID, $MD5, $library, $ident);
		
		// index - the first column
		$ires = $from + $i;

		
		//==================== current row of the Table ============================
		
		if ($i % 2) $color = ""; 			// flip colours $color1
		else $color = $color2;
		
		$line = "<tr valign=top bgcolor=$color>
		<td>$ires.</td>
		<td nowrap><a href='$Path' title='$Path $tip'>$title$volume$volstamp</a></td>
		<td nowrap>$author</td>
		<td nowrap>$size</td>
		<td nowrap>$ext</td>
		</tr>\n\n";

		return $line;
	}
	
// book info section (the one we see in parentheses)
function f_Book_Info($lang, $publisher, $year, $pages)
	{
		$volinf = $publisher;

		if ($volinf){
			if ($year) $volinf = $volinf.', '.$year;
		} else {
			if ($year) $volinf = $year;
		}

		if ($lang == 'Russian') $pp = ' ÑÑ‚Ñ€.';
		else $pp = ' pp.';
		if ($volinf){
			if ($pages) $volinf = $volinf.', '.$pages.$pp;
		} else {
			if ($pages) $volinf = $pages.$pp;
		}
		
		$volstamp = '';
		if ($volinf) $volstamp = " <font face=Times color=green><i>($volinf)</i></font>";
		
		return $volstamp;
	}

	
function f_Book_Vol($lang, $vol, $edition)
	{	
		$vol_ed = $vol;
		if ($lang == 'Russian') $ed = ' Ð¸Ð·Ð´.';
		else $ed = ' ed.';
		if ($vol_ed){
			if ($edition) $vol_ed = $vol_ed.', '.$edition.$ed;
		} else {
			if ($edition) $vol_ed = $edition.$ed;
		}
	$volume = '';
	if ($vol_ed) $volume = " <font face=Times color=green><i>[$vol_ed]</i></font>";
	return $volume;
	}	
	

function f_Book_Tip($ID, $MD5, $library, $ident)
	{
		$tip = "\nID: $ID\nMD5: $MD5\n";
		if ($ident)  $tip = $tip.'ISBN '.$ident;
		if ($library) $tip = $tip."\n".$library."\n";
		return $tip;
	}

	
function f_Convert_Size($size)
	{	
		if ($size >= 1024*1024*1024){
			$size = round($size/1024/1024/1024);
			$size = $size.' GB';
		} else
		if ($size >= 1024*1024){
			$size = round($size/1024/1024);
			$size = $size.' MB';
		} else
		if ($size >= 1024){
			$size = round($size/1024);
			$size = $size.' kB';
		} else
			$size = $size.' B';
		return $size;
	}

	
//================================ The End =======================================		
mysql_close($con);		
echo "</body></html>";
	
?>




 
