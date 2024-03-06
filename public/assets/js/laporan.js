function reloadPageWithNewDates() {
    // Mengambil nilai dari input tanggal mulai dan tanggal akhir
    var startDate = document.getElementById("startDate").value;
    var endDate = document.getElementById("endDate").value;

    // Membuat URL baru dengan tanggal yang dipilih
    var newURL = "/keuangan/laporan?startDate=" + startDate + "&endDate=" + endDate;

    // Memuat ulang halaman dengan URL baru
    window.location.href = newURL;
  }

  document.getElementById("startDate").addEventListener("change", reloadPageWithNewDates);
  document.getElementById("endDate").addEventListener("change", reloadPageWithNewDates);

  (function () {
    let cardColor, headingColor, labelColor, borderColor, legendColor;
  
    if (isDarkStyle) {
      cardColor = config.colors_dark.cardColor;
      headingColor = config.colors_dark.headingColor;
      labelColor = config.colors_dark.textMuted;
      legendColor = config.colors_dark.bodyColor;
      borderColor = config.colors_dark.borderColor;
    } else {
      cardColor = config.colors.cardColor;
      headingColor = config.colors.headingColor;
      labelColor = config.colors.textMuted;
      legendColor = config.colors.bodyColor;
      borderColor = config.colors.borderColor;
    }
  
    // Color constant
    const chartColors = {
      donutA: {
        series1: '#71dd37',
        series2: '#696cff',
        series3: '#ff3e1d',      
      },
      donutB: {
        series1: '#bcf235',
        series2: '#71dd37',
        series3: '#ff2f15',
        series4: '#f7591d',
        series5: '#ffd52b',      
        series6: '#00e7d5',      
      }
    };

    const donutChartEl = document.querySelector('#donutChart'),
    donutChartConfig = {
      chart: {
        height: 280,
        type: 'donut'
      },
      labels: ['Lunas', 'Panjar', 'Belum Bayar'],
      series: [invoicesLN, totalInvoicesPJ, totalInvoicesBB],
      colors: [
        chartColors.donutA.series1,    
        chartColors.donutA.series2,
        chartColors.donutA.series3
      ],
      stroke: {
        show: false,
        curve: 'straight'
      },
      dataLabels: {
        enabled: true,
        formatter: function (val, opt) {
          return parseInt(val, 10) + '%';
        }
      },
      legend: {
        show: true,
        position: 'bottom',
        markers: { offsetX: -3 },
        itemMargin: {
          vertical: 3,
          horizontal: 10
        },
        labels: {
          colors: legendColor,
          useSeriesColors: false
        }
      },
      plotOptions: {
        pie: {
          donut: {
            labels: {
              show: true,
              name: {
                fontSize: '1rem',
                fontFamily: 'Public Sans'
              },
              value: {
                fontSize: '0.8rem',
                color: legendColor,
                fontFamily: 'Public Sans',
                formatter: function (val) {
                  return parseInt(val, 10);
                }
              },
              total: {
                show: true,
                fontSize: '0.8rem',
                color: headingColor,
                label: 'Total',
                formatter: function (w) {
                  return totalInvoices;
                }
              }
            }
          }
        }
      },
      responsive: [
        {
          breakpoint: 992,
          options: {
            chart: {
              height: 250
            },
            legend: {
              position: 'bottom',
              labels: {
                colors: legendColor,
                useSeriesColors: true
              }
            }
          }
        },
        {
          breakpoint: 576,
          options: {
            chart: {
              height: 250
            },
            plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: true,
                    name: {
                      fontSize: '0.8rem'
                    },
                    value: {
                      fontSize: '1rem'
                    },
                    total: {
                      fontSize: '0.8rem'
                    }
                  }
                }
              }
            },
            legend: {
              position: 'bottom',
              labels: {
                colors: legendColor,
                useSeriesColors: false
              }
            }
          }
        },
        {
          breakpoint: 420,
          options: {
            chart: {
              height: 280
            },
            legend: {
              show: true
            }
          }
        },
        {
          breakpoint: 360,
          options: {
            chart: {
              height: 250
            },
            legend: {
              show: true
            }
          }
        }
      ]
    };    
    if (typeof donutChartEl !== undefined && donutChartEl !== null) {
      const donutChart = new ApexCharts(donutChartEl, donutChartConfig);
      donutChart.render();
    }
    
    const donutChartE2 = document.querySelector('#donutChart2'),
    donutChart2Config = {
      chart: {
        height: 280,
        type: 'donut'
      },
      labels: ['Pengeluaran', 'Margin'],
      series: [outcomeTotal, income],
      colors: [
        chartColors.donutB.series4,
        chartColors.donutB.series1
      ],
      stroke: {
        show: false,
        curve: 'straight'
      },
      dataLabels: {
        enabled: true,
        formatter: function (val, opt) {
          return parseInt(val, 10) + '%';
        }
      },
      legend: {
        show: true,
        position: 'bottom',
        markers: { offsetX: -3 },
        itemMargin: {
          vertical: 3,
          horizontal: 10
        },
        labels: {
          colors: legendColor,
          useSeriesColors: false
        }
      },
      plotOptions: {
        pie: {
          donut: {
            labels: {
              show: true,
              name: {
                fontSize: '1rem',
                fontFamily: 'Public Sans'
              },
              value: {
                fontSize: '0.8rem',
                color: legendColor,
                fontFamily: 'Public Sans',
                formatter: function (val) {                
                  return 'Rp ' + parseInt(val, 10).toLocaleString('id-ID');
                }
              },
              total: {
                show: true,
                fontSize: '0.8rem',
                color: headingColor,
                label: 'Income',
                formatter: function (w) {
                  return 'Rp ' + parseInt(pemasukan, 10).toLocaleString('id-ID');                  
                }
              }
            }
          }
        }
      },
      responsive: [
        {
          breakpoint: 992,
          options: {
            chart: {
              height: 250
            },
            legend: {
              position: 'bottom',
              labels: {
                colors: legendColor,
                useSeriesColors: true
              }
            }
          }
        },
        {
          breakpoint: 576,
          options: {
            chart: {
              height: 250
            },
            plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: true,
                    name: {
                      fontSize: '0.8rem'
                    },
                    value: {
                      fontSize: '1rem'
                    },
                    total: {
                      fontSize: '0.8rem'
                    }
                  }
                }
              }
            },
            legend: {
              position: 'bottom',
              labels: {
                colors: legendColor,
                useSeriesColors: false
              }
            }
          }
        },
        {
          breakpoint: 420,
          options: {
            chart: {
              height: 280
            },
            legend: {
              show: true
            }
          }
        },
        {
          breakpoint: 360,
          options: {
            chart: {
              height: 250
            },
            legend: {
              show: true
            }
          }
        }
      ]
    };    
    if (typeof donutChartE2 !== undefined && donutChartE2 !== null) {
      const donutChart = new ApexCharts(donutChartE2, donutChart2Config);
      donutChart.render();
    }
})();


