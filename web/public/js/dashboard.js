$(function() {
    drawMainChart();

    $('#datepickerContainer input').datepicker({
        format: "yyyy-mm-dd",
        startView: 1,
        autoclose: true
    }).on('changeDate', function(ev){
        drawMainChart();
    });

    $('.granularityPicker').change(function(){
        drawMainChart();
    });
});

function getChartDataBitcoin() {
    return $.getJSON("/charts/getJsonData/bitcoin/" + getUrlAppendage());
}

function getChartDataTwitter() {
    return $.getJSON("/charts/getJsonData/twitter/" + getUrlAppendage());
}

function getChartDataTwitterAdvanced() {
    return $.getJSON("/charts/getJsonData/twitter-advanced/" + getUrlAppendage());
}

function getChartDataGoogle() {
    return $.getJSON("/charts/getJsonData/google/" + getUrlAppendage());
}

function getChartDataBitcoinAnalysis() {
    return $.getJSON("/charts/getJsonData/bitcoin-analysis/" + getUrlAppendage());
}

function getUrlAppendage() {
    return $('#fromDatePicker').val() + '/' + $('#toDatePicker').val() + '/' + $('input[name=granularityPicker]:checked').val()
}

function drawMainChart()
{
    $.when(getChartDataBitcoin(), getChartDataTwitter(), getChartDataTwitterAdvanced(), getChartDataGoogle(), getChartDataBitcoinAnalysis()).done(function(bitcoin, twitter, twitteradvanced, google, bitcoinanalysis){

        var datasets = {
            "bitcoin price": {
                label: "bitcoin price",
                data: bitcoin[0],
                yaxis: 1,
                color: 'orange'
            },
            "twitter sentiment simple": {
                label: "twitter sentiment simple",
                data: twitter[0],
                yaxis: 2,
                color: 'lightblue'
            },
            "twitter sentiment advanced": {
                label: "twitter sentiment advanced",
                data: twitteradvanced[0],
                yaxis: 3,
                color: 'blue'
            },
            "google search volume": {
                label: "google search volume",
                data: google[0],
                yaxis: 4,
                color: 'green'
            },
            "bitcoin value change": {
                label: "bitcoin value change",
                data: bitcoinanalysis[0],
                yaxis: 5,
                color: 'red'
            }
        };

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
                    }],
                    yaxes: [{
                        color: 'orange'
                    },
                    {
                        color: 'lightblue'
                    },
                    {
                        color: 'blue'
                    },
                    {
                        color: 'green'
                    },
                    {
                        color: 'red',
                        min: -400,
                        max: 400
                    }
                    ]
                });
            }
        }

        plotAccordingToChoices();
    });

}