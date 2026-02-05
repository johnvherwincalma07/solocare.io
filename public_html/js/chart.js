document.addEventListener("DOMContentLoaded", function() {

  // ======== Additional Right Panel Charts ========
  const submissionTrendData = {
    labels: ['Jun','Jul','Aug','Sept','Oct','Nov','Dec'],
    datasets:[{
      label:'Applications Submitted',
      data:[0,0,0,0,0,1,0],
      borderColor:'#007bff',
      backgroundColor:'rgba(0,123,255,0.2)',
      tension:0.3
    }]
  };

  const approvalTrendData = {
    labels:['Jan','Feb','Mar','Apr','May','Jun','Jul'],
    datasets:[
      { label:'Approved', data:[8,12,7,10,20,15,18], borderColor:'#28a745', backgroundColor:'rgba(40,167,69,0.2)', fill:true, tension:0.3 },
      { label:'Rejected', data:[4,7,3,5,5,3,4], borderColor:'#dc3545', backgroundColor:'rgba(220,53,69,0.2)', fill:true, tension:0.3 }
    ]
  };

  const userTrendData = {
    labels:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
    datasets:[{ label:'Active Users', data:[3,5,7,4,6,8,10], borderColor:'#ffc107', backgroundColor:'rgba(255,193,7,0.2)', tension:0.3 }]
  };

  const submissionTrendRight = document.getElementById('submissionTrendChart')?.getContext('2d');
  const approvalTrendRight = document.getElementById('approvalTrendChartRight')?.getContext('2d');
  const userTrendRight = document.getElementById('userTrendChartRight')?.getContext('2d');

  if(submissionTrendRight) new Chart(submissionTrendRight, {type:'line', data:submissionTrendData, options:{responsive:true}});
  if(approvalTrendRight) new Chart(approvalTrendRight, {type:'line', data:approvalTrendData, options:{responsive:true}});
  if(userTrendRight) new Chart(userTrendRight, {type:'line', data:userTrendData, options:{responsive:true}});

});
