$(function() {

    loadNotifications();

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
        e.preventDefault();

        //alert("makeMorris(bitcoin,"+$('#fromDatePicker').val()+","+$('#toDatePicker').val()+")");
        makeMorrisArea("bitcoin",$('#fromDatePicker').val(),$('#toDatePicker').val());
        $('#selectedSet').val("bitcoin");
    });

    jQuery("#googleSetItem").click(function(e){
        e.preventDefault();
        //alert('google');
        makeMorrisArea("google",$('#fromDatePicker').val(),$('#toDatePicker').val());
        $('#selectedSet').val("google");
    });

    jQuery("#twitterSetItem").click(function(e){
        e.preventDefault();
        //alert('twitter');
        makeMorrisArea("twitter",$('#fromDatePicker').val(),$('#toDatePicker').val());
        $('#selectedSet').val("twitter");
    });

    jQuery("#allSetItem").click(function(e){
        e.preventDefault();
        alert('all');
        $('#selectedSet').val("all");
    });

});

function loadNotifications(){
        //alert('notifications');
        $('#notificationBody').empty();

        $.getJSON("http://151.236.222.251/notifications/getJsonData", function( data ) {
                var items = [];
                $.each( data, function( key, val ) {
                        //items.push( "<li id='" + key + "'>" + val + "</li>" );
                        $('#notificationBody').append(
                                '<a href="#" class="list-group-item">'
                                +'<i class="fa fa-' + val.Glyph + ' fa-fw"></i>' + val.Message
                                +'<span class="pull-right text-muted small"><em>'+val.Date+'</em>'
                                +'</span></a>');

                });
        });

}