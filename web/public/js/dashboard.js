$(function() {
    drawMainChart();

    drawDailyChangeChart();

    $('#datepickerContainer input').datepicker({
        format: "yyyy-mm-dd",
        startView: 1,
        autoclose: true
    }).on('changeDate', function(ev){
        drawMainChart();
    });
});

function getChartDataBitcoin() {
    return $.getJSON("/charts/getJsonData/bitcoin/" + $('#fromDatePicker').val() + '/' + $('#toDatePicker').val());
}

function getChartDataTwitter() {
    return $.getJSON("/charts/getJsonData/twitter/" + $('#fromDatePicker').val() + '/' + $('#toDatePicker').val());
}

function getChartDataTwitterAdvanced() {
    return $.getJSON("/charts/getJsonData/twitter-advanced/" + $('#fromDatePicker').val() + '/' + $('#toDatePicker').val());
}

function getChartDataGoogle() {
    return $.getJSON("/charts/getJsonData/google/" + $('#fromDatePicker').val() + '/' + $('#toDatePicker').val());
}

function getChartDataBitcoinAnalysis() {
    return $.getJSON("/charts/getJsonData/bitcoin-analysis/" + $('#fromDatePicker').val() + '/' + $('#toDatePicker').val());
}

function drawMainChart()
{
    $.when(getChartDataBitcoin(), getChartDataTwitter(), getChartDataTwitterAdvanced(), getChartDataGoogle(), getChartDataBitcoinAnalysis()).done(function(bitcoin, twitter, twitteradvanced, google, bitcoinanalysis){

        var datasets = {
            "bitcoin price": {
                label: "bitcoin price",
                data: bitcoin[0],
                yaxis: 1
            },
            "twitter sentiment simple": {
                label: "twitter sentiment simple",
                data: twitter[0],
                yaxis: 2
            },
            "twitter sentiment advanced": {
                label: "twitter sentiment advanced",
                data: twitteradvanced[0],
                yaxis: 3
            },
            "google search volume": {
                label: "google search volume",
                data: google[0],
                yaxis: 4
            },
            "bitcoin value change": {
                label: "bitcoin value change",
                data: bitcoinanalysis[0],
                yaxis: 5
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
                var checked = '';
                if (key == 'bitcoin price' || key == 'google search volume') {
                    checked = "checked='checked'";
                }
                choiceContainer.append(" <input type='checkbox' name='" + key +
                        "' " + checked + " id='id" + key + "'></input> " +
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
                    }]

                });
            }
        }

        plotAccordingToChoices();
    });

}

function drawDailyChangeChart()
{
    $.getJSON("/charts/getJsonData/bitcoin-analysis/" + $('#fromDatePicker').val() + '/' + $('#toDatePicker').val(), function(data){
        if (data.length > 0) {
            $.plot("#chart-dailychange", [data], {
                xaxis: {
                    tickDecimals: 0,
                    mode: "time",
                    timeformat: "%d.%b.%y"
                }
            });
        }
    });
}

