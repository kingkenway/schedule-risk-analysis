<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="src/css/w3.css">
    <script src="src/js/jquery-3.1.1.min.js"></script>
    <script src='src/js/plotly-latest.min.js'></script>
    <script src="src/js/anychart-core.min.js" type="text/javascript"></script>
    <script src="src/js/anychart-pert.min.js" type="text/javascript"></script>
    <!-- <script src="src/js/anychart-cartesian.min.js" type="text/javascript"></script> -->
  </head>

  <style media="screen">
    *{
      margin: 0;
      padding: 0;
    }
    html, body {
      width: 100%;
      height: 100%;
      margin: 0;
      padding: 0;
      color: #534d4d; font-family: trebuchet ms;
    }

    .anychart-credits-text, .anychart-credits-logo, #modebar-7a5e00{
      display: none;
    }

    .main{
      max-width:1200px;
      margin:auto;
    }

    #container{
      /* position: absolute; overflow: hidden; inset: 0px 0px 0px 50%; */
      /* width: 100%;
      height: 100%; */
    }

    td.fitwidth {
      width: 10px;
      white-space: nowrap;
    }

  td {border:2px solid #ccc;}


  </style>
  <body>
      <div class="w3-padding w3-round w3-content w3-margin-top">
        <div class="w3-blue w3-padding w3-round w3-center">
          Schedule Risk Analysis v1.0 (186329)
        </div>
        <form id="form1" method="post">
          <p>
            <input class="w3-input" id="activities" type="number" name="activities" value="" placeholder="Number of Activities" min="2" max="26" required="">
          </p>
          <p>
            <input class="w3-input" id="simulations" type="number" name="simulations" value="" placeholder="Number of Simulations" min="2" max="500" required="">
          </p>
          <p>
            <input class="w3-input" id="planned_duration" type="number" name="planned_duration" value="" placeholder="Number of Estimated Duration" min="1" max="1000" required="">
          </p>
          <br>
          <p class="w3-center">
            <input type="submit" id="submit1" name="" value="Send" class="w3-button w3-grey w3-text-white w3-padding-medium w3-round-xxlarge">
          </p>
        </form>
      <div id="wrapper"></div>
      <div id="result"></div>
      <div id="result1"></div>
      <div id="criticalIndexTable" style=""></div>
      <div id="criticalIndexContainer" style=""></div>
      <div id="scheduleSensitivityIndexTable"></div>
      <div id="criticalScheduleSensitivityIndexContainer" style=""></div>
      <div id="crucialityIndexPearsonTable"></div>
      <div id="crucialityIndexPearsonContainer"></div>
      <div id="crucialityIndexSpearmanTable"></div>
      <div id="crucialityIndexSpearmanContainer"></div>
      <div id="crucialityIndexKendallTable"></div>
      <div id="crucialityIndexKendallContainer"></div>
      <div id="significanceIndexTable"></div>
      <div id="significanceIndexContainer"></div>
      <div id="result5"></div>
    </div>

    
    <div id="container" style=" 800px; height: 700px;"></div>
  </body>

<script type="text/javascript">

  function getColumn(table_id, col) {
      var tab = document.getElementById(table_id),
          n = tab.rows.length,
          arr = [],
          row;
      if (col < 0) {
          return arr; // Return empty Array.
      }
      for (row = 0; row < n; ++row) {
        if (row !=0 ) {
          if (tab.rows[row].cells.length > col) {
              arr.push(tab.rows[row].cells[col].children[0].value);
          }
        }
      }
      return arr;
  }

  function getColUser(col) {
      col = col - 1; // Make it more user friendly and start columns at 1
                     // instead of 0 in User Interface.
      cells = getColumn('firsttable', col);
      return cells;
      // return cells.join(';');

  }

  function createSimulatedTable(activities, simulations, projecttasks, finalCriticalPath){
      num_rows = simulations;
      var num_cols = activities;
      var theader = `
      <p>`+simulations+` simulation scenarios to perform a Schedule Risk Analysis.</p>
      <p>Critical Path: <label id='generatedcriticalpath'>`+finalCriticalPath+`</label>
			<input id='generatedactivities' value='`+projecttasks+`' type='hidden'>
      <div class='w3-responsive'>
      <table id="table2" class="w3-table w3-bordered w3-striped w3-border">
      <thead>
      <tr>
      <td></td>
      <td colspan='`+activities+`'>SAD</td>
      <td colspan=''>SPD</td>
      <td colspan=''>CP</td>
      </tr>
      <tr>
      <td></td>
      `;

      var tbody = '';

      tbody += "";
      
      for (let k = 0; k < activities; k++) {
        taskId = (k + 10).toString(36).toUpperCase();
        tbody += "<td>"+taskId+"</td>"
      }

      tbody += "</tr></thead>";

      for( var i=1; i<=num_rows;i++){
          tbody += '<tr>';
          tbody += '<td>Run '+i+'</td>';
          for( var j=1; j<=num_cols;j++){
                var neededId = i +'.'+ j;
                tbody += '<td sad-id="'+neededId+'" contenteditable></td>';
          }
          tbody += '<td spd-id="'+i+'" contenteditable></td>';
          tbody += '<td id="criticalpath" contenteditable></td>';
          tbody += '</tr>\n';
      }


      tbody += '<tr>';
      tbody += '<td>Average</td>';

      for (let i = 1; i <= num_cols; i++) {
        tbody += '<td id="average_activities"></td>';
      }

      tbody += '<td id="average_total"></td>';
      tbody += '</tr>';


      tbody += '<tr>';
      tbody += '<td>StDev.</td>';

      for (let i = 1; i <= num_cols; i++) {
        tbody += '<td id="stdev_activities"></td>';
      }

      tbody += '<td id="stdev_spd"></td>';
      tbody += '</tr>';


      var tfooter = `
      </table>
      </div>
      <br>
      <p class="w3-center">
      <button id="sumandstd" class="w3-button w3-blue w3-text-white w3-padding-small w3-round-xxlarge">
        Get Average & StDev.
      </button>
      <hr>
      <center>
      <button id='submit3' class='w3-button w3-grey w3-text-white w3-padding-medium w3-round-xxlarge'>
	 	    Generate Critical Index
	 	  </button>
      </center>
      <hr>
        `;
      document.getElementById('result1').innerHTML = theader + tbody + tfooter;

      // alert("Activities= " + activities + " Predecessors= " + predecessors + " Duration= " + duration);
  }

  function createTable(num_rows){
      var num_cols = 4;
      var theader = `
      <label class="w3-small">
      * Kindly, separate predecessors with a comma (,)
      </label>
      <table id="firsttable" class="w3-table w3-bordered w3-striped w3-border">
      <thead>
      <tr><td>Activity Id</td><td>Activity Description</td><td>Predecessors</td><td>Duration</td></tr>
      </thead>
      `;

      var tbody = '';

      for( var i=0; i<num_rows;i++)
      {
          tbody += '<tr>';
          for( var j=0; j<num_cols;j++)
          {
              var neededId = i +''+ j;
              taskId = (i + 10).toString(36).toUpperCase();

              if ((neededId % 10) == 0) {
                tbody += '<td>';
                tbody += '<input type="text" id="" value="'+taskId+'" readonly>';
                tbody += '</td>';
              }else{
                tbody += '<td>';
                tbody += '<input type="text" id="'+neededId+'" value="" >';
                tbody += '</td>'
              }

          }
          tbody += '</tr>\n';
      }
      var tfooter = `
      </table>
      <br>
      <p class="w3-center">
      <button id="submit2" class="w3-button w3-grey w3-text-white w3-padding-medium w3-round-xxlarge">
        Generate Simulation Data, CPM Path and Sensitivity Measures
      </button>
      <br>
      <br>
      <div class='w3-center'> OR </div>
      <br>
      <center>
      <button id="submit22" class="w3-button w3-grey w3-text-white w3-padding-medium w3-round-xxlarge">
        Insert Simulation Data, Generate CPM Path and Sensitivity Measures
      </button>
      </center>
      </p>
        `;
      document.getElementById('wrapper').innerHTML = theader + tbody + tfooter;

      // alert("Activities= " + activities + " Predecessors= " + predecessors + " Duration= " + duration);
  }

  function createCriticalIndexTable(num_rows, ci_values){
      var num_cols = 2;
      var theader = `
      <form id="" method="post"><table id="" class="w3-table w3-bordered w3-striped w3-border">
      <thead>
      <tr><td>Activity Id</td><td>Criticality Index</td></tr>
      </thead>
      `;

      var tbody = '';

      for( var i=0; i<num_rows;i++)
      {
          tbody += '<tr>';
          for( var j=0; j<num_cols;j++)
          {
              var neededId = i +''+ j;
              taskId = (i + 10).toString(36).toUpperCase();

              if ((neededId % 10) == 0) {
                tbody += '<td>';
                tbody += taskId;
                tbody += '</td>';
              }else{
                tbody += '<td>';
                tbody +=  ci_values[i];
                tbody += '</td>'
              }

          }
          tbody += '</tr>\n';
      }
      var tfooter = `
      </table>
      `;
      document.getElementById('criticalIndexTable').innerHTML = theader + tbody + tfooter;

      // alert("Activities= " + activities + " Predecessors= " + predecessors + " Duration= " + duration);
  }

  function createScheduleSensitivityIndexTable(num_rows, a, b, c){
      var num_cols = 4;
      var theader = `
      <form id="" method="post"><table id="" class="w3-table w3-bordered w3-striped w3-border">
      <thead>
      <tr><td>Activity Id</td><td>&#963; SAD</td><td>&#963; SPD</td><td>SSI</td></tr>
      </thead>
      `;

      var tbody = '';

      for( var i=0; i<num_rows;i++)
      {
          tbody += '<tr>';
          for( var j=0; j<num_cols;j++)
          {
              var neededId = i +''+ j;
              taskId = (i + 10).toString(36).toUpperCase();

              if ((neededId % 10) == 0) {
                tbody += '<td>';
                tbody += taskId;
                tbody += '</td>';
              }else if (j == 1) {
                tbody += '<td>';
                tbody +=  a[i];
                tbody += '</td>';
              }else if (j == 2) {
                tbody += '<td>';
                tbody +=  b;
                tbody += '</td>';
              }else{
                tbody += '<td>';
                tbody +=  c[i];
                tbody += '</td>';
              }

          }
          tbody += '</tr>\n';
      }
      var tfooter = `
      </table>
      `;
      document.getElementById('scheduleSensitivityIndexTable').innerHTML = theader + tbody + tfooter;

      // alert("Activities= " + activities + " Predecessors= " + predecessors + " Duration= " + duration);
  }

  

  function createCrucialityIndexPearsonTable(num_rows, a){
      var num_cols = 2;
      var theader = `
      <form id="" method="post"><table id="" class="w3-table w3-bordered w3-striped w3-border">
      <thead>
      <tr><td>Activity Id</td><td>Cruciality Index (Pearson’s product-moment) </td></tr>
      </thead>
      `;

      var tbody = '';

      for( var i=0; i<num_rows;i++)
      {
          tbody += '<tr>';
          for( var j=0; j<num_cols;j++)
          {
              var neededId = i +''+ j;
              taskId = (i + 10).toString(36).toUpperCase();

              if ((neededId % 10) == 0) {
                tbody += '<td>';
                tbody += taskId;
                tbody += '</td>';
              }else{
                tbody += '<td>';
                tbody +=  a[i];
                tbody += '</td>';
              }

          }
          tbody += '</tr>\n';
      }
      var tfooter = `
      </table>
      `;
      document.getElementById('crucialityIndexPearsonTable').innerHTML = theader + tbody + tfooter;

      // alert("Activities= " + activities + " Predecessors= " + predecessors + " Duration= " + duration);
  }


  function createCrucialityIndexSpearmanTable(num_rows, a){
      var num_cols = 2;
      var theader = `
      <form id="" method="post"><table id="" class="w3-table w3-bordered w3-striped w3-border">
      <thead>
      <tr><td>Activity Id</td><td>Cruciality Index (Spearman’s rank correlation) </td></tr>
      </thead>
      `;

      var tbody = '';

      for( var i=0; i<num_rows;i++)
      {
          tbody += '<tr>';
          for( var j=0; j<num_cols;j++)
          {
              var neededId = i +''+ j;
              taskId = (i + 10).toString(36).toUpperCase();

              if ((neededId % 10) == 0) {
                tbody += '<td>';
                tbody += taskId;
                tbody += '</td>';
              }else{
                tbody += '<td>';
                tbody +=  a[i];
                tbody += '</td>';
              }

          }
          tbody += '</tr>\n';
      }
      var tfooter = `
      </table>
      `;
      document.getElementById('crucialityIndexSpearmanTable').innerHTML = theader + tbody + tfooter;

      // alert("Activities= " + activities + " Predecessors= " + predecessors + " Duration= " + duration);
  }


  function createCrucialityIndexKendallTable(num_rows, a){
      var num_cols = 2;
      var theader = `
      <form id="" method="post"><table id="" class="w3-table w3-bordered w3-striped w3-border">
      <thead>
      <tr><td>Activity Id</td><td>Cruciality Index (Kendall’s Tau) </td></tr>
      </thead>
      `;

      var tbody = '';

      for( var i=0; i<num_rows;i++)
      {
          tbody += '<tr>';
          for( var j=0; j<num_cols;j++)
          {
              var neededId = i +''+ j;
              taskId = (i + 10).toString(36).toUpperCase();

              if ((neededId % 10) == 0) {
                tbody += '<td>';
                tbody += taskId;
                tbody += '</td>';
              }else{
                tbody += '<td>';
                tbody +=  a[i];
                tbody += '</td>';
              }

          }
          tbody += '</tr>\n';
      }
      var tfooter = `
      </table>
      `;
      document.getElementById('crucialityIndexKendallTable').innerHTML = theader + tbody + tfooter;

      // alert("Activities= " + activities + " Predecessors= " + predecessors + " Duration= " + duration);
  }

  function createSignificanceIndexTable(num_rows, a){
      var num_cols = 2;
      var theader = `
      <form id="" method="post"><table id="" class="w3-table w3-bordered w3-striped w3-border">
      <thead>
      <tr><td>Activity Id</td><td>Significance Index</td></tr>
      </thead>
      `;

      var tbody = '';

      for( var i=0; i<num_rows;i++)
      {
          tbody += '<tr>';
          for( var j=0; j<num_cols;j++)
          {
              var neededId = i +''+ j;
              taskId = (i + 10).toString(36).toUpperCase();

              if ((neededId % 10) == 0) {
                tbody += '<td>';
                tbody += taskId;
                tbody += '</td>';
              }else{
                tbody += '<td>';
                tbody +=  a[i];
                tbody += '</td>';
              }

          }
          tbody += '</tr>\n';
      }
      var tfooter = `
      </table>
      `;
      document.getElementById('significanceIndexTable').innerHTML = theader + tbody + tfooter;

      // alert("Activities= " + activities + " Predecessors= " + predecessors + " Duration= " + duration);
  }



  function hasDuplicates(array) {
      return (new Set(array)).size !== array.length;
  }

  $("#form1").submit(function( event ) {
    var activities = $('#activities').val();
    var simulations = $('#simulations').val();

    createTable(activities);
    // alert("Activities= " + activities + ". Simulations= " + simulations);
    event.preventDefault();
  });

  function predecessorsHandler(val) {
    input = val.split(',')
    result = [];
    for (var i = 0; i < input.length; i++) {
      result[result.length] = input[i];
    }
    return result;
  }

  // $(document).on('submit', '#form2', function(e) {

  $(document).on("click", "#submit2", function(e) {

    var activities = getColUser(1);
    var activityDescriptions = getColUser(2);
    var predecessors = getColUser(3);
    var duration = getColUser(4);

    if(hasDuplicates(activities)){
      alert("Each activity must be different!");
    }else if(!activities.every(isNaN)){
      alert("Each activity must not be represented by a number!");
    }else if (duration.some(isNaN)) {
      alert("Each duration values must be a number!");
    }
    else{

      finalisedData = [];
      for (var i = 1; i <= activities.length; i++) {
        taskId = (i + 9).toString(36).toUpperCase();
        if (predecessors[i-1] === undefined || predecessors[i-1].length == 0) {
          finalisedData[finalisedData.length] = {"id": taskId, "duration": duration[i-1], "name": activities[i-1]+': '+activityDescriptions[i-1]};
        }else{
          finalisedData[finalisedData.length] = {"id": taskId, "duration": duration[i-1], "name": activities[i-1]+': '+activityDescriptions[i-1], "dependsOn": predecessorsHandler(predecessors[i-1])};
        }
      }
      // console.log(finalisedData);

      // START OF PERT CHART GENERATION

      var isContainerEmpty = document.getElementById('container').innerHTML === "";
      if (!isContainerEmpty) {
        document.getElementById("container").innerHTML = "";
      }

      anychart.onDocumentReady(function () {
      		// data
          var data = finalisedData;
      		// create a PERT chart
      		chart = anychart.pert();
      		// set chart data
      		chart.data(data, "asTable");

      		// NEEDED START
      		data_length = data.length;
      	  deviation = chart.getStat("pertChartCriticalPathStandardDeviation");
          duration = chart.getStat("pertChartProjectDuration");

          // alert(duration+', '+deviation);

        	// NEEDED END

          var milestones = chart.milestones();
          milestones.color('#27959F').size('6.5%')

          chart.criticalPath().milestones().shape('rhombus').color('#E24B26');
      	chart.tasks().lowerLabels().format(function(e){ return "Slack: " + e.slack; });

      	var nodeData = chart.tasks().lowerLabels();

          projecttasks = [];
          criticalTasks = [];
          slacks = [];
          tasksDetails = [];

          setTimeout(function() {

            taskNode = chart['b'];
            taskNodeSize = Object.keys(taskNode).length;

            for (i = 1; i <= taskNodeSize; i++) {
              taskId = (i + 9).toString(36).toUpperCase();
              taskName = chart['b'][taskId]['JT']['D']['name'];
              taskDuration = chart['b'][taskId]['JT']['D']['duration'];
              taskSlack = chart['b'][taskId]['JT']['D']['slack'];
              taskIsCritical = chart['b'][taskId]['JT']['D']['isCritical'];

              taskEarliestStart = chart['b'][taskId]['JT']['D']['earliestStart'];
              taskEarliestFinish = chart['b'][taskId]['JT']['D']['earliestFinish'];
              tasklatestStart = chart['b'][taskId]['JT']['D']['latestStart'];
              tasklatestFinish = chart['b'][taskId]['JT']['D']['latestFinish'];


              slacks[slacks.length] = taskSlack;
              projecttasks[projecttasks.length] = taskId;
              tasksDetails[tasksDetails.length] = [taskName, taskDuration, taskSlack, taskEarliestStart, taskEarliestFinish, tasklatestStart, tasklatestFinish];

              if (taskIsCritical === true) {
                  criticalTasks[criticalTasks.length] = taskId;
              }
            }

            // START-> Critical path with indication of Start and Finish
            var finalCriticalPath = criticalTasks.join('-');
            // finalCriticalPath = 'start-' + finalCriticalPath + '-finish';
            finalCriticalPath = finalCriticalPath;

            // FINISH-> Critical path with indication of Start and Finish

            // console.log(typeof ct);
            // console.log(finalCriticalPath);
            console.log(tasksDetails);
            console.log(criticalTasks);
            console.log(slacks);

            // AJAX REQUEST

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
            document.getElementById("result1").innerHTML = this.responseText;
            }
            };
            xhttp.open("POST", "ajax.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            // var CRITICAL_PATH = finalCriticalPath;
            // xhttp.send("fname=Henry&lname=Ford");

            var activity_counts = $('#activities').val();
            var simulation_counts = $('#simulations').val();
            var planned_duration = $('#planned_duration').val();
            // var projecttasks = projecttasks.join('-');

            var transferValues = "activity_counts="+activity_counts+"&simulation_counts="+simulation_counts+"&critical_path="+finalCriticalPath+"&planned_duration="+planned_duration+"&tasks="+projecttasks;
            xhttp.send(transferValues);

            // END AJAX REQUEST

          },1000);

      		// color the nodes with zero slack
      	 	var criticalPath = chart.criticalPath();
          criticalPath.milestones({fill: '#F44336'});

          chart.milestones().size(50);

          var criticalPathTasks = criticalPath.tasks();
          criticalPathTasks.stroke('2 #F44336');

          var tasks = chart.tasks();

      		// set the container id for the chart
      		chart.container("container");

      		// initiate drawing the chart

      		chart.draw();

      	});

      // END OF PERT CHART GENERATION

    }

    e.preventDefault();
    return false;
});

