/**
 * Dashboard eCommerce
 */

'use strict';

(function () {
  let cardColor, headingColor, labelColor, shadeColor, borderColor, heatMap1, heatMap2, heatMap3, heatMap4;

  if (isDarkStyle) {
    cardColor = config.colors_dark.cardColor;
    headingColor = config.colors_dark.headingColor;
    labelColor = config.colors_dark.textMuted;
    borderColor = config.colors_dark.borderColor;
    shadeColor = 'dark';
    heatMap1 = '#4f51c0';
    heatMap2 = '#595cd9';
    heatMap3 = '#8789ff';
    heatMap4 = '#c3c4ff';
  } else {
    cardColor = config.colors.cardColor;
    headingColor = config.colors.headingColor;
    labelColor = config.colors.textMuted;
    borderColor = config.colors.borderColor;
    shadeColor = '';
    heatMap1 = '#e1e2ff';
    heatMap2 = '#c3c4ff';
    heatMap3 = '#a5a7ff';
    heatMap4 = '#696cff';
  }

  // Visitor Bar Chart
  // --------------------------------------------------------------------
  const visitorBarChartEl = document.querySelector('#visitorsChart'),
    visitorBarChartConfig = {
      chart: {
        height: 120,
        width: 200,
        parentHeightOffset: 0,
        type: 'bar',
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          barHeight: '75%',
          columnWidth: '60%',
          startingShape: 'rounded',
          endingShape: 'rounded',
          borderRadius: 9,
          distributed: true
        }
      },
      grid: {
        show: false,
        padding: {
          top: -25,
          bottom: -12
        }
      },
      colors: [
        config.colors_label.primary,
        config.colors_label.primary,
        config.colors_label.primary,
        config.colors_label.primary,
        config.colors_label.primary,
        config.colors.primary,
        config.colors_label.primary
      ],
      dataLabels: {
        enabled: false
      },
      series: [
        {
          data: [40, 95, 60, 45, 90, 50, 75]
        }
      ],
      legend: {
        show: false
      },
      responsive: [
        {
          breakpoint: 1440,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 9,
                columnWidth: '60%'
              }
            }
          }
        },
        {
          breakpoint: 1300,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 9,
                columnWidth: '60%'
              }
            }
          }
        },
        {
          breakpoint: 1200,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 8,
                columnWidth: '50%'
              }
            }
          }
        },
        {
          breakpoint: 1040,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 8,
                columnWidth: '50%'
              }
            }
          }
        },
        {
          breakpoint: 991,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 8,
                columnWidth: '50%'
              }
            }
          }
        },
        {
          breakpoint: 420,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 8,
                columnWidth: '50%'
              }
            }
          }
        }
      ],
      xaxis: {
        categories: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          style: {
            colors: labelColor,
            fontSize: '13px'
          }
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };
  if (typeof visitorBarChartEl !== undefined && visitorBarChartEl !== null) {
    const visitorBarChart = new ApexCharts(visitorBarChartEl, visitorBarChartConfig);
    visitorBarChart.render();
  }  
  
  // Used
  // --------------------------------------------------------------------
  const pendapatanChartEl = document.querySelector('#pendapatanChart'),
  pendapatanChartConfig = {
      chart: {
        height: 120,
        width: 220,
        parentHeightOffset: 0,
        toolbar: {
          show: false
        },
        type: 'area'
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 2,
        curve: 'smooth'
      },
      series: [
        {
          data: [totIncomeSen, totIncomeSel, totIncomeRab, totIncomeKam, totIncomeJum, totIncomeSab]
        }
      ],
      colors: [config.colors.success],
      fill: {
        type: 'gradient',
        gradient: {
          shade: shadeColor,
          shadeIntensity: 0.8,
          opacityFrom: 0.8,
          opacityTo: 0.25,
          stops: [0, 85, 100]
        }
      },
      grid: {
        show: false,
        padding: {
          top: -20,
          bottom: -8
        }
      },
      legend: {
        show: false
      },
      xaxis: {
        categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          style: {
            fontSize: '13px',
            colors: labelColor
          }
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };
  if (typeof pendapatanChartEl !== undefined && pendapatanChartEl !== null) {
    const activityAreaChart = new ApexCharts(pendapatanChartEl, pendapatanChartConfig);
    activityAreaChart.render();
  }
  
  const pengeluaranChartEl = document.querySelector('#pengeluaranChart'),
  pengeluaranChartConfig = {
      chart: {
        height: 120,
        width: 220,
        parentHeightOffset: 0,
        toolbar: {
          show: false
        },
        type: 'area'
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 2,
        curve: 'smooth'
      },
      series: [
        {
          data: [totOutcomeSen, totOutcomeSel, totOutcomeRab, totOutcomeKam, totOutcomeJum, totOutcomeSab]
        }
      ],
      colors: [config.colors.danger],
      fill: {
        type: 'gradient',
        gradient: {
          shade: shadeColor,
          shadeIntensity: 0.8,
          opacityFrom: 0.8,
          opacityTo: 0.25,
          stops: [0, 85, 100]
        }
      },
      grid: {
        show: false,
        padding: {
          top: -20,
          bottom: -8
        }
      },
      legend: {
        show: false
      },
      xaxis: {
        categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          style: {
            fontSize: '13px',
            colors: labelColor
          }
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };

  if (typeof pengeluaranChartEl !== undefined && pengeluaranChartEl !== null) {
    const activityAreaChart = new ApexCharts(pengeluaranChartEl, pengeluaranChartConfig);
    activityAreaChart.render();
  }

  const invLunChartChartEl = document.querySelector('#incomeMonthlyChart'),
    invLunChartConfig = {
      chart: {
        height: 130,
        sparkline: {
          enabled: true
        },
        parentHeightOffset: 0,
        type: 'radialBar'
      },
      colors: [config.colors.success],
      series: [incomeMonthly],
      plotOptions: {
        radialBar: {
          startAngle: -90,
          endAngle: 90,
          hollow: {
            size: '55%'
          },
          track: {
            background: config.colors_label.secondary
          },
          dataLabels: {
            name: {
              show: false
            },
            value: {
              fontSize: '22px',
              color: headingColor,
              fontWeight: 500,
              offsetY: 0
            }
          }
        }
      },
      grid: {
        show: false,
        padding: {
          left: -10,
          right: -10,
          top: -10
        }
      },
      stroke: {
        lineCap: 'round'
      },
      labels: ['Progress']
    };
  if (typeof invLunChartChartEl !== undefined && invLunChartChartEl !== null) {
    const invLunChartChart = new ApexCharts(invLunChartChartEl, invLunChartConfig);
    invLunChartChart.render();
  }
  
  const invPanChartEl = document.querySelector('#bonMonthlyChart'),
    invPanChartConfig = {
      chart: {
        height: 130,
        sparkline: {
          enabled: true
        },
        parentHeightOffset: 0,
        type: 'radialBar'
      },
      colors: [config.colors.danger],
      series: [bonMonthly],
      plotOptions: {
        radialBar: {
          startAngle: -90,
          endAngle: 90,
          hollow: {
            size: '55%'
          },
          track: {
            background: config.colors_label.secondary
          },
          dataLabels: {
            name: {
              show: false
            },
            value: {
              fontSize: '22px',
              color: headingColor,
              fontWeight: 500,
              offsetY: 0
            }
          }
        }
      },
      grid: {
        show: false,
        padding: {
          left: -10,
          right: -10,
          top: -10
        }
      },
      stroke: {
        lineCap: 'round'
      },
      labels: ['Progress']
    };
  if (typeof invPanChartEl !== undefined && invPanChartEl !== null) {
    const invPanChart = new ApexCharts(invPanChartEl, invPanChartConfig);
    invPanChart.render();
  }


  
  const outcomeChartEl = document.querySelector('#outcomeChart'),
  outcomeConfig = {
    chart: {
      height: 130,
      sparkline: {
        enabled: true
      },
      parentHeightOffset: 0,
      type: 'radialBar'
    },
    colors: [config.colors.primary],
    series: [pengeluaran],
    plotOptions: {
      radialBar: {
        startAngle: -90,
        endAngle: 90,
        hollow: {
          size: '55%'
        },
        track: {
          background: config.colors_label.secondary
        },
        dataLabels: {
          name: {
            show: false
          },
          value: {
            fontSize: '22px',
            color: headingColor,
            fontWeight: 500,
            offsetY: 0
          }
        }
      }
    },
    grid: {
      show: false,
      padding: {
        left: -10,
        right: -10,
        top: -10
      }
    },
    stroke: {
      lineCap: 'round'
    },
    labels: ['Progress']
  };
  if (typeof outcomeChartEl !== undefined && outcomeChartEl !== null) {
    const outcomeChart = new ApexCharts(outcomeChartEl, outcomeConfig);
    outcomeChart.render();
  }
  
  const marginChartEl = document.querySelector('#marginChart'),
  marginConfig = {
    chart: {
      height: 130,
      sparkline: {
        enabled: true
      },
      parentHeightOffset: 0,
      type: 'radialBar'
    },
    colors: [config.colors.success],
    series: [margin],
    plotOptions: {
      radialBar: {
        startAngle: -90,
        endAngle: 90,
        hollow: {
          size: '55%'
        },
        track: {
          background: config.colors_label.secondary
        },
        dataLabels: {
          name: {
            show: false
          },
          value: {
            fontSize: '22px',
            color: headingColor,
            fontWeight: 500,
            offsetY: 0
          }
        }
      }
    },
    grid: {
      show: false,
      padding: {
        left: -10,
        right: -10,
        top: -10
      }
    },
    stroke: {
      lineCap: 'round'
    },
    labels: ['Progress']
  };
  if (typeof marginChartEl !== undefined && marginChartEl !== null) {
    const marginChart = new ApexCharts(marginChartEl, marginConfig);
    marginChart.render();
  }

  // Conversion rate Line Chart
  // --------------------------------------------------------------------
  const pendapatanBarChartEl = document.querySelector('#pendapatanBarChart'),
    pendapatanBarChartConfig = {
      series: [
        {
          name: bulan,
          data: [incomeWeek1, incomeWeek2, incomeWeek3, incomeWeek4]
        },
        {
          name: bulanLalu,
          data: [incPastWeek1, incPastWeek2, incPastWeek3, incPastWeek4, ]
        }
      ],
      chart: {
        height: 150,
        stacked: true,
        type: 'bar',
        toolbar: { show: false }
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '40%',
          borderRadius: 5,
          startingShape: 'rounded'
        }
      },
      colors: [config.colors.primary, config.colors.info],
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 2,
        lineCap: 'round',
        colors: [cardColor]
      },
      legend: {
        show: false
      },
      grid: {
        show: false,
        padding: {
          top: -10
        }
      },
      xaxis: {
        show: false,
        categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        labels: {
          show: false
        },
        axisTicks: {
          show: false
        },
        axisBorder: {
          show: false
        }
      },
      yaxis: {
        show: false
      },
      responsive: [
        {
          breakpoint: 1440,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 5,
                columnWidth: '60%'
              }
            }
          }
        },
        {
          breakpoint: 1300,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 5,
                columnWidth: '70%'
              }
            }
          }
        },
        {
          breakpoint: 1200,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 4,
                columnWidth: '50%'
              }
            }
          }
        },
        {
          breakpoint: 1040,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 4,
                columnWidth: '60%'
              }
            }
          }
        },
        {
          breakpoint: 991,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 4,
                columnWidth: '40%'
              }
            }
          }
        },
        {
          breakpoint: 420,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 5,
                columnWidth: '60%'
              }
            }
          }
        },
        {
          breakpoint: 360,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 5,
                columnWidth: '70%'
              }
            }
          }
        }
      ],
      states: {
        hover: {
          filter: {
            type: 'none'
          }
        },
        active: {
          filter: {
            type: 'none'
          }
        }
      }
    };
  if (typeof pendapatanBarChartEl !== undefined && pendapatanBarChartEl !== null) {
    const pendapatanBarChart = new ApexCharts(pendapatanBarChartEl, pendapatanBarChartConfig);
    pendapatanBarChart.render();
  }
  
  const marginBarChartEl = document.querySelector('#marginBarChart'),
    marginBarChartConfig = {
      series: [
        {
          name: bulan,
          data: [marginWeek1, marginWeek2, marginWeek3, marginWeek4]
        },
        {
          name: bulanLalu,
          data: [-margPastWeek1, -margPastWeek2, -margPastWeek3, -margPastWeek4]
        }
      ],
      chart: {
        height: 150,
        stacked: true,
        type: 'bar',
        toolbar: { show: false }
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '40%',
          borderRadius: 5,
          startingShape: 'rounded'
        }
      },
      colors: [config.colors.success, config.colors.warning],
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 2,
        lineCap: 'round',
        colors: [cardColor]
      },
      legend: {
        show: false
      },
      grid: {
        show: false,
        padding: {
          top: -10
        }
      },
      xaxis: {
        show: false,
        categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        labels: {
          show: false
        },
        axisTicks: {
          show: false
        },
        axisBorder: {
          show: false
        }
      },
      yaxis: {
        show: false
      },
      responsive: [
        {
          breakpoint: 1440,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 5,
                columnWidth: '60%'
              }
            }
          }
        },
        {
          breakpoint: 1300,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 5,
                columnWidth: '70%'
              }
            }
          }
        },
        {
          breakpoint: 1200,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 4,
                columnWidth: '50%'
              }
            }
          }
        },
        {
          breakpoint: 1040,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 4,
                columnWidth: '60%'
              }
            }
          }
        },
        {
          breakpoint: 991,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 4,
                columnWidth: '40%'
              }
            }
          }
        },
        {
          breakpoint: 420,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 5,
                columnWidth: '60%'
              }
            }
          }
        },
        {
          breakpoint: 360,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 5,
                columnWidth: '70%'
              }
            }
          }
        }
      ],
      states: {
        hover: {
          filter: {
            type: 'none'
          }
        },
        active: {
          filter: {
            type: 'none'
          }
        }
      }
    };
  if (typeof marginBarChartEl !== undefined && marginBarChartEl !== null) {
    const marginBarChart = new ApexCharts(marginBarChartEl, marginBarChartConfig);
    marginBarChart.render();
  }

  // --------------------------------------------------------------------
  // !Used


  // Profit Bar Chart
  // --------------------------------------------------------------------
  const profitBarChartEl = document.querySelector('#profitChart'),
    profitBarChartConfig = {
      series: [
        {
          data: [58, 28, 50, 80]
        },
        {
          data: [50, 22, 65, 72]
        }
      ],
      chart: {
        type: 'bar',
        height: 80,
        toolbar: {
          tools: {
            download: false
          }
        }
      },
      plotOptions: {
        bar: {
          columnWidth: '65%',
          startingShape: 'rounded',
          endingShape: 'rounded',
          borderRadius: 3,
          dataLabels: {
            show: false
          }
        }
      },
      grid: {
        show: false,
        padding: {
          top: -30,
          bottom: -12,
          left: -10,
          right: 0
        }
      },
      colors: [config.colors.success, config.colors_label.success],
      dataLabels: {
        enabled: false
      },
      stroke: {
        show: true,
        width: 5,
        colors: labelColor
      },
      legend: {
        show: false
      },
      xaxis: {
        categories: ['Jan', 'Apr', 'Jul', 'Oct'],
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          style: {
            colors: labelColor,
            fontSize: '13px'
          }
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };
  if (typeof profitBarChartEl !== undefined && profitBarChartEl !== null) {
    const profitBarChart = new ApexCharts(profitBarChartEl, profitBarChartConfig);
    profitBarChart.render();
  }

  // Total Income - Area Chart
  // --------------------------------------------------------------------
  const totalIncomeEl = document.querySelector('#totalIncomeChart'),
    totalIncomeConfig = {
      chart: {
        height: 250,
        type: 'area',
        toolbar: false,
        dropShadow: {
          enabled: true,
          top: 14,
          left: 2,
          blur: 3,
          color: config.colors.primary,
          opacity: 0.15
        }
      },
    
      series: [
        {
          name: 'Margin',
          data: [incomeJanYear, incomeFebYear, incomeMarYear, incomeAprYear, incomeMayYear, incomeJunYear, incomeJulYear, incomeAugYear, incomeSepYear, incomeOctYear, incomeNovYear, incomeDecYear]
        }
      ],
   
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 3,
        curve: 'straight'
      },
      colors: [config.colors.primary],
      fill: {
        type: 'gradient',
        gradient: {
          shade: shadeColor,
          shadeIntensity: 0.8,
          opacityFrom: 0.7,
          opacityTo: 0.25,
          stops: [0, 95, 100]
        }
      },
      grid: {
        show: true,
        borderColor: borderColor,
        padding: {
          top: -15,
          bottom: -10,
          left: 0,
          right: 0
        }
      },
      xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        labels: {
          offsetX: 0,
          style: {
            colors: labelColor,
            fontSize: '13px'
          }
        },
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        lines: {
          show: false
        }
      },
      yaxis: {
        labels: {
          offsetX: -15,
          formatter: function(val) {
              // Format angka ke dalam format mata uang rupiah (Rp.)
              return 'Rp.' + val.toLocaleString('id-ID') + ',-';
          },
          style: {
            fontSize: '13px',
            colors: labelColor
          }
        },
        min: -20000000,
        max: 50000000,
        tickAmount: 5
      }
    };
  if (typeof totalIncomeEl !== undefined && totalIncomeEl !== null) {
    const totalIncome = new ApexCharts(totalIncomeEl, totalIncomeConfig);
    totalIncome.render();
  }

  // Performance - Radar Chart
  // --------------------------------------------------------------------
  const performanceChartEl = document.querySelector('#performanceChart'),
    performanceChartConfig = {
      series: [
        {
          name: 'Income',
          data: [26, 29, 31, 40, 29, 24]
        },
        {
          name: 'Earning',
          data: [30, 26, 24, 26, 24, 40]
        }
      ],
      chart: {
        height: 270,
        type: 'radar',
        toolbar: {
          show: false
        },
        dropShadow: {
          enabled: true,
          enabledOnSeries: undefined,
          top: 6,
          left: 0,
          blur: 6,
          color: '#000',
          opacity: 0.14
        }
      },
      plotOptions: {
        radar: {
          polygons: {
            strokeColors: borderColor,
            connectorColors: borderColor
          }
        }
      },
      stroke: {
        show: false,
        width: 0
      },
      legend: {
        show: true,
        fontSize: '13px',
        position: 'bottom',
        labels: {
          colors: '#aab3bf',
          useSeriesColors: false
        },
        markers: {
          height: 10,
          width: 10,
          offsetX: -3
        },
        itemMargin: {
          horizontal: 10
        },
        onItemHover: {
          highlightDataSeries: false
        }
      },
      colors: [config.colors.primary, config.colors.info],
      fill: {
        opacity: [1, 0.85]
      },
      markers: {
        size: 0
      },
      grid: {
        show: false,
        padding: {
          top: -8,
          bottom: -5
        }
      },
      xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        labels: {
          show: true,
          style: {
            colors: [labelColor, labelColor, labelColor, labelColor, labelColor, labelColor],
            fontSize: '13px',
            fontFamily: 'Public Sans'
          }
        }
      },
      yaxis: {
        show: false,
        min: 0,
        max: 40,
        tickAmount: 4
      }
    };
  if (typeof performanceChartEl !== undefined && performanceChartEl !== null) {
    const performanceChart = new ApexCharts(performanceChartEl, performanceChartConfig);
    performanceChart.render();
  }

  // Conversion rate Line Chart
  // --------------------------------------------------------------------
  const conversionLineChartEl = document.querySelector('#conversionRateChart'),
    conversionLineChartConfig = {
      chart: {
        height: 80,
        width: 140,
        type: 'line',
        toolbar: {
          show: false
        },
        dropShadow: {
          enabled: true,
          top: 10,
          left: 5,
          blur: 3,
          color: config.colors.primary,
          opacity: 0.15
        },
        sparkline: {
          enabled: true
        }
      },
      markers: {
        size: 6,
        colors: 'transparent',
        strokeColors: 'transparent',
        strokeWidth: 4,
        discrete: [
          {
            fillColor: config.colors.white,
            seriesIndex: 0,
            dataPointIndex: 3,
            strokeColor: config.colors.primary,
            strokeWidth: 4,
            size: 6,
            radius: 2
          }
        ],
        hover: {
          size: 7
        }
      },
      grid: {
        show: false,
        padding: {
          right: 8
        }
      },
      colors: [config.colors.primary],
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 5,
        curve: 'smooth'
      },
      series: [
        {
          name: 'Income',
          data: [incomweToMonth, incomeLastMonth, incomeThisMonth]
        }
      ],
      xaxis: {
        show: false,
        categories: [duaBulanLalu, bulanLalu, bulan],
        labels: {
          show: false
        },
        axisTicks: {
          show: false
        },
        axisBorder: {
          show: false
        }
      },
      yaxis: {
        show: false
      }
    };
  if (typeof conversionLineChartEl !== undefined && conversionLineChartEl !== null) {
    const conversionLineChart = new ApexCharts(conversionLineChartEl, conversionLineChartConfig);
    conversionLineChart.render();
  }

  
  // Total Balance - Line Chart
  // --------------------------------------------------------------------
  const totalBalanceChartEl = document.querySelector('#totalBalanceChart'),
    totalBalanceChartConfig = {
      series: [
        {
          name: 'Invoice',
          data: [invoiceJan, invoiceFeb, invoiceMar, invoiceApr, invoiceMay, invoiceJun, invoiceJul, invoiceAug, invoiceSep, invoiceOct, invoiceNov, invoiceDec]
        }
      ],
      chart: {
        height: 250,
        parentHeightOffset: 0,
        parentWidthOffset: 0,
        type: 'line',
        dropShadow: {
          enabled: true,
          top: 10,
          left: 5,
          blur: 3,
          color: config.colors.warning,
          opacity: 0.15
        },
        toolbar: {
          show: false
        }
      },
      dataLabels: {
        enabled: true
      },
      stroke: {
        width: 4,
        curve: 'smooth'
      },
      legend: {
        show: false
      },
      colors: [config.colors.warning],
      markers: {
        size: 6,
        colors: 'transparent',
        strokeColors: 'transparent',
        strokeWidth: 4,
        discrete: [
          {
            fillColor: config.colors.white,
            seriesIndex: 0,
            dataPointIndex: 5,
            strokeColor: config.colors.warning,
            strokeWidth: 8,
            size: 6,
            radius: 8
          }
        ],
        hover: {
          size: 7
        }
      },
      grid: {
        show: false,
        padding: {
          top: -10,
          left: 0,
          right: 0,
          bottom: 10
        }
      },
      xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          show: true,
          style: {
            fontSize: '13px',
            colors: labelColor
          }
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };
  if (typeof totalBalanceChartEl !== undefined && totalBalanceChartEl !== null) {
    const totalBalanceChart = new ApexCharts(totalBalanceChartEl, totalBalanceChartConfig);
    totalBalanceChart.render();
  }
})();
