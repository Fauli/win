$(function() {
  makeMorrisArea("google");
});
 
function makeMorrisArea(set, from, to){
  $('#morris-area-chart').empty();
  if(typeof(set) == "undefined"){ return; }
  if(typeof(from) == "undefined"){ from = "2012-01-01"; }
  if(typeof(to) == "undefined"){ to = "2099-01-01"; }
  //$('#selectedSet').val(set);

  $.getJSON("http://151.236.222.251/win/web/getdata.php?set="
             +(typeof(set) != "undefined" ? set : "bitcoin")
             +(typeof(from) != "undefined" ? "&from="+from : "")
             +(typeof(to) != "undefined" ? "&to="+to : ""), 
             function(jsonData){
      // alert("adsf"+(typeof(from)!="undefined"&&"from="+from)+(typeof(to)!="undefined"&&"to"+to));
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
}