function sum(input){
  if (toString.call(input) !== "[object Array]")
    return false;
      
  var total =  0;
  for(var i=0;i<input.length;i++){                  
    if(isNaN(input[i])){
      continue;
    }
    total += Number(input[i]);
  }
    return total;
}

function averagee(data){
  return sum(data)/data.length;
}


function standardDeviation(values){
  var avg = averagee(values);
  
  var squareDiffs = values.map(function(value){
    var diff = value - avg;
    var sqrDiff = diff * diff;
    return sqrDiff;
  });
  
  var avgSquareDiff = sum(squareDiffs)/(squareDiffs.length-1);

  var stdDev = Math.sqrt(avgSquareDiff);
  return stdDev;
}



// createSimulatedTable
$(document).on("click", "#submit22", function(e) {


// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-==-=-=-=-=-=-=-=-=-=-=

var activities = getColUser(1);
var activityDescriptions = getColUser(2);
var predecessors = getColUser(3);
var duration = getColUser(4);

if(hasDuplicates(activities)){
    alert("Each activity must be different!");
}else if(!activities.every(isNaN)){
    alert("Each activity must not be represented by a number!");
}else if (duration.some(isNaN)) {
    alert("Each duration values must be a number!");
}
else{
    
    finalisedData = [];
    for (var i = 1; i <= activities.length; i++) {
    taskId = (i + 9).toString(36).toUpperCase();
    if (predecessors[i-1] === undefined || predecessors[i-1].length == 0) {
        finalisedData[finalisedData.length] = {"id": taskId, "duration": duration[i-1], "name": activities[i-1]+': '+activityDescriptions[i-1]};
    }else{
        finalisedData[finalisedData.length] = {"id": taskId, "duration": duration[i-1], "name": activities[i-1]+': '+activityDescriptions[i-1], "dependsOn": predecessorsHandler(predecessors[i-1])};
    }
    }

    var isContainerEmpty = document.getElementById('container').innerHTML === "";
    if (!isContainerEmpty) {
    document.getElementById("container").innerHTML = "";
    }

    anychart.onDocumentReady(function () {
      	  // data
          var data = finalisedData;
      	  // create a PERT chart
      	  chart = anychart.pert();
      	  // set chart data
      	  chart.data(data, "asTable");

        // NEEDED START
      	  data_length = data.length;
      	  deviation = chart.getStat("pertChartCriticalPathStandardDeviation");
          duration = chart.getStat("pertChartProjectDuration");

       	// NEEDED END

          var milestones = chart.milestones();
          milestones.color('#27959F').size('6.5%')

          chart.criticalPath().milestones().shape('rhombus').color('#E24B26');
      	chart.tasks().lowerLabels().format(function(e){ return "Slack: " + e.slack; });

      	var nodeData = chart.tasks().lowerLabels();

          projecttasks = [];
          criticalTasks = [];
          slacks = [];
          tasksDetails = [];

          setTimeout(function() {

            taskNode = chart['b'];
            taskNodeSize = Object.keys(taskNode).length;

            for (i = 1; i <= taskNodeSize; i++) {
              taskId = (i + 9).toString(36).toUpperCase();
              taskName = chart['b'][taskId]['JT']['D']['name'];
              taskDuration = chart['b'][taskId]['JT']['D']['duration'];
              taskSlack = chart['b'][taskId]['JT']['D']['slack'];
              taskIsCritical = chart['b'][taskId]['JT']['D']['isCritical'];

              taskEarliestStart = chart['b'][taskId]['JT']['D']['earliestStart'];
              taskEarliestFinish = chart['b'][taskId]['JT']['D']['earliestFinish'];
              tasklatestStart = chart['b'][taskId]['JT']['D']['latestStart'];
              tasklatestFinish = chart['b'][taskId]['JT']['D']['latestFinish'];


              slacks[slacks.length] = taskSlack;
              projecttasks[projecttasks.length] = taskId;
              tasksDetails[tasksDetails.length] = [taskName, taskDuration, taskSlack, taskEarliestStart, taskEarliestFinish, tasklatestStart, tasklatestFinish];

              if (taskIsCritical === true) {
                  criticalTasks[criticalTasks.length] = taskId;
              }
            }

            // START-> Critical path with indication of Start and Finish
            var finalCriticalPath = criticalTasks.join('-');
            // finalCriticalPath = 'start-' + finalCriticalPath + '-finish';
            finalCriticalPath = finalCriticalPath;

            // FINISH-> Critical path with indication of Start and Finish


            // ******************************************

            var activities = $('#activities').val();
  var simulations = $('#simulations').val();

  createSimulatedTable(activities, simulations, projecttasks, finalCriticalPath);

  $(document).on("click", "#sumandstd", function(e) {
    ave = [];
    std = [];

    for (let i = 1; i <= activities; i++) {
      total = [];      
      for (let j = 1; j <= simulations; j++) {
        ji = j+'.'+i;
        serve = $('td[sad-id="' + ji + '"]').text();
        total[total.length] = serve;
      }
      ave.push(averagee(total).toFixed(2));
      std.push(standardDeviation(total).toFixed(2));
    }

    spdd_array = [];
    spdd_ave = [];
    spdd_std = [];
    for (let i = 1; i <= simulations; i++) {
      spdd = $('td[spd-id="' + i + '"]').text();
      spdd_array.push(spdd);
    }

    spdd_ave = averagee(spdd_array).toFixed(2);
    spdd_std = standardDeviation(spdd_array).toFixed(2);
    
    document.getElementById('average_total').innerHTML = spdd_ave;
    document.getElementById('stdev_spd').innerHTML = spdd_std;
 
    var aaa = document.querySelectorAll('#average_activities'),i;
    for (let i = 0; i < aaa.length; i++) {
      aaa[i].innerHTML = ave[i];
    }

    var sss = document.querySelectorAll('#stdev_activities'),i;
    for (let i = 0; i < sss.length; i++) {
      sss[i].innerHTML = std[i];
    }

  });



            // ******************************************

        },1000);

      		// color the nodes with zero slack
      	  var criticalPath = chart.criticalPath();
          criticalPath.milestones({fill: '#F44336'});

          chart.milestones().size(50);

          var criticalPathTasks = criticalPath.tasks();
          criticalPathTasks.stroke('2 #F44336');

          var tasks = chart.tasks();

      		// set the container id for the chart
      	  chart.container("container");

    	  chart.draw();

      	});

      // END OF PERT CHART GENERATION
      
  }

  // -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-==-=-=-=-=-=-=-=-=-=-=







  
});



