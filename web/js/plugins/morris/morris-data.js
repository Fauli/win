$(function() {
    $.getJSON("http://151.236.222.251/win/web/getdata.php?set=google", function(jsonData){
    console.log(jsonData);
      Morris.Area({
          element: 'morris-area-chart',
          data: jsonData,
          xkey: 'x',
          ykeys: 'y',
          labels: 'Bitcoin Trend Google',
          pointSize: 2,
          hideHover: 'auto',
          resize: true
      });

    });

});
