
function prepare(data){
var set = null;
var graph3d = null;
var startdate=data[0]['calldate'];//'2018-12-01';
var to_days_offset=data[0]['to_days'];	//non zero sometimes
var x_step=2;	//weekly
var y_step=1;

var keys=["номера операторов","2011","2012","2013","2014","2015","2016","2017","2018","2019"
			,"2105","6404","4400"
			,"2401","2402","2403","2404"];
var reportTypeValues=["ReceivedCalls","AnsweredCalls","UnansweredCalls","SumTalkTime"];

for (var i = 0; i < reportTypeValues.length; i++) {
	document.getElementById(reportTypeValues[i]).onchange = drawVisualization;
}

function dateToYMD(date) {
	var strArray=['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	var d = date.getDate();
	var m = strArray[date.getMonth()];
	var y = date.getFullYear();
	return '' + (d <= 9 ? '0' + d : d) + '-' + m + '-' + y;
}

function getXValueLabel(value) {
	var d = new Date(startdate);
	d.setDate(d.getDate() + value - to_days_offset);
	var result=d.getDate();
	return dateToYMD(d);
}

function myval(value){
	var i=keys.indexOf(value.toString());
	return i*y_step;
}

function myval_(value){
  for (var i=1; i<keys.length;i++){
    if(value==keys[i])
      return i*y_step;
  }
  return 0;
}

function getOperatorsLabel(value) {
	res=keys[value/y_step]
	return res ? res : '' ;
}  

function getTooltip(point) {
	// parameter point contains properties x, y, z
	var res='value: <b>' + point.z + '</b><br>'
			+'Hour: '+(point.y)+ '<br>'	
			+''+getXValueLabel(point.x)+ '';
	return res;
}

function getTooltipForOperators(point) {
	// parameter point contains properties x, y, z
	var res='Value: <b>' + point.z + '</b><br>'
			+'Operator: '+getOperatorsLabel(point.y)+ '<br>'	
			+''+getXValueLabel(point.x)+ '';
	return res;
}

  // Called when the Visualization API is loaded.
function drawVisualization() {	
    var style = document.getElementById('style').value;
	var QueryType=document.getElementById('QueryType').value;

	//get report type
	var reportType=reportTypeValues[0];	//default
	for (var i = 0; i < reportTypeValues.length; i++) {
		if(document.getElementById(reportTypeValues[i]).checked){
			reportType=reportTypeValues[i];
			break;
		}
	}	
	console.log('reportType: '+reportType);
	
    // Create and populate a data table.
    var set = new vis.DataSet();
    for (var i = 0; i < data.length; i++) {
		var x = parseInt(data[i]['to_days']);
		var y = parseInt(data[i]['y']);
		var z = parseInt(data[i][reportType]);
	  
		if(QueryType=='Operators')
			y = myval(y);
		
		set.add({
          x: x,
          y: y,
          z: z,
          style: y  
		});
    }
    
    // specify options
    var options = {
      width:  '100%',
      height: '100%',
      style: style,		//from documentElement
      //xmin:'0',
      //xmax:10,
      //zMax: '100',
      zMin: 0,
      xStep: x_step,
      yStep: y_step,
      //zStep: 20,
      //zStep: 1,
      xCenter: '50%',
      yCenter: '30%',
      showPerspective: (document.getElementById("showPerspective").checked ),
      showGrid: true,
      showLegend: true,
	  legendLabel: reportType,
	  legendStep: y_step,
	  //legendValueLabel: getOperatorsLabel,
      showShadow: false,
      keepAspectRatio: true,
      verticalRatio: 0.5,
      
	  // Option tooltip can be true, false, or a function returning a string with HTML contents
      //tooltip: true,
      tooltip: getTooltip,
      xValueLabel: getXValueLabel,
      //yValueLabel: getOperatorsLabel
    };
	console.log('QueryType: '+ QueryType);
	if(QueryType=='Operators'){
		options.legendValueLabel= getOperatorsLabel;
		options.yValueLabel= getOperatorsLabel;
		options.tooltip= getTooltipForOperators;
	}

    var camera = graph3d ? graph3d.getCameraPosition() : null;
    // create a graph3d
    var container = document.getElementById('mygraph');
    graph3d = new vis.Graph3d(container, set, options);
     
    //------------
    if (camera) graph3d.setCameraPosition(camera); // restore camera position
	else {
		var pos = {horizontal: 0.1, vertical: 0.5, distance: 2};
		graph3d.setCameraPosition(pos);
		var i;
	}
	  
	for (var i = 0; i < reportTypeValues.length; i++)
		document.getElementById(reportTypeValues[i]).onchange = drawVisualization;

    document.getElementById('style').onchange = drawVisualization;
	document.getElementById('showPerspective').onchange = drawVisualization;
}

drawVisualization();  
}  