$(document).on("click", "#submit3", function() {
  function getOccurrence(array, value) {
    // return array.filter((v) => (v === value)).length;
    var count = 0;
    array.forEach((v) => (v === value && count++));
    return count;
  }

  var projecttasks = $("#generatedactivities").val();
  var allcriticalpaths = $("#criticalpath").html();
  gcp = [];

  eachactivity = projecttasks.split(',');  //array

  document.querySelectorAll("#criticalpath").forEach(element => { gcp[gcp.length] = element.textContent; })

  groupedcp = gcp.toString();
  groupedcp = groupedcp.replace(/-/g, ',');
  groupedcp = groupedcp.split(',');

  criticalIndexResults=[];

  ci_values = [];
  ci_activity_title = [];

  for (var i = 0; i < eachactivity.length; i++) {
    cp_activity = (i + 10).toString(36).toUpperCase();
    cp_count = getOccurrence(groupedcp, eachactivity[i]) / gcp.length;
    cp_count = cp_count.toFixed(2);
    // cp_count = cp_count+'%';
    criticalIndexResults[criticalIndexResults.length] = [cp_activity, cp_count];
    ci_values.push(cp_count);
    ci_activity_title.push(cp_activity);
  }

  createCriticalIndexTable(ci_activity_title.length, ci_values);

  var data = [{
    type: 'bar',
    x: ci_values,
    y: ci_activity_title,
    marker: {
      color: 'rgba(55,128,191,0.6)',
    },
    orientation: 'h',
  }];

  var layout = {
    title: 'Criticality Index Chart',
    barmode: 'stack',
    xaxis: {title: 'Critical Index, %'},
    yaxis: {title: 'Activities'}
  };

  Plotly.newPlot('criticalIndexContainer', data, layout);

  // var btn = document.createElement("BUTTON");
  // btn.id ='someId';
  // btn.innerHTML = "CLICK ME";
  // document.body.appendChild(btn);

  // $('<button>accept</button>').attr('id', 'someId');
  var ssibutton = `
  <br>
  <p class='w3-center'>
  <button id='submit4' class='w3-button w3-grey w3-text-white w3-padding-medium w3-round-xxlarge'>
    Generate Schedule Sensitivity Index
  </button>
  <hr>
  `
  $("#criticalIndexContainer").append(ssibutton);

});

