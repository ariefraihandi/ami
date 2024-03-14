@extends('Index/app')

@push('head-script')
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/typeahead-js/typeahead.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/apex-charts/apex-charts.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />    
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/card-analytics.css" />    
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Selamat Datang {{$user->name}}! 🎉</h5>
                        <p class="mb-4">
                            Mari Mulai Hari Ini Dengan Semngat Baru
                        </p>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img src="{{ asset('assets') }}/img/illustrations/superman-flying-dark.png" height="140" alt="View Badge User"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 col-lg-4 mb-4">
      <div class="card">
        <div class="d-flex align-items-end row">
          <div class="col-8">
            <div class="card-body">
              <h6 class="card-title mb-1 text-nowrap">Selamat {{$user->name}}</h6>
              <small class="d-block mb-3 text-nowrap">
                  Pendapatan Hari Ini 
                @if ($income > $incomeTotalYes)
                  Lebih Besar dari
                @elseif ($income < $incomeTotalYes)
                  Lebih Kecil dari
                @else
                  Sama dengan
                @endif
                  Kemarin
              </small>
              <h5 class="card-title text-{{ $income > $incomeTotalYes ? 'success' : 'danger' }} mb-1">Rp. {{ number_format($income, 0) }},-</h5>
              @if($incomeTotalYes != 0)
                  <?php $increasePercentage = (($income - $incomeTotalYes) / $incomeTotalYes) * 100; ?>
              @else
                  <?php $increasePercentage = 100;?>
              @endif
              <small class="d-block mb-4 pb-1 text-muted">{{ round($increasePercentage, 2) }}% {{ $income > $incomeTotalYes ? 'Peningkatan' : 'Penururnan' }}</small>
              <a href="{{ route('keuangan.laporan') }}" class="btn btn-sm btn-{{ $income > $incomeTotalYes ? 'success' : 'danger' }}">Lihat Laporan</a>
            </div>
          </div>
          <div class="col-4 pt-3 ps-0">
            <img
            src="{{ asset('assets') }}/img/illustrations/{{ $income > $incomeTotalYes ? 'profit.png' : 'loss.png' }}"
              width="120"
              height="140"
              class="rounded-start"
              alt="View Sales" />
          </div>
        </div>
      </div>
    </div>
    <!-- Pemasukan & Pengeluaran -->
    <div class="col-lg-8 mb-4">
      <div class="card">
        <div class="card-body row g-4">
          <div class="col-md-6 pe-md-4 card-separator">
            <div class="card-title d-flex align-items-start justify-content-between">
              <h5 class="mb-0">Pemasukan</h5>
              <small>Mingguan</small>
            </div>
            <div class="d-flex justify-content-between">
              <div class="mt-auto">
                <h4 class="mb-2">{{number_format($incomeWeekly,0)}},-</h4>
                @php
                if ($incomeLastWeek != 0) {
                    $percentChange = ($incomeWeekly - $incomeLastWeek) / $incomeLastWeek * 100;
                } else {
                    $percentChange = 0; // Atau nilai default yang Anda tentukan
            }
            
            $arrowIcon = $percentChange >= 0 ? 'bx bx-up-arrow-alt' : 'bx bx-down-arrow-alt';
            $textColorClass = $percentChange >= 0 ? 'text-success' : 'text-danger';
            @endphp
            
            <small class="{{ $textColorClass }} text-nowrap fw-medium"><i class="{{ $arrowIcon }}"></i> {{ number_format($percentChange, 2) }}%</small>
            
              </div>
              <div id="pendapatanChart"></div>
            </div>
          </div>
          <div class="col-md-6 ps-md-4">
            <div class="card-title d-flex align-items-start justify-content-between">
              <h5 class="mb-0">Pengeluaran</h5>
              <small>Mingguan</small>
            </div>
            <div class="d-flex justify-content-between">
              <div class="mt-auto">
                <h4 class="mb-2">{{number_format($outcomeWeekly,0)}},-</h4>
                @php
                if ($outcomelastWeek != 0) {
                    $percentCha = ($outcomeWeekly - $outcomelastWeek) / $outcomelastWeek * 100;
                } else {
                    $percentCha = 0; // Atau nilai default yang Anda tentukan
                }
                
                $arrowIon = $percentCha >= 0 ? 'bx bx-up-arrow-alt' : 'bx bx-down-arrow-alt';
                $textColor = $percentCha <= 0 ? 'text-success' : 'text-danger';
                @endphp
                
                <small class="{{ $textColor }} text-nowrap fw-medium"><i class="{{ $arrowIon }}"></i> {{ number_format($percentCha, 2) }}%</small>
              </div>
              <div id="pengeluaranChart"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ New Visitors & Activity -->

    <div class="col-md-12 col-lg-4">
      <div class="row">
        <div class="col-lg-6 col-md-3 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <img
                    src="{{ asset('assets') }}/img/icons/unicons/inv-success.png"
                    alt="Credit Card"
                    class="rounded" />
                </div>
                <div class="dropdown">
                  <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown" aria-haspopup="true"aria-expanded="false"><i class="bx bx-dots-vertical-rounded"></i></button>
                  <div class="dropdown-menu" aria-labelledby="cardOpt6">
                    <a class="dropdown-item" href="{{ url('/keuangan/laporan') }}?startDate={{ $startingMonth }}&endDate={{ $endMonth }}" target="_blank">Lihat Laporan</a>
                  </div>
                </div>
              </div>
              <span class="d-block">Income {{$bulan}}</span>
              <h6 class="card-title mb-1">Rp. {{ number_format($totalInvPaid, 0, ',', '.') }}</h6>                            
                @php
                  $incomeMounthlyPercentage = ($totalInvMouthly == 0) ? 0 : ($totalInvPaid / $totalInvMouthly) * 100;
                @endphp                 
                <div id="incomeMonthlyChart" class="mb-2"></div>            
              </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-3 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <img
                    src="{{ asset('assets') }}/img/icons/unicons/inv-danger.png"
                    alt="Credit Card"
                    class="rounded" />
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="cardOpt6"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="cardOpt6">
                    <a class="dropdown-item" href="{{ url('/keuangan/laporan') }}?startDate={{ $startingMonth }}&endDate={{ $endMonth }}" target="_blank">Lihat Laporan</a>
                  </div>
                </div>
              </div>
              @php
                $bonMonthlyPercentage = ($totalBonMonthly == 0) ? 0 : ($totalBonMonthly / 50000000) * 100;                                
              @endphp              
              <span class="d-block">Bon Inv {{$bulan}}</span>
              <h6 class="card-title mb-1">Rp. {{ number_format($totalBonMonthly, 0, ',', '.') }}</h6>
              <div id="bonMonthlyChart" class="mb-2"></div>          
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-12 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between gap-3">
                <div class="d-flex align-items-start flex-column justify-content-between">
                  <div class="card-title">
                    <h5 class="mb-0">Pendapatan Mingguan</h5>
                  </div>
                  <div class="d-flex justify-content-between">
                    <div class="mt-auto">
                      <h5 class="mb-0">{{ number_format($incomeWeekly, 0, ',', '.') }}</h5>
                      @php
                      if ($incomeLastWeek != 0) {
                          $percentChange = ($incomeWeekly - $incomeLastWeek) / $incomeLastWeek * 100;
                      } else {
                          $percentChange = 0; // Atau nilai default yang Anda tentukan
                  }
                  
                  $arrowIcon = $percentChange >= 0 ? 'bx bx-up-arrow-alt' : 'bx bx-down-arrow-alt';
                  $textColorClass = $percentChange >= 0 ? 'text-success' : 'text-danger';
                  @endphp
                  
                  <small class="{{ $textColorClass }} text-nowrap fw-medium"><i class="{{ $arrowIcon }}"></i> {{ number_format($percentChange, 2) }}%</small>
                    </div>
                  </div>
                  <span class="badge bg-label-secondary rounded-pill">{{$bulan}}</span>
                </div>
                <div id="pendapatanBarChart"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>    
     <!-- Conversion rate -->
     <div class="col-md-6 col-lg-4 mb-4">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div class="card-title mb-0">
            <h5 class="m-0 me-2">Pendapatan {{$bulan}}</h5>
            <small class="text-muted">Compared To Last Month</small>
          </div>
          <div class="dropdown">
            <button
              class="btn p-0"
              type="button"
              id="conversionRate"
              data-bs-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false">
              <i class="bx bx-dots-vertical-rounded"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="conversionRate">
              <a class="dropdown-item" href="{{ url('/keuangan/laporan') }}?startDate={{ $startingMonth }}&endDate={{ $endMonth }}" target="_blank">Lihat Laporan</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex flex-row align-items-center gap-1 mb-4">
              <h4 class="mb-2">{{ number_format($incomeThisMonth, 0, ',', '.') }}</h4>
              @php
                $incomeGrowthPercentage = ($incomeLastMonth == 0) ? 0 : (($incomeThisMonth - $incomeLastMonth) / $incomeLastMonth) * 100;
                $iconClass = ($incomeGrowthPercentage >= 0) ? 'bx-chevron-up text-success' : 'bx-chevron-down text-danger';
                $growthPercentage = abs($incomeGrowthPercentage); 
              @endphp
              <small class="fw-medium">
                <i class="bx {{ $iconClass }}"></i>
                {{ number_format($growthPercentage, 1) }}%
              </small>
            </div>
            <div id="conversionRateChart"></div>
          </div>
          <ul class="p-0 m-0">
            @php
              $OprPercentage = ($operationalLastMo == 0) ? 0 : (($operationalThisMo - $operationalLastMo) / $operationalLastMo) * 100;
            @endphp
            <li class="d-flex mb-4">
              <div class="d-flex w-100 flex-wrap justify-content-between gap-2">
                  <div class="me-2">
                      <h6 class="mb-0">Operational {{$bulan}}</h6>
                      <small class="text-muted">Rp. {{ number_format($operationalThisMo, 0, ',', '.') }}</small>
                  </div>
                  <div class="user-progress">                  
                      @if($OprPercentage >= 0)
                          <i class="bx bx-up-arrow-alt text-success me-2"></i>
                      @else
                          <i class="bx bx-down-arrow-alt text-danger me-2"></i>
                      @endif
                      <span>{{ number_format(abs($OprPercentage), 1) }}%</span>
                  </div>
              </div>
            </li>
            @php
              $topUpPercentage = ($topUpLastMonth == 0) ? 0 : (($topUpMonthly - $topUpLastMonth) / $topUpLastMonth) * 100;
            @endphp
            <li class="d-flex mb-4">
              <div class="d-flex w-100 flex-wrap justify-content-between gap-2">
                  <div class="me-2">
                      <h6 class="mb-0">Top Up {{$bulan}}</h6>
                      <small class="text-muted">Rp. {{ number_format($topUpMonthly, 0, ',', '.') }}</small>
                  </div>
                  <div class="user-progress">
                      @if($topUpPercentage >= 0)
                          <i class="bx bx-up-arrow-alt text-success me-2"></i>
                      @else
                          <i class="bx bx-down-arrow-alt text-danger me-2"></i>
                      @endif
                      <span>{{ number_format(abs($topUpPercentage), 1) }}%</span>
                  </div>
              </div>
            </li>
            @php
              $outcomePercentage = ($outcomeLastMount == 0) ? 0 : (($outcomeMountly - $outcomeLastMount) / $outcomeLastMount) * 100;
            @endphp
            <li class="d-flex mb-4">
              <div class="d-flex w-100 flex-wrap justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Pengeluaran {{$bulan}}</h6>
                  <small class="text-muted">Rp. {{ number_format($outcomeMountly, 0, ',', '.') }}</small>
                </div>
                <div class="user-progress">
                  @if($outcomePercentage >= 0)
                      <i class="bx bx-up-arrow-alt text-danger me-2"></i>
                  @else
                      <i class="bx bx-down-arrow-alt text-success me-2"></i>
                  @endif
                  <span>{{ number_format(abs($outcomePercentage), 1) }}%</span>
                </div>
              </div>
            </li>  
            @php
              $setorkasPercentage = ($setorKasLastMonth == 0) ? 0 : (($setorKasMonthly - $setorKasLastMonth) / $setorKasLastMonth) * 100;
            @endphp          
            <li class="d-flex mb-4">
              <div class="d-flex w-100 flex-wrap justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Setor Kas {{$bulan}}</h6>
                  <small class="text-muted">Rp. {{ number_format($setorKasMonthly, 0, ',', '.') }}</small>
                </div>
                <div class="user-progress">
                  @if($setorkasPercentage >= 0)
                      <i class="bx bx-up-arrow-alt text-danger me-2"></i>
                  @else
                      <i class="bx bx-down-arrow-alt text-success me-2"></i>
                  @endif
                  <span>{{ number_format(abs($setorkasPercentage), 1) }}%</span>
                </div>
              </div>
            </li>            
          </ul>
        </div>
      </div>
    </div>
    <!--/ Conversion rate -->
    <div class="col-md-12 col-lg-4">
      <div class="row">  
        <div class="col-lg-6 col-md-3 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <img
                    src="{{ asset('assets') }}/img/icons/unicons/wallet.png"
                    alt="Credit Card"
                    class="rounded" />
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="cardOpt6"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="cardOpt6">
                    <a class="dropdown-item" href="{{ url('/keuangan/laporan') }}?startDate={{ $startingMonth }}&endDate={{ $endMonth }}" target="_blank">Lihat Laporan</a>
                  </div>
                </div>
              </div>
              <span class="d-block">Margin {{$bulan}}</span>
              <h6 class="card-title mb-1">Rp. {{ number_format($marginMonthly, 0, ',', '.') }}</h6>   
              @php
                $marginPercentage = ($marginLastMonth == 0) ? 0 : (($marginMonthly - $marginLastMonth) / $marginLastMonth) * 100;
              @endphp
            <div id="marginChart" class="mb-2"></div>
            </div>
          </div>
        </div>      
        <div class="col-lg-6 col-md-3 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <img
                    src="{{ asset('assets') }}/img/icons/unicons/wallet-up-round.png"
                    alt="Credit Card"
                    class="rounded" />
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="cardOpt6"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="cardOpt6">
                    <a class="dropdown-item" href="{{ url('/keuangan/laporan') }}?startDate={{ $startingMonth }}&endDate={{ $endMonth }}" target="_blank">Lihat Laporan</a>
                  </div>
                </div>
              </div>
              <span class="d-block">Sisa Kas {{$bulan}}</span>
              <h6 class="card-title mb-1">Rp. {{ number_format($sisaSaldo, 0, ',', '.') }}</h6>   
            
              @php
                $lastMountOutPercentage = ($sisaSaldoPast == 0) ? 0 : (($sisaSaldo - $sisaSaldoPast) / $sisaSaldoPast) * 100;
              @endphp
              <div id="outcomeChart" class="mb-2"></div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-12 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between gap-3">
                <div class="d-flex align-items-start flex-column justify-content-between">
                  <div class="card-title">
                    <h5 class="mb-0">Margin Mingguan</h5>
                  </div>
                  <div class="d-flex justify-content-between">
                    <div class="mt-auto">
                      <h5 class="mb-0">{{ number_format($incomeWeekly, 0, ',', '.') }}</h5>
                      @php
                        if ($incomeLastWeek != 0) {
                                $percentChange = ($incomeWeekly - $incomeLastWeek) / $incomeLastWeek * 100;
                            } else {
                                $percentChange = 0; // Atau nilai default yang Anda tentukan
                        }
                        
                        $arrowIcon = $percentChange >= 0 ? 'bx bx-up-arrow-alt' : 'bx bx-down-arrow-alt';
                        $textColorClass = $percentChange >= 0 ? 'text-success' : 'text-danger';
                      @endphp
                  
                  <small class="{{ $textColorClass }} text-nowrap fw-medium"><i class="{{ $arrowIcon }}"></i> {{ number_format($percentChange, 2) }}%</small>
                    </div>
                  </div>
                  <span class="badge bg-label-secondary rounded-pill">{{$bulan}}</span>
                </div>
                <div id="marginBarChart"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12 col-lg-4">
      <div class="row">
        <div class="col-lg-6 col-md-3 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <img
                    src="{{ asset('assets') }}/img/icons/unicons/wallet-info.png"
                    alt="Credit Card"
                    class="rounded" />
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="cardOpt6"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="cardOpt6">                    
                    <a class="dropdown-item" href="{{ url('/keuangan/laporan') }}?startDate={{ $startingYear }}&endDate={{ $endingYear }}" target="_blank">Lihat Laporan</a>         
                  </div>
                </div>
              </div>
              <span class="d-block">Penjualan {{$currentYear}}</span>
              <h5 class="card-title mb-1">{{ number_format($totalInvPaidYear, 0, ',', '.') }},-</h5>       
              @php
                $incomeYearlyPercentage = ($totalInvPaidLastYear == 0) ? 0 : (($totalInvPaidYear - $totalInvPaidLastYear) / $totalInvPaidLastYear) * 100;
                $iconClass = ($incomeYearlyPercentage >= 0) ? 'bx bx-up-arrow-alt text-success' : 'bx bx-down-arrow-alt text-danger';
                $percentage = number_format(abs($incomeYearlyPercentage), 2);
                $arrow = ($incomeYearlyPercentage >= 0) ? '+' : '-';
              @endphp
              
              <small class="fw-medium"><i class="{{ $iconClass }}"></i> {{ $arrow }}{{ $percentage }}%</small>
          
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-3 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <img
                    src="{{ asset('assets') }}/img/icons/unicons/inv-danger.png"
                    alt="Credit Card"
                    class="rounded" />
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="cardOpt6"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="cardOpt6">                    
                    <a class="dropdown-item" href="{{ url('/keuangan/laporan') }}?startDate={{ $startingYear }}&endDate={{ $endingYear }}" target="_blank">Lihat Laporan</a>         
                  </div>
                </div>
              </div>
              <span class="d-block">Bon {{$currentYear}}</span>
              <h5 class="card-title mb-1">{{ number_format($totalBonYearly, 0, ',', '.') }},-</h5>       
              @php                
                $bonYearlyPercentage  = ($totalBonYearly == 0) ? 0 : ($totalBonYearly / 50000000) * 100;       
                $iconClassBon         = ($bonYearlyPercentage >= 0) ? 'bx bx-up-arrow-alt text-danger' : 'bx bx-down-arrow-alt text-success';
                $percentageBon        = number_format(abs($bonYearlyPercentage), 2);
                $arrowBon             = ($bonYearlyPercentage >= 0) ? '+' : '-';
              @endphp            
              <small class="fw-medium"><i class="{{ $iconClassBon }}"></i> {{ $arrowBon }}{{ $percentageBon }}%</small>  
            </div>
          </div>
        </div>
        
        <div class="col-lg-6 col-md-3 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <img
                    src="{{ asset('assets') }}/img/icons/unicons/wallet-info.png"
                    alt="Credit Card"
                    class="rounded" />
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="cardOpt6"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="cardOpt6">                    
                    <a class="dropdown-item" href="{{ url('/keuangan/laporan') }}?startDate={{ $startingYear }}&endDate={{ $endingYear }}" target="_blank">Lihat Laporan</a>         
                  </div>
                </div>
              </div>
              <span class="d-block">Invoice {{$currentYear}}</span>
              <h4 class="card-title mb-1">{{$invYear}} Invoice</h4>       
              @php
                $invYearlyPercentage = ($invLastYear == 0) ? 0 : (($invYear - $invLastYear) / $invLastYear) * 100;
                $iconClassInv = ($invYearlyPercentage >= 0) ? 'bx bx-up-arrow-alt text-success' : 'bx bx-down-arrow-alt text-danger';
                $percentageInv = number_format(abs($invYearlyPercentage), 2);
                $arrowInv = ($invYearlyPercentage >= 0) ? '+' : '-';
              @endphp
              <small class="fw-medium"><i class="{{ $iconClassInv }}"></i> {{ $arrowInv }}{{ $percentageInv }}%</small>
            </div>
          </div>
        </div>
      <div class="col-lg-6 col-md-3 col-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <img
                  src="{{ asset('assets') }}/img/icons/unicons/inv-danger.png"
                  alt="Credit Card"
                  class="rounded" />
              </div>
              <div class="dropdown">
                <button
                  class="btn p-0"
                  type="button"
                  id="cardOpt1"
                  data-bs-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt1">
                  <a class="dropdown-item" href="{{ url('/keuangan/laporan') }}?startDate={{ $startingYear }}&endDate={{ $endingYear }}" target="_blank">Lihat Laporan</a>         
                </div>
              </div>
            </div>
            <span class="d-block">Invoice Bon {{$currentYear}}</span>
            <h4 class="card-title mb-1">{{$invBonYear}} Invoice</h4>
            <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.14%</small>
          </div>
        </div>
      </div>
    </div>
    </div>
    <!-- Total Income -->
    <div class="col-md-12 col-lg-8 mb-4">
      <div class="card">
        <div class="row row-bordered g-0">
          <div class="col-md-8">
            <div class="card-header">
              <h5 class="card-title mb-0">Total Income</h5>
              <small class="card-subtitle">Yearly report overview</small>
            </div>
            <div class="card-body">
              <div id="totalIncomeChart"></div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card-header d-flex justify-content-between">
              <div>
                <h5 class="card-title mb-0">Report</h5>
                <small class="card-subtitle">Monthly Avg. $45.578k</small>
              </div>
              <div class="dropdown">
                <button
                  class="btn p-0"
                  type="button"
                  id="totalIncome"
                  data-bs-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalIncome">
                  <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                  <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                  <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="report-list">
                <div class="report-list-item rounded-2 mb-3">
                  <div class="d-flex align-items-start">
                    <div class="report-list-icon shadow-sm me-2">
                      <img
                        src="{{ asset('assets') }}/svg/icons/paypal-icon.svg"
                        width="22"
                        height="22"
                        alt="Paypal" />
                    </div>
                    <div class="d-flex justify-content-between align-items-end w-100 flex-wrap gap-2">
                      <div class="d-flex flex-column">
                        <span>Income</span>
                        <h5 class="mb-0">$42,845</h5>
                      </div>
                      <small class="text-success">+2.34k</small>
                    </div>
                  </div>
                </div>
                <div class="report-list-item rounded-2 mb-3">
                  <div class="d-flex align-items-start">
                    <div class="report-list-icon shadow-sm me-2">
                      <img
                        src="{{ asset('assets') }}/svg/icons/shopping-bag-icon.svg"
                        width="22"
                        height="22"
                        alt="Shopping Bag" />
                    </div>
                    <div class="d-flex justify-content-between align-items-end w-100 flex-wrap gap-2">
                      <div class="d-flex flex-column">
                        <span>Expense</span>
                        <h5 class="mb-0">$38,658</h5>
                      </div>
                      <small class="text-danger">-1.15k</small>
                    </div>
                  </div>
                </div>
                <div class="report-list-item rounded-2">
                  <div class="d-flex align-items-start">
                    <div class="report-list-icon shadow-sm me-2">
                      <img
                        src="{{ asset('assets') }}/svg/icons/wallet-icon.svg"
                        width="22"
                        height="22"
                        alt="Wallet" />
                    </div>
                    <div class="d-flex justify-content-between align-items-end w-100 flex-wrap gap-2">
                      <div class="d-flex flex-column">
                        <span>Profit</span>
                        <h5 class="mb-0">$18,220</h5>
                      </div>
                      <small class="text-success">+1.35k</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--/ Total Income -->
    </div>
    <!--/ Total Income -->
  </div>
  <div class="row">
    <div class="col-md-6 col-lg-8 mb-4 mb-md-0">
      <div class="card">
        <div class="table-responsive text-nowrap">
          <table class="table text-nowrap">
            <thead>
              <tr>
                <th>Invoice</th>
                <th>Constumer</th>
                <th>Pembayaran</th>
                <th>Order Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              @foreach($invoiceData as $invoice)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                          @php
                              $imagePath = '';
                              if (Str::startsWith($invoice->invoice_number, 'S')) {
                                  $imagePath = 'sales.png';
                              } elseif (Str::startsWith($invoice->invoice_number, 'P')) {
                                  $imagePath = 'project.png';
                              }
                          @endphp
                          <img src="{{ asset('assets/img/icons/unicons/' . $imagePath) }}" alt="{{ $invoice->invoice_number }}" height="32" width="32" class="me-2" />
                          <div class="d-flex flex-column">
                              <span class="fw-medium lh-1">{{ $invoice->invoice_number }}</span>
                              <small class="text-muted">{{ $invoice->invoice_name }}</small>
                          </div>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex align-items-center">
                          @php
                              $imagePath = '';
                              $customer = \App\Models\Customer::where('uuid', $invoice->customer_uuid)->first();
                              
                              if ($customer) {
                                  if ($customer->customer_type == 'individual') {
                                      $imagePath = 'user.png';
                                  } elseif ($customer->customer_type == 'biro') {
                                      $imagePath = 'biro.png';
                                  } elseif ($customer->customer_type == 'instansi') {
                                      $imagePath = 'instansi.png';
                                  }
                              }
                          @endphp
                          <img src="{{ asset('assets/img/icons/unicons/' . $imagePath) }}" alt="{{ $invoice->invoice_number }}" height="32" width="32" class="me-2" />
                          <div class="d-flex flex-column">
                              <span class="fw-medium lh-1">{{ $customer->name }}</span>
                              <small class="text-muted">{{ ucfirst($customer->customer_type) }}</small>
                          </div>
                      </div>
                    </td>  
                    <td>
                      <div class="text-muted lh-1">
                        <span class="text-primary fw-medium">Rp. {{ number_format($invoice->total_amount, 0) }}</span>/{{ number_format($invoice->panjar_amount, 0) }}
                      </div>
                      <small class="text-muted">
                        @if($invoice->status == 0)
                            Belum Bayar
                        @elseif($invoice->status == 1)
                            Panjar
                        @endif
                      </small>   
                    </td>
                    <td>
                      @if($invoice->status == 0)
                          <div class="progress">
                              <div class="progress-bar bg-danger" role="progressbar" style="width: 10%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">                                
                              </div>
                          </div>
                      @elseif($invoice->status == 1)
                          @php
                              $percentage = ($invoice->panjar_amount / $invoice->total_amount) * 100;
                          @endphp
                          <div class="progress">
                              <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                  {{ $percentage }}%
                              </div>
                          </div>
                      @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-1"></i> View Details</a>
                                <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                  </tr>
              @endforeach
              </tbody>
              
          </table>
        </div>
      </div>
    </div>
    <!-- Total Balance -->
    <div class="col-md-6 col-lg-4">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Total Balance</h5>
          <div class="dropdown">
            <button
              class="btn p-0"
              type="button"
              id="totalBalance"
              data-bs-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false">
              <i class="bx bx-dots-vertical-rounded"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalBalance">
              <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-start">
            <div class="d-flex pe-4">
              <div class="me-3">
                <span class="badge bg-label-warning p-2"><i class="bx bx-wallet text-warning"></i></span>
              </div>
              <div>
                <h6 class="mb-0">$2.54k</h6>
                <small>Wallet</small>
              </div>
            </div>
            <div class="d-flex">
              <div class="me-3">
                <span class="badge bg-label-secondary p-2"
                  ><i class="bx bx-dollar text-secondary"></i
                ></span>
              </div>
              <div>
                <h6 class="mb-0">$4.2k</h6>
                <small>Paypal</small>
              </div>
            </div>
          </div>
          <div id="totalBalanceChart" class="border-bottom mb-3"></div>
          <div class="d-flex justify-content-between">
            <small class="text-muted"
              >You have done <span class="fw-medium">57.6%</span> more sales.<br />Check your new badge in
              your profile.</small
            >
            <div>
              <span class="badge bg-label-warning p-2"
                ><i class="bx bx-chevron-right text-warning scaleX-n1-rtl"></i
              ></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Total Balance -->
  </div>
