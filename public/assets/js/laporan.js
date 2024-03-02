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

 
  console.log("Total Invoices:", totalInvoices);
  console.log("Total Invoices Belum Bayar:", totalInvoicesBB);
  console.log("Total Invoices Panjar:", totalInvoicesPJ);
  console.log("Total Invoices Lunas:", invoicesLN);

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
      donut: {
        series1: '#71dd37',
        series2: '#696cff',
        series3: '#ff3e1d',      
      }
    };

    const donutChartEl = document.querySelector('#donutChart'),
    donutChartConfig = {
      chart: {
        height: 390,
        type: 'donut'
      },
      labels: ['Lunas', 'Panjar', 'Belum Bayar'],
      series: [invoicesLN, totalInvoicesPJ, totalInvoicesBB],
      colors: [
        chartColors.donut.series1,    
        chartColors.donut.series2,
        chartColors.donut.series3
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
                fontSize: '2rem',
                fontFamily: 'Public Sans'
              },
              value: {
                fontSize: '1.2rem',
                color: legendColor,
                fontFamily: 'Public Sans',
                formatter: function (val) {
                  return parseInt(val, 10) + '%';
                }
              },
              total: {
                show: true,
                fontSize: '1.5rem',
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
              height: 380
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
          breakpoint: 576,
          options: {
            chart: {
              height: 320
            },
            plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: true,
                    name: {
                      fontSize: '1.5rem'
                    },
                    value: {
                      fontSize: '1rem'
                    },
                    total: {
                      fontSize: '1.5rem'
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
              show: false
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
              show: false
            }
          }
        }
      ]
    };
  if (typeof donutChartEl !== undefined && donutChartEl !== null) {
    const donutChart = new ApexCharts(donutChartEl, donutChartConfig);
    donutChart.render();
  }
})();