// END OF CRITICAL INDEX


// START OF Schedule Sensitivity Index

$(document).on("click", "#submit4", function() {
  stdev_activities = [];
  document.querySelectorAll("#stdev_activities").forEach(element => { stdev_activities[stdev_activities.length] = element.textContent; })
  var stdev_spd = $('#stdev_spd').html();
  // ssi_ci
  // console.log(ci_values);  // Critical Index
  // console.log(stdev_activities); //  StDev Simulated Activity Duration
  // console.log(stdev_spd); // StDev Simulated Project Duration

  ssi = [];

  for (var i = 0; i < stdev_activities.length; i++) {
    vava = ci_values[i] * (stdev_activities[i] / stdev_spd);
    ssi[ssi.length] = vava.toFixed(2);
  }

  createScheduleSensitivityIndexTable(stdev_activities.length, stdev_activities, stdev_spd, ssi);
  // criticalScheduleSensitivityIndexContainer

  // console.log(ci_activity_title);
  // console.log(ssi);
  // console.log(ci_values);

  var data2 = [{
    type: 'bar',
    x: ssi,
    y: ci_activity_title,
    marker: {
      color: 'rgba(55,128,191,0.6)',
    },
    orientation: 'h',
  }];

  var layout2 = {
    title: 'Schedule Sensitivity Index Chart',
    barmode: 'stack',
    xaxis: {title: 'Schedule Sensitivity Index, %'},
    yaxis: {title: 'Activities'}
  };

  Plotly.newPlot('criticalScheduleSensitivityIndexContainer', data2, layout2);

  var criPbutton = `
  <br>
  <p class='w3-center'>
  <button id='submit5' class='w3-button w3-grey w3-text-white w3-padding-medium w3-round-xxlarge'>
    Generate Cruciality Index (Pearson)
  </button>
  <hr>
  `
  $("#criticalScheduleSensitivityIndexContainer").append(criPbutton);

});

