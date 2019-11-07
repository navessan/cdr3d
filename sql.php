<?php

/* Default database settings*/
$database_type = "mysql";
$database_default = "cdr";
$database_hostname = "localhost";
$database_username = "cdr";
$database_password = "password";
$database_port = "";

/* display ALL errors */
error_reporting(E_ALL);

header("Content-type: application/json; charset=utf-8");

/* Include configuration */
include("config.php");

if($database_type=="sqlsrv")
	$dsn = "$database_type:server=$database_hostname;database=$database_default";
else 	
	$dsn = "$database_type:host=$database_hostname;dbname=$database_default;charset=$database_charset";

$opt = array(
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);

try {
	$conn = new PDO($dsn, $database_username, $database_password, $opt);
}
catch(PDOException $e) {
	die($e->getMessage());
}

//$date=date("Y-m-d");
$date = new DateTime();
$date->modify('-1 day');
$date=$date->format('Y-m-d');
//echo $date."\n";
$date_from=$date.' 00:00:00';
$date_till=$date.' 23:59:59';

if(isset($_REQUEST['date_from'])){
	
}

$date_from='2019-10-07 00:00:00';
//$date_till='2017-03-01 00:00:00';

if(isset($_REQUEST['query_type'])){
	$query_type=$_REQUEST['query_type'];
}

//------------------

/* Set up and execute the query. */
$sql = "select
 sum(case when ttime<5 then 1 else 0 end) as UnansweredCalls
,sum(case when ttime>=5 then 1 else 0 end) as AnsweredCalls
,count(ttime) as ReceivedCalls
,sum(ttime)/60 as SumTalkTime
,to_days(calldate)-to_days(:date_from) as to_days
,calldate
,h as y
from(
	/* t1 ---------------*/
	SELECT 
		SUM(`talk time`) AS TTIME, 
		uniqueid as uniqueid
		,from_days(to_days(`start time`)) AS calldate 
		,hour(`start time`) as h
	 FROM `imp_cdr` 
	 WHERE (`source trunk name` LIKE '%6070167%' or `source trunk name` LIKE '%6070746%')
	 and `start time`> :date_from
	 and `start time`< :date_till
	 GROUP BY from_days(to_days(`start time`)), to_days(`start time`)
	 ,hour(`start time`)
	 , uniqueid 
	/* HAVING TTIME = 0 */
	 ORDER BY calldate
	/* t1 ---------------*/
	 ) as t1
group by calldate,h
order by calldate,h
";

/* Set up and execute the query. */
if($query_type=='Daily'){
$sql = "select
 sum(case when ttime<5 then 1 else 0 end) as UnansweredCalls
,sum(case when ttime>=5 then 1 else 0 end) as AnsweredCalls
,count(ttime) as ReceivedCalls
,sum(ttime)/60 as SumTalkTime
,to_days(calldate)-to_days(:date_from) as to_days
,calldate
,24 as y
from(
	/* t1 ---------------*/
	SELECT 
		SUM(`talk time`) AS TTIME, 
		uniqueid as uniqueid
		,from_days(to_days(`start time`)) AS calldate 
	 FROM `imp_cdr` 
	 WHERE (`source trunk name` LIKE '%6070167%' or `source trunk name` LIKE '%6070746%')
	 and `start time`> :date_from
	 and `start time`< :date_till
	 GROUP BY 
	 from_days(to_days(`start time`))
	 ,to_days(`start time`)
	 ,uniqueid 
	/* HAVING TTIME = 0 */
	 ORDER BY calldate
	/* t1 ---------------*/
	 ) as t1
group by calldate
order by calldate
";
}

if($query_type=='Operators'){
$sql="
SELECT 
 sum(case when ttime<5 then 1 else 0 end) as UnansweredCalls
,sum(case when ttime>=5 then 1 else 0 end) as AnsweredCalls 
,count(ttime) as ReceivedCalls
,sum(ttime)/60 as SumTalkTime
,to_days(calldate)-to_days(:date_from) as to_days
,calldate
,operator as y
 FROM 
	(SELECT 
		count(1) AS c,
		SUM(`talk time`) AS TTIME, 
		uniqueid as uniqueid,
		`source trunk name` as trunk,
		from_days(to_days(`start time`)) AS calldate 
		,`answer by` as operator
	 FROM `imp_cdr` 
	 WHERE (`source trunk name` LIKE '%6070167%' or `source trunk name` LIKE '%6070746%')
	 	 and (`answer by` in ('2105', '4400') or 
			  `answer by` like '201%' or 
			  `answer by` like '24%'  )
		 and `start time`> :date_from
		 and `start time`< :date_till
	 GROUP BY from_days(to_days(`start time`)), to_days(`start time`), `source trunk name`, uniqueid, `answer by`
	/* HAVING TTIME = 0 */
	 ORDER BY calldate) as t1 
 GROUP BY calldate,operator
 order by calldate,operator
";	
}

$r=array('date_from' =>$date_from
		,'date_till' =>$date_till); 
 
$stmt = $conn->prepare($sql);
$stmt -> execute($r);

//$stmt = $conn->query($tsql);
$rows = $stmt->fetchAll();

$numRows = count($rows);
//echo "<p>$numRows Row" . ($numRows == 1 ? "" : "s") . " Returned </p>";

if($numRows>0)
{	
	echo json_encode($rows);
}
else 
{
	echo json_encode("No rows returned.", JSON_FORCE_OBJECT);
}

?>