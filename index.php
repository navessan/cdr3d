<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<!-- Load jquery for ajax support -->
	<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="libs/vis-4.21.0/dist/vis.js"></script>
	<script src="libs/vis-4.21.0/dist/vis.min.css"></script>
	<script src="script.js"></script>
	<style id="compiled-css" type="text/css">
		  html, body {
		  //font: 10pt arial;
		  padding: 0;
		  margin: 0;
		  width: 100%;
		  height: 100%;
		  }
		#mygraph {
		  padding: 0;
		  margin: 0;
		  width: 100%;
		  height: 100%;
		}
	</style>   
</head>

<body >

<p>
	<table>
		<tr>
		<td>Calldate</td>
		<td>С&nbsp;
<select name="startday" id="startday">
	<?php
	for ($i = 1; $i <= 31; $i++) {
		if ( date('d', time()) == $i ) {
			echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
		} else {
			echo '<option value="'.$i.'">'.$i.'</option>';
		}
	}
	?>
</select>
<select name="startmonth" id="startmonth">
<?php
$months = array('01' => 'Январь', '02' => 'Февраль', '03' => 'Март', '04' => 'Апрель', '05' => 'Май', '06' => 'Июнь', '07' => 'Июль', '08' => 'Август', '09' => 'Сентябрь', '10' => 'Октябрь', '11' => 'Ноябрь', '12' => 'Декабрь');
foreach ($months as $i => $month) {
	if ( date('m') == $i ) {
		echo '<option value="'.$i.'" selected="selected">'.$month.'</option>';
	} else {
		echo '<option value="'.$i.'">'.$month.'</option>';
	}
}
?>
</select>
<select name="startyear" id="startyear">
	<?php
	for ( $i = 2000; $i <= date('Y'); $i++) {
		if ( date('Y') == $i ) {
			echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
		} else {
			echo '<option value="'.$i.'">'.$i.'</option>';
		}
	}
	?>
</select>&nbsp;
<select name="starthour" id="starthour">
	<?php
	for ($i = 0; $i <= 23; $i++) {
		if ( $i == 0 ) {
			echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
		} else {
			echo '<option value="'.$i.'">'.$i.'</option>';
		}
	}
	?>
</select>
:
<select name="startmin" id="startmin">
	<?php
	for ($i = 0; $i <= 59; $i++) {
		if ( $i == 0 ) {
			echo '<option value="'.sprintf('%02d', $i).'" selected="selected">'.sprintf('%02d', $i).'</option>';
		} else {
			echo '<option value="'.sprintf('%02d', $i).'">'.sprintf('%02d', $i).'</option>';
		}
	}
	?>
</select>
&ensp;
По&ensp;
<select name="endday" id="endday">
	<?php
	for ($i = 1; $i <= 31; $i++) {
		if ( $i == 31 ) {
			echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
		} else {
			echo '<option value="'.$i.'">'.$i.'</option>';
		}
	}
	?>
</select>
<select name="endmonth" id="endmonth">
	<?php
	foreach ($months as $i => $month) {
		if ( date('m') == $i ) {
			echo '<option value="'.$i.'" selected="selected">'.$month.'</option>';
		} else {
			echo '<option value="'.$i.'">'.$month.'</option>';
		}
	}
	?>
</select>
<select name="endyear" id="endyear">
	<?php
	for ( $i = 2000; $i <= date('Y'); $i++) {
		if ( date('Y') == $i ) {
			echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
		} else {
			echo '<option value="'.$i.'">'.$i.'</option>';
		}
	}
	?>
</select>&nbsp;
<select name="endhour" id="endhour">
	<?php
	for ($i = 0; $i <= 23; $i++) {
		if ( $i == 23 ) {
			echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
		} else {
			echo '<option value="'.$i.'">'.$i.'</option>';
		}
	}
	?>
			</select>
			:
			<select name="endmin" id="endmin">
	<?php
	for ($i = 0; $i <= 59; $i++) {
		if ( $i == 59 ) {
			echo '<option value="'.sprintf('%02d', $i).'" selected="selected">'.sprintf('%02d', $i).'</option>';
		} else {
			echo '<option value="'.sprintf('%02d', $i).'">'.sprintf('%02d', $i).'</option>';
		}
	}
	?>
			</select>
			&emsp;
			<select id="id_range" name="range">
				<option class="head">Выбрать период...</option>
				<option value="td">Сегодня</option>
				<option value="yd">Вчера</option>
				<option value="3d">Последние 3 дня</option>
				<option value="tw">Текущая неделя</option>
				<option value="pw">Предыдущая неделя</option>
				<option value="3w">Последние 3 недели</option>
				<option value="tm">Текущий месяц</option>
				<option value="pm">Предыдущий месяц</option>
				<option value="3m">Последние 3 месяца</option>
			</select>
			</td>
		</tr>
		<tr>
			<td>Query</td>
			<td>
				<select id="QueryType">
					<option value="HourHeatmap">Daily hour Heatmap</option>
					<option value="Daily">Daily summary</option>					
					<option value="Operators">Daily by operators</option>
				</select>
		</tr>			
		<tr>
			<td>Report type</td>
			<td>
				<input type="radio" id="ReceivedCalls" name="reportType" value="ReceivedCalls" checked>
				<label for="ReceivedCalls">Received Calls</label>
				<br>
				<input type="radio" id="AnsweredCalls" name="reportType" value="AnsweredCalls">
				<label for="AnsweredCalls">Answered Calls</label>				
				<br>
				<input type="radio" id="UnansweredCalls" name="reportType" value="UnansweredCalls">
				<label for="UnansweredCalls">Unanswered Calls</label>
				<br>
				<input type="radio" id="SumTalkTime" name="reportType" value="SumTalkTime">
				<label for="SumTalkTime">Summary Talked Time in minutes</label>						
			</td>
		</tr>	
		<tr>
			<td>style</td>
			<td>
				<select id="style">
					<option value="bar">bar</option>
					<option value="bar-color">bar-color</option>
					<option value="bar-size">bar-size</option>
					
					<option value="dot">dot</option>
					<option value="dot-color">dot-color</option>
					<option value="dot-size">dot-size</option>
					<option value="dot-line">dot-line</option>
					
					<option value="line">line</option>
					<option value="grid">grid</option>
					<option value="grid-color">grid-color</option>
					<option value="surface">surface</option>
				</select>
		</tr>  
		<tr>
			<td>showPerspective</td>
			<td><input type="checkbox" id="showPerspective" checked /></td>
		</tr>
    </table>			
</p>
<div id="DataLoadStatus">Select Query</div>

<script type="text/javascript">
var URLBase='sql.php';

document.getElementById('QueryType').onchange = load_data;

load_data();

function load_data(){
	var QueryType=document.getElementById('QueryType').value;
	var URL=URLBase+'?query_type='+QueryType;

	document.getElementById('DataLoadStatus').innerHTML = 
		'Loading values for query: '+QueryType+'...';
	
	// load data via an ajax request.
	$.ajax({
		url: URL,
		success: function (data) {
			// hide the "loading..." message
			//document.getElementById('DataLoadStatus').style.display = 'none';
			document.getElementById('DataLoadStatus').innerHTML = 'Loaded: '+data.length+' values.';

			console.log('data.length: '+data.length);
	  
			//drawVisualization();
		prepare(data);
		},
		error: function (err) {
			console.log('Error', err);
			if (err.status === 0) {
				alert('Failed to load json.\nPlease run this example on a server.');
			}
			else {
				alert('Failed to load data/basic.json.');
			}
		}
});
}
</script>
<div id="mygraph"></div>

</body>
</html>