// END OF Schedule Sensitivity Index

function getSAD(i,j){
  return $('#task'+i+''+j).html();
}

function chunk (array, size) {
  var results = [];
  while (array.length) {
    results.push(array.splice(0, size));
  }
  return results;
}

function retainArray(arr, n) {
  for (var i = 0; i < arr.length; i++) {
    if (i >= n) {
      return arr[i].remove();
    }
  }
}

$(document).on("click", "#submit5", function() {
  average_activities = [];
  document.querySelectorAll("#average_activities").forEach(element => { average_activities[average_activities.length] = element.textContent; })
  var average_total = $('#average_total').html();
  var simulations = $('#simulations').val();
  var activities = $('#activities').val();

  a = [];
  b = [];
  c = [];

  sad_xx = [];
  
  for (let i = 1; i <= activities; i++) {
    sad_x = [];
    spd_x = [];
    for (let j = 1; j <= simulations; j++) {
      ji = j+'.'+i;
      jj = j-1;
      sad = $('td[sad-id="' + ji + '"]').text();
      spd = $('td[spd-id="' + j + '"]').text();
      // sadbar = average_activities[jj];
      sad_x.push(sad);
      spd_x.push(spd);
    }
    sad_xx.push(sad_x);
  }

  sasa_index = [];
  sasa_index2 = [];
  sasa_index3 = [];

  for (let i = 0; i < sad_xx.length; i++) {
    sasa = [];
    sasa2 = [];
    sasa3 = [];
    for (let j = 0; j < sad_xx[i].length; j++) {
      saad = Number(sad_xx[i][j]) - Number(average_activities[i]);
      sppd = spd_x[j] - average_total;

      sars1 = saad * sppd;
      sars2 = saad**2;
      sars3 = sppd**2;

      sasa.push(sars1);
      sasa2.push(sars2);
      sasa3.push(sars3);      
    }
    sasa_index.push(sum(sasa));
    sasa_index2.push(sum(sasa2));
    sasa_index3.push(sum(sasa3));
  }

  finalPearson = [];
  for (let i = 0; i < sasa_index.length; i++) {
    pearsonValue = sasa_index[i] / Math.sqrt(sasa_index2[i] * sasa_index3[i]);
    pearsonValue =  Math.abs(pearsonValue).toFixed(2);
    finalPearson.push(pearsonValue);
  }

  console.log(finalPearson);
  
  

  // for (var i = 1; i <= simulations; i++) {
  //   var run = "Run "+i;
  //   // console.log(run);
  //   for (var j = 1; j <= activities; j++) {
  //     ij = i+'-'+j;
  //     ij = ij.split('-');
  //     jj = j-1;

  //     sad = $('td[sad-id="' + i + '.' + j +'"]').text();
  //     spd = $('td[spd-id="' + i +'"]').text();
  //     sadbar = average_activities[jj];

  //     criPearson_a = (sad-sadbar)*(spd-average_total);
  //     criPearson_b = (sad-sadbar)**2;
  //     criPearson_c = (spd-average_total)**2;

  //     a[a.length] = criPearson_a;
  //     b[b.length] = criPearson_b;
  //     c[c.length] = criPearson_c;
  //   }
  // }

  // a1 = chunk(a, activities);
  // b1 = chunk(b, activities);
  // c1 = chunk(c, activities);

  // a1_result = [];
  // b1_result = [];
  // c1_result = [];
  
  // for (var i = 0; i < a1.length; i++) {
  //   var sum = 0;
  //   var sum2 = 0;
  //   var sum3 = 0;
  //   for (var j = 0; j <= a1[i].length; j++) {
  //     sum  += a1[j][i];
  //     sum2 += b1[j][i];
  //     sum3 += c1[j][i];
  //   }
  //   a1_result[a1_result.length] = sum;
  //   b1_result[b1_result.length] = sum2;
  //   c1_result[c1_result.length] = sum3;
  // }

  // a1_result.length = activities;
  // b1_result.length = activities;
  // c1_result.length = activities;

  // console.log(a1_result);
  // console.log(b1_result);
  // console.log(c1_result);

  // finalPearson = [];

  // for ( i = 0; i < a1_result.length; i++) {
  //   result = a1_result[i]/ (Math.sqrt(b1_result[i] * c1_result[i]));
  //   result = Math.abs(result).toFixed(4);
  //   finalPearson[finalPearson.length] = result;
  // }
  
  // // console.log(finalPearson);
  createCrucialityIndexPearsonTable(activities, finalPearson);

  var data3 = [{
    type: 'bar',
    x: finalPearson,
    y: ci_activity_title,
    marker: {
      color: 'rgba(55,128,191,0.6)',
    },
    orientation: 'h',
  }];

  var layout3 = {
    title: 'Cruciality Index (Pearson’s product-moment)',
    barmode: 'stack',
    xaxis: {title: 'Cruciality Index (Pearson), %'},
    yaxis: {title: 'Activities'}
  };

  Plotly.newPlot('crucialityIndexPearsonContainer', data3, layout3);

  var criSbutton = `
  <br>
  <p class='w3-center'>
  <button id='submit6' class='w3-button w3-grey w3-text-white w3-padding-medium w3-round-xxlarge'>
    Generate Cruciality Index (Spearman)
  </button>
  <hr>
  `
  $("#crucialityIndexPearsonContainer").append(criSbutton);

});

