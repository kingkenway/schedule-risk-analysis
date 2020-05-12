<script>

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

</script>