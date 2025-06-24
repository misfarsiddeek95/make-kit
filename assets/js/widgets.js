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

  // Peity charts
  $('#peity-chart-1').peity('line', {
    fill: 'rgba(255, 255, 255, 0.3)',
    height: '50px',
    stroke: null,
    width: '100%'
  });

  $('#peity-chart-2').peity('bar', {
    fill: ['rgba(255, 255, 255, 0.3)'],
    height: '50px',
    width: '100%'
  });

  $('#peity-chart-3').peity('line', {
    fill: 'rgba(255, 255, 255, 0.3)',
    height: '50px',
    stroke: null,
    width: '100%'
  });

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

  // Vector map
  $('#vector-map').vectorMap({
    map: 'usa_en',
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
    selectedRegions: ['fl', 'ca', 'tx', 'ny', 'nd', 'oh'],
    showTooltip: true
  });

  // Bootstrap slider
  $('[data-plugin="bootstrapslider"]').bootstrapSlider();

  // Select2
  $.fn.select2.defaults.set('theme', 'bootstrap');
  $('[data-plugin="select2"]').select2($(this).attr('data-options'));
})(jQuery);
