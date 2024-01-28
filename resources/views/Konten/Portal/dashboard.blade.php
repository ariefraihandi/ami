@extends('Index/app')

@push('head-script')
     <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />        
@endpush

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
      <div class="col-lg-8 mb-4 order-0">
        <div class="card">
          <div class="d-flex align-items-end row">
            <div class="col-sm-7">
                <div class="card-body">
                    <h5 class="card-title text-primary">Selamat Datang Pak Dedy! 🎉</h5>
                    <p class="mb-4">
                    Hari ini kita meraih keuntungan hingga <span class="fw-medium">100%</span>. Tetap konsiten dan selalu jaga kesehatan dalam berkerja.
                    </p>

                    <a href="javascript:;" class="btn btn-sm btn-label-primary">View Badges</a>
                </div>
            </div>
            <div class="col-sm-5 text-center text-sm-left">
                <div class="card-body pb-0 px-0 px-md-4">
                  <img src="{{ asset('assets') }}/img/staff/dedy.png" height="180" alt="View Badge User" data-app-dark-img="staff/dedy.png" data-app-light-img="staff/dedy.png" />
                </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 order-1">
        <div class="row">
          <div class="col-lg-6 col-md-12 col-6 mb-4">
              <div class="card">
                  <div class="card-body pb-0">
                  <span class="d-block fw-medium mb-1">Pendapatan</span>
                  <h4 class="card-title mb-1">{{$incomeToday}}</h4>              
                    <small class="@if($percentageIncrease > 0) text-success @elseif($percentageIncrease < 0) text-danger @else text-secondary @endif fw-medium">
                      @if($percentageIncrease > 0)
                          <i class="bx bx-up-arrow-alt"></i> 
                      @elseif($percentageIncrease < 0)
                          <i class="bx bx-down-arrow-alt"></i> 
                      @endif
                      {{ number_format($percentageIncrease, 1) }} %
                    </small>                  
                  </div>
                  <div id="income" class="mb-3"></div>
              </div>
          </div>
          <div class="col-lg-6 col-md-12 col-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0">
                    <img src="{{ asset('assets') }}/img/icons/unicons/wallet-info.png" alt="Credit Card" class="rounded" />
                  </div>
                  <div class="dropdown">
                    <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                      <a class="dropdown-item" href="javascript:void(0);">View More</a>
                      <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                    </div>
                  </div>
                </div>
                <span>Kas Harian</span>
                <h3 class="card-title text-nowrap mb-1">{{$totalKas}}</h3>
                @if($percentageKas > 0)
                  <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> {{number_format($percentageKas, 1)}}</small>
                @elseif($percentageKas < 0)
                  <small class="text-danger fw-medium"><i class="bx bx-down-arrow-alt"></i> {{number_format($percentageKas, 1)}}</small>
                @else
                  <small class="text-secondary fw-medium"><i class="bx bx-minus"></i> {{number_format($percentageKas, 1)}}</small>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Total Revenue -->
      <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
        <div class="card">
          <div class="row row-bordered g-0">
            <div class="col-md-8">
              <h5 class="card-header m-0 me-2 pb-3">Total Revenue</h5>
              <div id="totalRevenueChart" class="px-2"></div>
            </div>
            <div class="col-md-4">
              <div class="card-body">
                <div class="text-center">
                  <div class="dropdown">
                    <button
                      class="btn btn-sm btn-label-primary dropdown-toggle"
                      type="button"
                      id="growthReportId"
                      data-bs-toggle="dropdown"
                      aria-haspopup="true"
                      aria-expanded="false">
                      2022
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="growthReportId">
                      <a class="dropdown-item" href="javascript:void(0);">2021</a>
                      <a class="dropdown-item" href="javascript:void(0);">2020</a>
                      <a class="dropdown-item" href="javascript:void(0);">2019</a>
                    </div>
                  </div>
                </div>
              </div>
              <div id="growthChart"></div>
              <div class="text-center fw-medium pt-3 mb-2">62% Company Growth</div>

              <div class="d-flex px-xxl-4 px-lg-2 p-4 gap-xxl-3 gap-lg-1 gap-3 justify-content-between">
                <div class="d-flex">
                  <div class="me-2">
                    <span class="badge bg-label-primary p-2"><i class="bx bx-dollar text-primary"></i></span>
                  </div>
                  <div class="d-flex flex-column">
                    <small>2022</small>
                    <h6 class="mb-0">$32.5k</h6>
                  </div>
                </div>
                <div class="d-flex">
                  <div class="me-2">
                    <span class="badge bg-label-info p-2"><i class="bx bx-wallet text-info"></i></span>
                  </div>
                  <div class="d-flex flex-column">
                    <small>2021</small>
                    <h6 class="mb-0">$41.2k</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--/ Total Revenue -->
      <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
        <div class="row">
          <div class="col-6 mb-4">
            <div class="card"> 
              <div class="card-body pb-0">
                  <span class="d-block fw-medium mb-1">Pengeluaran</span>
                  <h4 class="card-title mb-1">{{$outcomeToday}}</h4>
                  <small class="@if($percentageOutcomeToday > 0) text-success @elseif($percentageOutcomeToday < 0) text-danger @else text-secondary @endif fw-medium">
                      @if($percentageOutcomeToday > 0)
                          <i class="bx bx-up-arrow-alt"></i> 
                      @elseif($percentageOutcomeToday < 0)
                          <i class="bx bx-down-arrow-alt"></i> 
                      @endif
                      {{ number_format($percentageOutcomeToday, 1) }} %
                  </small>
              </div>
              <div id="outcome" class="mb-3"></div>
            </div>
          </div>
          <div class="col-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0">
                    <img src="../../assets/img/icons/unicons/paypal.png" alt="Credit Card" class="rounded" />
                  </div>
                  <div class="dropdown">
                    <button
                      class="btn p-0"
                      type="button"
                      id="cardOpt4"
                      data-bs-toggle="dropdown"
                      aria-haspopup="true"
                      aria-expanded="false">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                      <a class="dropdown-item" href="javascript:void(0);">View More</a>
                      <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                    </div>
                  </div>
                </div>
                <span class="d-block mb-1">Pengeluaran</span>
                <h3 class="card-title text-nowrap mb-2">{{$outcomeTotal}}</h3>
                <small class="text-danger fw-medium"><i class="bx bx-down-arrow-alt"></i> -14.82%</small>
              </div>
            </div>
          </div>
          
          
        </div>
      </div>
           
    </div>
  </div>
  <!-- / Content -->
