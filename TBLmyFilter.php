<?php


include "team017-mysql-connect.php"; 

	$ptype = $_GET["ptype"];
	$sdate = $_GET["sdate"];
	$fdate = $_GET["fdate"];
	$primary = $_GET["primary"];
	$secondary = $_GET["secondary"];
	
	
	
//Connect to database 
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
//SQL to add sorts and filters to my unresolved problems
	$sql = "SELECT PROBID, PROBTYPE, HARDWARE, SOFTWARE, SERIALNO, CASE
				WHEN (l.SPECIALISTID > 1000 AND l.SPECIALISTID < 1100) THEN e.FNAME
				WHEN (l.SPECIALISTID > 9000 AND l.SPECIALISTID < 9999) THEN s.FNAME
			END AS FNAME, PRIORITY
			FROM log l
				LEFT OUTER JOIN employee e ON l.SPECIALISTID = e.EMPNO
				LEFT OUTER JOIN extspecialist s ON l.SPECIALISTID = s.TEMPNO
			WHERE (s.TEMPNO IS NOT null OR e.EMPNO IS NOT null)
			AND `SPECIALISTID` = '1002'
			AND `STATUS` = 'UNRESOLVED'";
 
	//echo $sql;
	if ($ptype != "fNone"){
		$sql .= " AND PROBTYPE = '$ptype'";
	}
	if ($sdate != "" || $fdate != ""){
		$sql .= " AND (INITIALDATE BETWEEN '$sdate' AND '$fdate'
				  OR INITIALDATE >= '$sdate' AND UPDATEDDATE <= '$fdate')";
	}
	if ($primary != "pN"){
		$sql .= " ORDER BY '$primary' '$secondary'";
	}
	//echo $sql;

/* get all the rows and output it as a JSON */
	$result = mysqli_query($conn, $sql); 
	$allDataArray = array();
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$allDataArray[] = $row;
	}
	$value = json_encode($allDataArray);
	echo $value;

mysqli_close($conn);
?>