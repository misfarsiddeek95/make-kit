'use strict';

(function ($) {
  'use strict';

  // Infoblock charts

  new Chart($('#infoblock-chart-1'), {
    type: 'line',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
      datasets: [{
        label: 'Dataset',
        data: [45, 40, 30, 20, 25, 35, 50],
        fill: true,
        backgroundColor: '#e53935',
        borderColor: '#e53935',
        borderWidth: 2,
        borderCapStyle: 'butt',
        borderDash: [],
        borderDashOffset: 0.0,
        borderJoinStyle: 'miter',
        pointBorderColor: '#e53935',
        pointBackgroundColor: '#fff',
        pointBorderWidth: 2,
        pointHoverRadius: 4,
        pointHoverBackgroundColor: '#e53935',
        pointHoverBorderColor: '#fff',
        pointHoverBorderWidth: 2,
        pointRadius: [0, 4, 4, 4, 4, 4, 0],
        pointHitRadius: 10,
        spanGaps: false
      }]
    },
    options: {
      scales: {
        xAxes: [{
          display: false
        }],
        yAxes: [{
          display: false,
          ticks: {
            min: 0,
            max: 60
          }
        }]
      },
      legend: {
        display: false
      }
    }
  });

  new Chart($('#infoblock-chart-2'), {
    type: 'line',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
      datasets: [{
        label: 'Dataset',
        data: [30, 22, 18, 25, 40, 55, 60],
        fill: true,
        backgroundColor: '#7d57c1',
        borderColor: '#7d57c1',
        borderWidth: 2,
        borderCapStyle: 'butt',
        borderDash: [],
        borderDashOffset: 0.0,
        borderJoinStyle: 'miter',
        pointBorderColor: '#7d57c1',
        pointBackgroundColor: '#fff',
        pointBorderWidth: 2,
        pointHoverRadius: 4,
        pointHoverBackgroundColor: '#7d57c1',
        pointHoverBorderColor: '#fff',
        pointHoverBorderWidth: 2,
        pointRadius: [0, 4, 4, 4, 4, 4, 0],
        pointHitRadius: 10,
        spanGaps: false
      }]
    },
    options: {
      scales: {
        xAxes: [{
          display: false
        }],
        yAxes: [{
          display: false,
          ticks: {
            min: 0,
            max: 60
          }
        }]
      },
      legend: {
        display: false
      }
    }
  });

  // Peity charts
  $('[data-chart="peity"]').each(function () {
    var type = $(this).attr('data-type');
    $(this).peity(type);
  });

  // Donut chart
  Morris.Donut({
    element: 'donut',
    data: [{
      label: "Android",
      value: 34

    }, {
      label: "iOS",
      value: 67
    }, {
      label: "Windows",
      value: 45
    }],
    resize: true,
    colors: ['#1d87e4', '#faa800', '#e53935']
  });

  // Vector map
  $('#vector-map').vectorMap({
    map: 'world_en',
    backgroundColor: null,
    borderColor: null,
    borderOpacity: 0.5,
    borderWidth: 1,
    color: '#1d87e4',
    enableZoom: true,
    hoverColor: '#1d87e4',
    hoverOpacity: 0.7,
    normalizeFunction: 'linear',
    selectedColor: '#faa800',
    selectedRegions: ['au', 'ca', 'de', 'br', 'in'],
    showTooltip: true
  });

  /* Bar chart */
  var data4 = [];
  for (var i = 0; i <= 6; i += 1) {
    data4.push([i, parseInt(Math.random() * 20)]);
  }

  var data5 = [];
  for (var o = 0; o <= 6; o += 1) {
    data5.push([o, parseInt(Math.random() * 20)]);
  }

  var data6 = [{
    label: "Data One",
    data: data4,
    bars: {
      order: 1
    }
  }, {
    label: "Data Two",
    data: data5,
    bars: {
      order: 2
    }
  }];

  $.plot($("#chart-bar"), data6, {
    bars: {
      show: true,
      barWidth: 0.2,
      fill: 1
    },
    series: {
      stack: 0
    },
    grid: {
      color: "#aaa",
      hoverable: true,
      borderWidth: 0,
      labelMargin: 5,
      backgroundColor: "#fff"
    },
    legend: {
      show: false
    },
    colors: ["#faa800", "#34a853"],
    tooltip: true, //activate tooltip
    tooltipOpts: {
      content: "%s : %y.0",
      shifts: {
        x: -30,
        y: -50
      }
    }
  });

  // Realtime chart
  $(function () {

    // We use an inline data source in the example, usually data would
    // be fetched from a server

    var data = [],
        totalPoints = 300;

    function getRandomData() {
      if (data.length > 0) {
        data = data.slice(1);
      }

      // Do a random walk

      while (data.length < totalPoints) {
        var prev = data.length > 0 ? data[data.length - 1] : 50,
            y = prev + Math.random() * 10 - 5;

        if (y < 5) {
          y = 5;
        } else if (y > 95) {
          y = 95;
        }

        data.push(y);
      }

      // Zip the generated y values with the x values

      var res = [];
      for (var i = 0; i < data.length; ++i) {
        res.push([i, data[i]]);
      }

      return res;
    }

    // Set up the control widget

    var updateInterval = 60;

    var plot = $.plot("#realtime", [getRandomData()], {
      series: {
        shadowSize: 0 // Drawing is faster without shadows
      },
      yaxis: {
        min: 0,
        max: 100
      },
      xaxis: {
        min: 0,
        max: 300
      },
      colors: ["#7d57c1"],
      grid: {
        color: "#aaa",
        hoverable: true,
        borderWidth: 0,
        backgroundColor: '#fff'
      },
      tooltip: true,
      tooltipOpts: {
        content: "Y: %y",
        defaultTheme: false
      }
    });

    function update() {
      plot.setData([getRandomData()]);

      // Since the axes don't change, we don't need to call plot.setupGrid()

      plot.draw();
      setTimeout(update, updateInterval);
    }

    update();
  });
})(jQuery);