</div>
@endsection

@push('footer-script')
  <script src="{{ asset('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>  
  <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('footer-Sec-script')
<script>         
  var totIncomeSen   = {{ $totIncomeSen }};   
  var totIncomeSel   = {{ $totIncomeSel }};   
  var totIncomeRab   = {{ $totIncomeRab }};   
  var totIncomeKam   = {{ $totIncomeKam }};   
  var totIncomeJum   = {{ $totIncomeJum }};   
  var totIncomeSab   = {{ $totIncomeSab }};   
  
  var totOutcomeSen   = {{ $totOutcomeSen }};   
  var totOutcomeSel   = {{ $totOutcomeSel }};   
  var totOutcomeRab   = {{ $totOutcomeRab }};   
  var totOutcomeKam   = {{ $totOutcomeKam }};   
  var totOutcomeJum   = {{ $totOutcomeJum }};   
  var totOutcomeSab   = {{ $totOutcomeSab }};   
  
  var incomeJanYear   = {{ $incomeJanYear }};   
  var incomeFebYear   = {{ $incomeFebYear }};   
  var incomeMarYear   = {{ $incomeMarYear }};   
  var incomeAprYear   = {{ $incomeAprYear }};   
  var incomeMayYear   = {{ $incomeMayYear }};   
  var incomeJunYear   = {{ $incomeJunYear }};   
  var incomeJulYear   = {{ $incomeJulYear }};   
  var incomeAugYear   = {{ $incomeAugYear }};   
  var incomeSepYear   = {{ $incomeSepYear }};   
  var incomeOctYear   = {{ $incomeOctYear }};   
  var incomeNovYear   = {{ $incomeNovYear }};   
  var incomeDecYear   = {{ $incomeDecYear }};   
  
  var bulan           = {!! json_encode($bulan) !!};
  var bulanLalu       = {!! json_encode($bulanLalu) !!};
  var duaBulanLalu    = {!! json_encode($duaBulanLalu) !!};

  
  var incomeWeek1 = {{ number_format($incomeWeek1, 0, '.', '') }};
  var incomeWeek2 = {{ number_format($incomeWeek2, 0, '.', '') }};
  var incomeWeek3 = {{ number_format($incomeWeek3, 0, '.', '') }};
  var incomeWeek4 = {{ number_format($incomeWeek4, 0, '.', '') }};
  
  var incPastWeek1 = {{ number_format($incPastWeek1, 0, '.', '') }};
  var incPastWeek2 = {{ number_format($incPastWeek2, 0, '.', '') }};
  var incPastWeek3 = {{ number_format($incPastWeek3, 0, '.', '') }};
  var incPastWeek4 = {{ number_format($incPastWeek4, 0, '.', '') }};
  
  var marginWeek1 = {{ number_format($marginWeek1, 0, '.', '') }};
  var marginWeek2 = {{ number_format($marginWeek2, 0, '.', '') }};
  var marginWeek3 = {{ number_format($marginWeek3, 0, '.', '') }};
  var marginWeek4 = {{ number_format($marginWeek4, 0, '.', '') }};
  
  var margPastWeek1 = {{ number_format($margPastWeek1, 0, '.', '') }};
  var margPastWeek2 = {{ number_format($margPastWeek2, 0, '.', '') }};
  var margPastWeek3 = {{ number_format($margPastWeek3, 0, '.', '') }};
  var margPastWeek4 = {{ number_format($margPastWeek4, 0, '.', '') }};


  var incomeThisMonth = {{ number_format($incomeThisMonth, 0, '.', '') }};
  var incomeLastMonth = {{ number_format($incomeLastMonth, 0, '.', '') }};
  var incomweToMonth  = {{ number_format($incomweToMonth, 0, '.', '') }};

  var incomeMonthly     = {{ number_format($incomeMounthlyPercentage, 0, '.', '') }};
  var bonMonthly        = {{ number_format($bonMonthlyPercentage, 0, '.', '') }};
  var setorkasMonthly   = {{ number_format($setorkasPercentage, 0, '.', '') }};
  var pengeluaran       = {{ number_format($lastMountOutPercentage, 0, '.', '') }};
  var margin            = {{ number_format($marginPercentage, 0, '.', '') }};
  
  
</script>
<script src="{{ asset('assets') }}/js/app-ecommerce-dashboard.js"></script>
<script>
  @if(session('response'))
      var response = @json(session('response'));
      showSweetAlert(response);
  @endif
</script>  
@endpush
