$(document).ready(function(){

    chart = new Highcharts.Chart({
        chart: {
            renderTo: 'chartContainer',
            type: 'column',
            events: {
                load: function(){
                    setTimeout(load, 1000);
                }
            },
            height: 480,
            spacingTop: 32,
            marginTop: 96,
            spacingBottom: 32,
            marginBottom: 128,
            style: {
              fontFamily: '"ronnia", sans-serif',
            }
        },
        plotOptions: {
            column:{
                colorByPoint: true
            }
        },
        credits: {
            enabled: false,
            text: 'NetGen',
            href: 'http://netgenlabs.com'
        },
        colors: ["#1678b9"],
        title: {
            text: eventNameHighchart,
            style: {
              "color" : "#404041",
              "font-weight" : "600",
              "font-size" : "2em"
            }
        },
        tooltip: {
          enabled: false
        },
        xAxis: {
            type: 'String',
            tickPixelInterval: 5,
            // lineColor: "#ffffff",
            lineColor: "#ddd",
            lineWidth: 1,
            tickColor: "#ffffff",
            labels: {
              padding: 16,
              style: {
                "color" : "#404041",
                "font-size" : "14px",
                "font-weight" : "400",
              }
            }
        },
        yAxis: {
            minPadding: 0.1,
            maxPadding: 0.1,
            tickmarkPlacement: 'on',
            showFirstLabel: false, 
            min: 0,
            max: 5,
            title: {
                text: 'Average number of rating score',
                enabled: false
            },
            labels: {
              enabled: false,
              style: {
                "color" : "#404041",
                "font-size" : "18px",
                "font-weight" : "600"
              }
            },
            gridLineColor: "#ddd"
        },
        series: [
            {
                showInLegend: false,
                data:[],
                dataLabels: {
                    enabled: true,
                    rotation: 0,
                    formatter: function() {
                        return this.y;
                    },
                    inside: true,
                    shadow: false,
                    color: "#ffffff",
                    style: {
                        fontSize: '20px'
                    }
                }
            }]
    });

});