$(function() {
    $.when(ajax1(), ajax2(), ajax3()).done(function(bitcoin, twitter, google){

        var datasets = {
            "bitcoin": {
                label: "bitcoin",
                data: bitcoin[0]
            },
            "twitter": {
                label: "twitter",
                data: twitter[0]
            },
            "google": {
                label: "google",
                data: google[0]
            }
        };

        var i = 0;
        $.each(datasets, function(key, val) {
            val.color = i;
            ++i;
        });

        // insert checkboxes 
        var choiceContainer = $("#choices");
        $.each(datasets, function(key, val) {
            choiceContainer.append("<br/><input type='checkbox' name='" + key +
                    "' checked='checked' id='id" + key + "'></input>" +
                    "<label for='id" + key + "'>"
                    + val.label + "</label>");
        });

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
                    yaxis: {
                        min: 0
                    },
                    xaxis: {
                        tickDecimals: 0,
                        mode: "time",
                        timeformat: "%d.%b.%y"
                    }
                });
            }
        }

        plotAccordingToChoices();
    });
});

function ajax1() {
    return $.getJSON("/charts/getJsonData/bitcoin");
}

function ajax2() {
    return $.getJSON("/charts/getJsonData/twitter");
}

function ajax3() {
    return $.getJSON("/charts/getJsonData/google");
}