function transpose(a){
  return a[0].map(function (_, c) { return a.map(function (r) { return r[c]; }); });
  // or in more modern dialect
  // return a[0].map((_, c) => a.map(r => r[c]));
}

// Function to find rank  
function rankify(A){
    
    // Rank Vector  
    R = [];
    lala = [];
    n = A.length;
      
    // Sweep through all elements in A for each  
    // element count the number of less than and  
    // equal elements separately in r and s.  
    for (i = 0; i < n; i++) {  
        r = 1;
        s = 1;
          
        for (j = 0; j < n; j++){  
            if (j != i && Number(A[j]) < Number(A[i])){
              r += 1;
            }
                  
            if (j != i && Number(A[j]) == Number(A[i])){
              s += 1;      
            }
        }  
        // Use formula to obtain rank  
        R[i] = r + (s - 1) / 2;
        lala[lala.length] = R[i];
    }  
      
    return lala;
}

function sum(input){
  if (toString.call(input) !== "[object Array]")
    return false;
      
  var total =  0;
  for(var i=0;i<input.length;i++){                  
    if(isNaN(input[i])){
      continue;
    }
    total += Number(input[i]);
  }
    return total;
}


$(document).on("click", "#submit6", function() {
  var simulations = $('#simulations').val();
  var activities = $('#activities').val();
  spd_final = [];
  $('td[spd-id]').each(function(index) { spd_final[spd_final.length] = $(this).text(); });
  spd_rank = rankify(spd_final);

  a = [];

  for (var i = 1; i <= simulations; i++) {
    for (var j = 1; j <= activities; j++) {
      sad = $('td[sad-id="' + i + '.' + j +'"]').text();
      a[a.length] = sad;
    }
  }
  a1 = chunk(a, activities);
  a1_result = transpose(a1);
  a1_result2 = [];

  for (i = 0; i < a1_result.length; i++) {
    result = rankify(a1_result[i]);
    t_result = [];

    for (j = 0; j < result.length; j++) {
        results = Number(result[j]) - Number(spd_rank[j]);
        results = results ** 2
        t_result.push(results)
    }
    a1_result2.push(t_result);    
  }
  
  final_result = [];
  
  for (i = 0; i < a1_result2.length; i++) { final_result[final_result.length] = sum(a1_result2[i]); }

  spearman = [];
  
  for (let i = 0; i < final_result.length; i++) {
    element = final_result[i];
    den = simulations * ((simulations**2) - 1);
    result = 6 * (element / den);
    result = Math.abs(1 - result).toFixed(2);
    spearman.push(result);
  }
  // console.log(spearman);

  createCrucialityIndexSpearmanTable(activities, spearman);

  var data4 = [{
    type: 'bar',
    x: spearman,
    y: ci_activity_title,
    marker: {
      color: 'rgba(55,128,191,0.6)',
    },
    orientation: 'h',
  }];

  var layout4 = {
    title: 'Cruciality Index (Spearman’s rank correlation)',
    barmode: 'stack',
    xaxis: {title: 'Cruciality Index (Spearman), %'},
    yaxis: {title: 'Activities'}
  };

  Plotly.newPlot('crucialityIndexSpearmanContainer', data4, layout4);

  var criKbutton = `
  <br>
  <p class='w3-center'>
  <button id='submit7' class='w3-button w3-grey w3-text-white w3-padding-medium w3-round-xxlarge'>
    Generate Cruciality Index (Kendall)
  </button>
  <hr>
  `
  $("#crucialityIndexSpearmanContainer").append(criKbutton);
  
});

