$(function() {

    // The chart GUI logic, needs adaption when new Sources come to play
    $('#toDatePicker').datepicker()
        .on('changeDate', function(ev){
            //alert(ev.date.valueOf());
    	makeMorrisArea($('#selectedSet').val(),$('#fromDatePicker').val(),$('#toDatePicker').val());
    });

    $('#fromDatePicker').datepicker()
        .on('changeDate', function(ev){
            //alert(ev.date.valueOf());
    	makeMorrisArea($('#selectedSet').val(),$('#fromDatePicker').val(),$('#toDatePicker').val());
    	//alert($('#selectedSet').val()+","+$('#fromDatePicker').val()+","+$('#toDatePicker').val());
    });

    jQuery("#bitcoinSetItem").click(function(e){
    //do something
    e.preventDefault();

    //alert("makeMorris(bitcoin,"+$('#fromDatePicker').val()+","+$('#toDatePicker').val()+")");
    makeMorrisArea("bitcoin",$('#fromDatePicker').val(),$('#toDatePicker').val());
    $('#selectedSet').val("bitcoin");
    });

    jQuery("#googleSetItem").click(function(e){
    //do something
    e.preventDefault();
    //alert('google');
    makeMorrisArea("google",$('#fromDatePicker').val(),$('#toDatePicker').val());
    $('#selectedSet').val("google");
    });

    jQuery("#twitterSetItem").click(function(e){
    //do something
    e.preventDefault();
    //alert('twitter');
    makeMorrisArea("twitter",$('#fromDatePicker').val(),$('#toDatePicker').val());
    $('#selectedSet').val("twitter");
    });

    jQuery("#allSetItem").click(function(e){
    //do something
    e.preventDefault();
    alert('all');
    $('#selectedSet').val("all");
    });

});