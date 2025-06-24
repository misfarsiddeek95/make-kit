'use strict';

(function ($) {
  'use strict';

  // Tiles charts

  new Chart($('#tile-chart-1'), {
    type: 'line',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
      datasets: [{
        label: 'Dataset',
        data: [30, 22, 18, 25, 40, 55, 60],
        fill: true,
        backgroundColor: '#1d87e4',
        borderColor: '#1d87e4',
        borderWidth: 2,
        borderCapStyle: 'butt',
        borderDash: [],
        borderDashOffset: 0.0,
        borderJoinStyle: 'miter',
        pointBorderColor: '#1d87e4',
        pointBackgroundColor: '#fff',
        pointBorderWidth: 2,
        pointHoverRadius: 4,
        pointHoverBackgroundColor: '#1d87e4',
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

  new Chart($('#tile-chart-2'), {
    type: 'line',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
      datasets: [{
        label: 'Dataset',
        data: [50, 45, 30, 20, 25, 35, 50],
        fill: true,
        backgroundColor: '#faa800',
        borderColor: '#faa800',
        borderWidth: 2,
        borderCapStyle: 'butt',
        borderDash: [],
        borderDashOffset: 0.0,
        borderJoinStyle: 'miter',
        pointBorderColor: '#faa800',
        pointBackgroundColor: '#fff',
        pointBorderWidth: 2,
        pointHoverRadius: 4,
        pointHoverBackgroundColor: '#faa800',
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

  new Chart($('#tile-chart-3'), {
    type: 'line',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
      datasets: [{
        label: 'Dataset',
        data: [30, 22, 18, 25, 40, 55, 60],
        fill: false,
        backgroundColor: '#34a853',
        borderColor: '#34a853',
        borderWidth: 2,
        borderCapStyle: 'butt',
        borderDash: [],
        borderDashOffset: 0.0,
        borderJoinStyle: 'miter',
        pointBorderColor: '#34a853',
        pointBackgroundColor: '#fff',
        pointBorderWidth: 2,
        pointHoverRadius: 4,
        pointHoverBackgroundColor: '#34a853',
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

  new Chart($('#tile-chart-4'), {
    type: 'bar',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
      datasets: [{
        label: 'Dataset',
        data: [50, 45, 30, 20, 25, 35, 50],
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

  // Tab chart
  Morris.Area({
    element: 'multiple',
    data: [{
      period: '2011',
      apple: 120,
      sony: 110,
      samsung: 40
    }, {
      period: '2012',
      apple: 180,
      sony: 130,
      samsung: 170
    }, {
      period: '2013',
      apple: 120,
      sony: 170,
      samsung: 100
    }, {
      period: '2014',
      apple: 90,
      sony: 130,
      samsung: 40
    }, {
      period: '2015',
      apple: 120,
      sony: 150,
      samsung: 70
    }, {
      period: '2016',
      apple: 60,
      sony: 70,
      samsung: 90
    }, {
      period: '2017',
      apple: 170,
      sony: 190,
      samsung: 140
    }],
    xkey: 'period',
    ykeys: ['apple', 'sony', 'samsung'],
    labels: ['Apple', 'Sony', 'Samsung'],
    pointSize: 3,
    fillOpacity: 0.1,
    pointStrokeColors: ['#1d87e4', '#34a853', '#faa800'],
    behaveLikeLine: true,
    gridLineColor: '#e0e0e0',
    lineWidth: 1,
    hideHover: 'auto',
    lineColors: ['#1d87e4', '#34a853', '#faa800'],
    resize: true
  });

  /* Line Chart */
  var data3 = [[1, 10], [2, 20], [3, 12], [4, 28], [5, 15]];

  var labels = ["Tickets"];
  var colors = ['#34a853'];

  $.plot($("#chart-line"), [{
    data: data3,
    label: labels[0],
    color: colors[0]
  }], {
    series: {
      lines: {
        show: true,
        fill: true,
        lineWidth: 3,
        fillColor: {
          colors: [{
            opacity: 1
          }, {
            opacity: 1
          }]
        }
      },
      points: {
        show: true,
        radius: 0
      },
      shadowSize: 0,
      curvedLines: {
        apply: true,
        active: true,
        monotonicFit: true
      }
    },
    grid: {
      labelMargin: 10,
      color: "#aaa",
      hoverable: true,
      borderWidth: 0,
      backgroundColor: "#fff"
    },
    legend: {
      show: false
    },
    tooltip: true,
    tooltipOpts: {
      content: '%s: %y',
      shifts: {
        x: -60,
        y: 25
      },
      defaultTheme: false
    }
  });

  /* Donut chart */
  var data7 = [{
    label: "Mobile",
    data: 3,
    color: "#1d87e4"
  }, {
    label: "Tablet",
    data: 4,
    color: "#7d57c1"
  }, {
    label: "Desktop",
    data: 6,
    color: "#e53935"
  }];

  $.plot($("#chart-donut"), data7, {
    series: {
      pie: {
        innerRadius: 0.5,
        show: true
      }
    },
    grid: {
      hoverable: true
    },
    legend: {
      show: false
    },
    color: null,
    tooltip: true,
    tooltipOpts: {
      content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
      shifts: {
        x: 20,
        y: 0
      },
      defaultTheme: false
    }
  });
})(jQuery);