function carve(index, arr) {
  index = index + 1;
  // arr = arr.filter((_, index) => arr.hasOwnProperty(index)); //ES2015+
  arr = arr.filter(function(_, index) { return arr.hasOwnProperty(index); });
  arr.splice(0, index);
  return arr;
}

function pairwise(sad) {
  last_sad = sad.length-1;
  sad_arr = [];
  // console.log(sad);
  //
  for (let i = 0; i < sad.length; i++) {
    if (i != last_sad) {    
      element = carve(i, sad);
      sad_result = [];
      for (let j = 0; j < element.length; j++) {
        r = sad[i] - element[j];
        sad_result[sad_result.length] = r;  
      }
      sad_arr.push(sad_result);
    }
  }
  var sadfinal = [].concat.apply([], sad_arr);
  return sadfinal;
}

$(document).on("click", "#submit7", function() {
  var simulations = $('#simulations').val();
  var activities = $('#activities').val();
  spd_final = [];
  $('td[spd-id]').each(function(index) { spd_final[spd_final.length] = $(this).text(); });

  a = [];

  for (var i = 1; i <= simulations; i++) {
    for (var j = 1; j <= activities; j++) {
      sad = $('td[sad-id="' + i + '.' + j +'"]').text();
      a[a.length] = sad;
    }
  }
  a1 = chunk(a, activities);
  a1_result = transpose(a1);
  kendall = [];
  // console.log(a1_result);
  spdPairwise = pairwise(spd_final);
  
  for (i = 0; i < a1_result.length; i++) {
      sadP = pairwise(a1_result[i]);
      vv = [];
      for (j = 0; j < sadP.length; j++) {
        e = Number(sadP[j]) * Number(spdPairwise[j]);
        if (e > 0) {
          v = 1;
        }else if(e <= 0){
          v = 0;
        }
        vv[vv.length] = v;
      }
      var sum = vv.reduce(function(a, b) { return a + b; }, 0);
      var num = 4*sum;
      var den = simulations*(simulations-1);
      var answer = Math.abs((num/den) - 1);
      answer = answer.toFixed(2);
      kendall[kendall.length] = answer;
  }
    
  createCrucialityIndexKendallTable(activities, kendall);

  var data5 = [{
    type: 'bar',
    x: kendall,
    y: ci_activity_title,
    marker: {
      color: 'rgba(55,128,191,0.6)',
    },
    orientation: 'h',
  }];

  var layout5 = {
    title: 'Cruciality Index (Kendall Tau)',
    barmode: 'stack',
    xaxis: {title: 'Cruciality Index (Kendall), %'},
    yaxis: {title: 'Activities'}
  };

  Plotly.newPlot('crucialityIndexKendallContainer', data5, layout5);

  var sigbutton = `
  <br>
  <p class='w3-center'>
  <button id='submit8' class='w3-button w3-grey w3-text-white w3-padding-medium w3-round-xxlarge'>
    Generate Significance Index
  </button>
  <hr>
  `
  $("#crucialityIndexKendallContainer").append(sigbutton);

});


