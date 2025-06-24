"use strict";

(function ($) {
  'use strict';

  // Line chart

  var dataLine = {
    labels: ["January", "February", "March", "April", "May", "June", "July"],
    datasets: [{
      label: "Registartions",
      fill: false,
      lineTension: 0.0,
      backgroundColor: "#e53935",
      borderColor: "#e53935",
      borderCapStyle: 'butt',
      borderDash: [],
      borderDashOffset: 0.0,
      borderJoinStyle: 'miter',
      pointBorderColor: "#e53935",
      pointBackgroundColor: "#fff",
      pointBorderWidth: 1,
      pointHoverRadius: 5,
      pointHoverBackgroundColor: "#e53935",
      pointHoverBorderColor: "#fff",
      pointHoverBorderWidth: 2,
      pointRadius: 1,
      pointHitRadius: 10,
      data: [20, 60, 40, 95, 40, 55, 120],
      spanGaps: false
    }]
  };

  new Chart($('#line'), {
    type: 'line',
    data: dataLine,
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });

  // Bar chart
  var dataBar = {
    labels: ["January", "February", "March", "April", "May", "June", "July"],
    datasets: [{
      label: 'Sales',
      data: [20, 28, 16, 10, 23, 18, 35],
      backgroundColor: 'rgba(67, 185, 104, 0.2)',
      borderColor: '#34a853',
      borderWidth: 1
    }]
  };

  new Chart($('#bar'), {
    type: 'bar',
    data: dataBar,
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });

  //Pie chart
  var dataPie = {
    labels: ["Apple", "Samsung", "LG"],
    datasets: [{
      data: [250, 70, 160],
      backgroundColor: ["#1d87e4", "#faa800", "#e53935"]
    }]
  };

  new Chart($('#pie'), {
    type: 'pie',
    data: dataPie
  });

  //Doughnut chart
  var dataDoughnut = {
    labels: ["Apple", "Samsung", "LG"],
    datasets: [{
      data: [250, 70, 160],
      backgroundColor: ["#1d87e4", "#faa800", "#e53935"]
    }]
  };

  new Chart($('#doughnut'), {
    type: 'doughnut',
    data: dataDoughnut
  });

  //Polar area chart
  var dataPolar = {
    datasets: [{
      data: [11, 25, 17, 8, 30],
      backgroundColor: ["#e53935", "#34a853", "#faa800", "#777"]
    }],
    labels: ["Red", "Green", "Orange", "Grey"]
  };

  new Chart($('#polar-area'), {
    type: 'polarArea',
    data: dataPolar
  });

  //Radar chart
  var dataRadar = {
    labels: ["Eating", "Drinking", "Sleeping", "Designing", "Coding", "Cycling", "Running"],
    datasets: [{
      label: "2015",
      backgroundColor: "rgba(244,66,54,0.2)",
      borderColor: "#e53935",
      pointBackgroundColor: "#e53935",
      pointBorderColor: "#fff",
      pointHoverBackgroundColor: "#fff",
      pointHoverBorderColor: "#e53935",
      data: [65, 59, 90, 81, 56, 55, 40]
    }, {
      label: "2016",
      backgroundColor: "rgba(153,153,153,0.2)",
      borderColor: "999",
      pointBackgroundColor: "999",
      pointBorderColor: "#fff",
      pointHoverBackgroundColor: "#fff",
      pointHoverBorderColor: "#999",
      data: [28, 48, 40, 19, 96, 27, 100]
    }]
  };

  new Chart($('#radar'), {
    type: 'radar',
    data: dataRadar
  });
})(jQuery);
