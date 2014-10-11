$(function() {
    loadNotifications();
});


function loadNotifications(){
        //alert('notifications');
        $('#notificationBody').empty();

        $.getJSON("/notifications/getJsonData", function( data ) {
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