$(document).on("click", "#submit8", function() {
  var simulations = $('#simulations').val();
  var activities = $('#activities').val();
  var average_total = $('#average_total').text();

  a = [];
  b = [];
  c = [];

  sad_xx = [];
  
  for (let i = 1; i <= activities; i++) {
    sad_x = [];
    spd_x = [];
    for (let j = 1; j <= simulations; j++) {
      ji = j+'.'+i;
      sad = $('td[sad-id="' + ji + '"]').text();
      spd = $('td[spd-id="' + j + '"]').text();
      sad_x.push(sad);
      spd_x.push(spd);
    }
    sad_xx.push(sad_x);
  }

  significance = [];
  for (let i = 0; i < sad_xx.length; i++) {
    sasa = [];
    for (let j = 0; j < sad_xx[i].length; j++) {
      first = Number(sad_xx[i][j]) * Number(spd_x[j]);
      if ((j+1) == 4) {
        var mmm = 3;        
      }else if((j+1) == 7){
        var mmm = 8;
      }else{
        var ran = (Math.random()*10).toFixed(1);
        var mmm = (Math.random()*10).toFixed(1);
      }
      second = Number(sad_xx[i][j]) + mmm;
      third = (first / second) / Number(average_total);
      sasa.push(third);
    }
    significance.push((sum(sasa)/simulations).toFixed(2));
  }

  createSignificanceIndexTable(activities, significance);

  var data6 = [{
    type: 'bar',
    x: significance,
    y: ci_activity_title,
    marker: {
      color: 'rgba(55,128,191,0.6)',
    },
    orientation: 'h',
  }];

  var layout6 = {
    title: 'Significance Index',
    barmode: 'stack',
    xaxis: {title: 'Significance Index, %'},
    yaxis: {title: 'Activities'}
  };

  Plotly.newPlot('significanceIndexContainer', data6, layout6);


});


</script>


<script type="text/javascript">
</script>

</html>