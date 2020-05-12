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
