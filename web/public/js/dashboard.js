$(function() {
    $.getJSON("/charts/getJsonData/bitcoin", function(data) {
        console.log(data);
        $.plot("#chart", [data]);
    });
    
});