@endsection

@push('footer-script')
  <script src="{{ asset('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>  
  <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
  <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush

@push('footer-Sec-script')
{{-- Income Chart --}}
    <script>
        'use strict';

        (function () {
            const orderAreaChartEl = document.querySelector('#income');

            if (orderAreaChartEl !== null) {
                // Konfigurasi chart
                const orderAreaChartConfig = {
                    chart: {
                        height: 80,
                        type: 'area',
                        toolbar: { show: false },
                        sparkline: { enabled: true }
                    },
                    markers: {
                        size: 6,
                        colors: 'transparent',
                        strokeColors: 'transparent',
                        strokeWidth: 4,
                        discrete: [
                            {
                                fillColor: '#fff',
                                seriesIndex: 0,
                                dataPointIndex: 6,
                                strokeColor: '#4caf50',
                                strokeWidth: 2,
                                size: 6,
                                radius: 8
                            }
                        ],
                        hover: { size: 7 }
                    },
                    grid: { show: false, padding: { right: 8 } },
                    colors: ['#4caf50'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            shadeIntensity: 0.8,
                            opacityFrom: 0.8,
                            opacityTo: 0.25,
                            stops: [0, 85, 100]
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: { width: 2, curve: 'smooth' },
                    series: [
                        {
                            data: {!! json_encode($seriesData) !!}.map(item => parseFloat(item.total_amount))
                        }
                    ],

                    xaxis: {
                        categories: {!! json_encode($categories) !!} // Gunakan data categories yang dikirim dari PHP
                    },
                    yaxis: { show: false },
                    tooltip: {
                        enabled: true,
                        x: {
                            format: 'dd MMM yyyy', // Format tanggal yang diinginkan
                        },
                        y: {
                            formatter: function (value) {
                                // Format nilai sebagai mata uang dengan simbol 'Rp.'
                                return 'Rp. ' + value.toLocaleString();
                            }
                        },
                    },
                };

                // Inisialisasi dan rendering chart
                const orderAreaChart = new ApexCharts(orderAreaChartEl, orderAreaChartConfig);
                orderAreaChart.render();
            }
        })();
    </script>
{{-- // Income Chart --}}
 
{{-- Outcome Chart --}}
<script>
  'use strict';

  (function () {
      const outcomeChartEl = document.querySelector('#outcome'); // Ganti nama variabel

      if (outcomeChartEl !== null) {
          // Konfigurasi chart
          const outcomeChartConfig = {
              chart: {
                  height: 80,
                  type: 'area',
                  toolbar: { show: false },
                  sparkline: { enabled: true }
              },
              markers: {
                  size: 6,
                  colors: 'transparent',
                  strokeColors: 'transparent',
                  strokeWidth: 4,
                  discrete: [
                      {
                          fillColor: '#fff',
                          seriesIndex: 0,
                          dataPointIndex: 6,
                          strokeColor: '#ff3f1e',
                          strokeWidth: 2,
                          size: 6,
                          radius: 8
                      }
                  ],
                  hover: { size: 7 }
              },
              grid: { show: false, padding: { right: 8 } },
              colors: ['#ff3f1e'],
              fill: {
                  type: 'gradient',
                  gradient: {
                      shade: 'dark',
                      shadeIntensity: 0.8,
                      opacityFrom: 0.8,
                      opacityTo: 0.25,
                      stops: [0, 85, 100]
                  }
              },
              dataLabels: { enabled: false },
              stroke: { width: 2, curve: 'smooth' },
              series: [
                  {
                      data: {!! json_encode($outcomeSeriesData) !!}.map(item => parseFloat(item.total_amount))
                  }
              ],

              xaxis: {
                  categories: {!! json_encode($outCategories) !!} // Gunakan data categories yang dikirim dari PHP
              },
              yaxis: { show: false },
              tooltip: {
                  enabled: true,
                  x: {
                      format: 'dd MMM yyyy', // Format tanggal yang diinginkan
                  },
                  y: {
                      formatter: function (value) {
                          // Format nilai sebagai mata uang dengan simbol 'Rp.'
                          return 'Rp. ' + value.toLocaleString();
                      }
                  },
              },
          };

          // Inisialisasi dan rendering chart
          const outcomeChart = new ApexCharts(outcomeChartEl, outcomeChartConfig); // Ganti nama variabel
          outcomeChart.render();
      }
  })();
</script>
{{-- // Outcome Chart --}}

<script>
  var sweetAlertData = @json(session('response'));

  if (sweetAlertData.success) {
      Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: sweetAlertData.message,
      }).then(function() {
          // Kode tambahan setelah pengguna menutup SweetAlert
      });
  } else {
      Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'Terjadi kesalahan. ' + sweetAlertData.message, // Tampilkan pesan kesalahan dari server
          customClass: {
              confirmButton: 'btn btn-primary'
          },
          buttonsStyling: false
      });
  }
</script>
@endpush
