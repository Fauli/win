$(function() {
    redrawMainChart();

    $('#toDatePicker').datepicker().on('changeDate', function(ev){
        redrawMainChart();
    });

    $('#fromDatePicker').datepicker().on('changeDate', function(ev){
        redrawMainChart();
    });
});

function ajax1() {
    return $.getJSON("/charts/getJsonData/bitcoin/" + $('#fromDatePicker').val() + '/' + $('#toDatePicker').val());
}

function ajax2() {
    return $.getJSON("/charts/getJsonData/twitter/" + $('#fromDatePicker').val() + '/' + $('#toDatePicker').val());
}

function ajax3() {
    return $.getJSON("/charts/getJsonData/google/" + $('#fromDatePicker').val() + '/' + $('#toDatePicker').val());
}

function redrawMainChart()
{
    $.when(ajax1(), ajax2(), ajax3()).done(function(bitcoin, twitter, google){

        var datasets = {
            "bitcoin": {
                label: "bitcoin",
                data: bitcoin[0],
		yaxis: 1
            },
            "twitter": {
                label: "twitter",
                data: twitter[0],
		yaxis: 2
            },
            "google": {
                label: "google",
                data: google[0],
		yaxis: 3
            }
        };

        var i = 0;
        $.each(datasets, function(key, val) {
            val.color = i;
            ++i;
        });

        var choiceContainer = $("#choices");
        if (!choiceContainer.html()) {
            $.each(datasets, function(key, val) {
                choiceContainer.append("<br/><input type='checkbox' name='" + key +
                        "' checked='checked' id='id" + key + "'></input>" +
                        "<label for='id" + key + "'>"
                        + val.label + "</label>");
            });
        }

        choiceContainer.find("input").click(plotAccordingToChoices);

        function plotAccordingToChoices() {

            var data = [];

            choiceContainer.find("input:checked").each(function () {
                var key = $(this).attr("name");
                if (key && datasets[key]) {
                    data.push(datasets[key]);
                }
            });

            if (data.length > 0) {
                $.plot("#chart", data, {
                    xaxes: [{
                        mode: "time",
                        timeformat: "%d.%b.%y"
                    }],
                    yaxes: [
			{position: "left", min: 0, max: 1300},
                        {position: "right", min: -2.5, max:2.5}, 
                        {position: "right", min: 0, max:100}
                    ]
                });
            }
        }

        plotAccordingToChoices();
    });
}
