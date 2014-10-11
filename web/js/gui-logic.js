$('#toDatePicker').datepicker()
    .on('changeDate', function(ev){
        alert(ev.date.valueOf());
});

$('#fromDatePicker').datepicker()
    .on('changeDate', function(ev){
        alert(ev.date.valueOf());
});
