<div>
    <div class="container-xxl flex-grow-1 container-p-y">
  
      <!-- Card Border Shadow -->
      <div class="row">
        <div class="col-sm-6 col-lg-3 mb-4">
          <div class="card card-border-shadow-success h-100">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                  <span class="avatar-initial rounded bg-label-success"><i class="bx bxs-group"></i></span>
                </div>
                <h4 class="ms-1 mb-0">{{ $users['admin'] }}</h4>
              </div>
              <p class="mb-1">{{ __('label.admins_count') }}</p>
              <p class="mb-0">
                <!-- <span class="fw-medium me-1">+18.2%</span>
                <small class="text-muted">than last week</small> -->
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
          <div class="card card-border-shadow-primary h-100">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                  <span class="avatar-initial rounded bg-label-primary"><i class="bx bxs-group"></i></span>
                </div>
                <h4 class="ms-1 mb-0">{{ $users['reviewer'] }}</h4>
              </div>
              <p class="mb-1">{{ __('label.reviewers_count') }}</p>
              <p class="mb-0">
                <!-- <span class="fw-medium me-1">-8.7%</span>
                <small class="text-muted">than last week</small> -->
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
          <div class="card card-border-shadow-info h-100">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                  <span class="avatar-initial rounded bg-label-info"
                    ><i class="bx bxs-group"></i
                  ></span>
                </div>
                <h4 class="ms-1 mb-0">{{ $users['author'] }}</h4>
              </div>
              <p class="mb-1">{{ __('label.authors_count') }}</p>
              <!-- <p class="mb-0">
                <span class="fw-medium me-1">+4.3%</span>
                <small class="text-muted">than last week</small>
              </p> -->
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
          <div class="card card-border-shadow-info h-100">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                  <span class="avatar-initial rounded bg-label-info"><i class="bx bxs-group"></i></span>
                </div>
                <h4 class="ms-1 mb-0">{{ $users['all'] }}</h4>
              </div>
              <p class="mb-1">{{ __('label.users_count') }}</p>
              <!-- <p class="mb-0">
                <span class="fw-medium me-1">-2.5%</span>
                <small class="text-muted">than last week</small>
              </p> -->
            </div>
          </div>
        </div>
      </div>
      <!--/ Card Border Shadow -->
      <div class="row">
        <!-- Vehicles overview -->
        <!-- <div class="col-xxl-6 mb-4 order-5 order-xxl-0">
          <div class="card h-100">
            <div class="card-header">
              <div class="card-title mb-0">
                <h5 class="m-0">Vehicles overview</h5>
              </div>
            </div>
            <div class="card-body">
              <div class="d-none d-lg-flex vehicles-progress-labels mb-3">
                <div class="vehicles-progress-label on-the-way-text" style="width: 39.7%">On the way</div>
                <div class="vehicles-progress-label unloading-text" style="width: 28.3%">Unloading</div>
                <div class="vehicles-progress-label loading-text" style="width: 17.4%">Loading</div>
                <div class="vehicles-progress-label waiting-text" style="width: 14.6%">Waiting</div>
              </div>
              <div class="vehicles-overview-progress progress mb-3" style="height: 46px">
                <div
                  class="progress-bar fs-big fw-medium text-start bg-body text-body px-1 px-lg-3"
                  role="progressbar"
                  style="width: 39.7%"
                  aria-valuenow="39.7"
                  aria-valuemin="0"
                  aria-valuemax="100">
                  39.7%
                </div>
                <div
                  class="progress-bar fs-big fw-medium text-start bg-primary px-1 px-lg-3"
                  role="progressbar"
                  style="width: 28.3%"
                  aria-valuenow="28.3"
                  aria-valuemin="0"
                  aria-valuemax="100">
                  28.3%
                </div>
                <div
                  class="progress-bar fs-big fw-medium text-start text-bg-info px-1 px-lg-3"
                  role="progressbar"
                  style="width: 17.4%"
                  aria-valuenow="17.4"
                  aria-valuemin="0"
                  aria-valuemax="100">
                  17.4%
                </div>
                <div
                  class="progress-bar fs-big fw-medium text-start bg-gray-900 px-1 px-lg-3"
                  role="progressbar"
                  style="width: 14.6%"
                  aria-valuenow="14.6"
                  aria-valuemin="0"
                  aria-valuemax="100">
                  14.6%
                </div>
              </div>
              <div class="table-responsive">
                <table class="table card-table">
                  <tbody class="table-border-bottom-0">
                    <tr>
                      <td class="w-50 ps-0">
                        <div class="d-flex justify-content-start align-items-center">
                          <div class="me-2">
                            <i class="bx bxs-truck"></i>
                          </div>
                          <h6 class="mb-0 fw-normal">On the way</h6>
                        </div>
                      </td>
                      <td class="text-end pe-0 text-nowrap">
                        <h6 class="mb-0">2hr 10min</h6>
                      </td>
                      <td class="text-end pe-0">
                        <span class="fw-medium">39.7%</span>
                      </td>
                    </tr>
                    <tr>
                      <td class="w-50 ps-0">
                        <div class="d-flex justify-content-start align-items-center">
                          <div class="me-2">
                            <i class="bx bx-down-arrow-circle"></i>
                          </div>
                          <h6 class="mb-0 fw-normal">Unloading</h6>
                        </div>
                      </td>
                      <td class="text-end pe-0 text-nowrap">
                        <h6 class="mb-0">3hr 15min</h6>
                      </td>
                      <td class="text-end pe-0">
                        <span class="fw-medium">28.3%</span>
                      </td>
                    </tr>
                    <tr>
                      <td class="w-50 ps-0">
                        <div class="d-flex justify-content-start align-items-center">
                          <div class="me-2">
                            <i class="bx bx-up-arrow-circle"></i>
                          </div>
                          <h6 class="mb-0 fw-normal">Loading</h6>
                        </div>
                      </td>
                      <td class="text-end pe-0 text-nowrap">
                        <h6 class="mb-0">1hr 24min</h6>
                      </td>
                      <td class="text-end pe-0">
                        <span class="fw-medium">17.4%</span>
                      </td>
                    </tr>
                    <tr>
                      <td class="w-50 ps-0">
                        <div class="d-flex justify-content-start align-items-center">
                          <div class="me-2">
                            <i class="bx bx-time-five"></i>
                          </div>
                          <h6 class="mb-0 fw-normal">Waiting</h6>
                        </div>
                      </td>
                      <td class="text-end pe-0 text-nowrap">
                        <h6 class="mb-0">5hr 19min</h6>
                      </td>
                      <td class="text-end pe-0">
                        <span class="fw-medium">14.6%</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div> -->
        <!--/ Vehicles overview -->
        <!-- Shipment statistics-->
        <!-- <div class="col-lg-6 col-xxl-6 mb-4 order-3 order-xxl-1">
          <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
              <div class="card-title mb-0">
                <h5 class="m-0 me-2">Shipment statistics</h5>
                <small class="text-muted">Total number of deliveries 23.8k</small>
              </div>
              <div class="dropdown">
                <button
                  type="button"
                  class="btn btn-label-primary dropdown-toggle"
                  data-bs-toggle="dropdown"
                  aria-expanded="false">
                  January
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="javascript:void(0);">January</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">February</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">March</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">April</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">May</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">June</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">July</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">August</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">September</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">October</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">November</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">December</a></li>
                </ul>
              </div>
            </div>
            <div class="card-body">
              <div id="shipmentStatisticsChart"></div>
            </div>
          </div>
        </div> -->
        <!--/ Shipment statistics -->
        <!-- Delivery Performance -->
        <div class="col-lg-6 col-xxl-6 mb-4 order-2 order-xxl-2">
          <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
              <div class="card-title mb-0">
                <h5 class="m-0 me-2">{{ __('label.articles') }}</h5>
                <!-- <small class="text-muted">{{ __('label.articles_overview') }}</small> -->
              </div>
              <div class="dropdown">
                <button
                  class="btn p-0"
                  type="button"
                  id="deliveryPerformance"
                  data-bs-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="deliveryPerformance">
                  <!-- <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                  <a class="dropdown-item" href="javascript:void(0);">Refresh</a> -->
                </div>
              </div>
            </div>
            <div class="card-body">
              <ul class="p-0 m-0">
                <li class="d-flex mb-4 pb-1">
                  <div class="avatar flex-shrink-0 me-3">
                    <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-file"></i></span>
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-1 fw-normal">{{ __('label.submitted_articles') }}</h6>
                      <!-- <small class="text-success fw-normal d-block">
                        <i class="bx bx-chevron-up"></i>
                        25.8%
                      </small> -->
                    </div>
                    <div class="user-progress">
                      <h6 class="mb-0 text-primary">{{ $status['submitted'] }}</h6>
                    </div>
                  </div>
                </li>
                <li class="d-flex mb-4 pb-1">
                  <div class="avatar flex-shrink-0 me-3">
                    <span class="avatar-initial rounded bg-label-secondary"><i class="bx bx-file"></i></span>
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-1 fw-normal">{{ __('label.initial_review_articles') }}</h6>
                      <!-- <small class="text-success fw-normal d-block">
                        <i class="bx bx-chevron-up"></i>
                        4.3%
                      </small> -->
                    </div>
                    <div class="user-progress">
                      <h6 class="mb-0 text-secondary">{{ $status['screening'] }}</h6>
                    </div>
                  </div>
                </li>
                <li class="d-flex mb-4 pb-1">
                  <div class="avatar flex-shrink-0 me-3">
                    <span class="avatar-initial rounded bg-label-info"
                      ><i class="bx bx-file"></i
                    ></span>
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-1 fw-normal">{{ __('label.under_review_articles') }}</h6>
                      <!-- <small class="text-danger fw-normal d-block">
                        <i class="bx bx-chevron-down"></i>
                        12.5
                      </small> -->
                    </div>
                    <div class="user-progress">
                      <h6 class="mb-0 text-info">{{ $status['under_review'] }}</h6>
                    </div>
                  </div>
                </li>

                <li class="d-flex mb-4 pb-1">
                  <div class="avatar flex-shrink-0 me-3">
                    <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-file"></i></span>
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-1 fw-normal">{{ __('label.revised_articles') }}</h6>
                      <!-- <small class="text-success fw-normal d-block">
                        <i class="bx bx-chevron-up"></i>
                        35.6%
                      </small> -->
                    </div>
                    <div class="user-progress">
                      <h6 class="mb-0 text-warning">{{ $status['revision_required'] }}</h6>
                    </div>
                  </div>
                </li>
                <li class="d-flex mb-4 pb-1">
                  <div class="avatar flex-shrink-0 me-3">
                    <span class="avatar-initial rounded bg-label-success"><i class="bx bx-file"></i></span>
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-1 fw-normal">{{ __('label.accepted_articles') }}</h6>
                      <!-- <small class="text-success fw-normal d-block">
                        <i class="bx bx-chevron-up"></i>
                        35.6%
                      </small> -->
                    </div>
                    <div class="user-progress">
                      <h6 class="mb-0 text-success">{{ $status['accepted'] }}</h6>
                    </div>
                  </div>
                </li>
                
                <li class="d-flex mb-4 pb-1">
                  <div class="avatar flex-shrink-0 me-3">
                    <span class="avatar-initial rounded bg-label-danger"
                      ><i class="bx bx-file"></i
                    ></span>
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-1 fw-normal">{{ __('label.rejected_articles') }}</h6>
                      <!-- <small class="text-danger fw-normal d-block">
                        <i class="bx bx-chevron-down"></i>
                        2.15
                      </small> -->
                    </div>
                    <div class="user-progress">
                      <h6 class="mb-0 text-danger">{{ $status['rejected'] }}</h6>
                    </div>
                  </div>
                </li>
                <li class="d-flex">
                  <div class="avatar flex-shrink-0 me-3">
                    <span class="avatar-initial rounded bg-label-success"><i class="bx bx-file"></i></span>
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <h6 class="mb-1 fw-normal">{{ __('label.published_articles') }}</h6>
                      <!-- <small class="text-success fw-normal d-block">
                        <i class="bx bx-chevron-up"></i>
                        5.7%
                      </small> -->
                    </div>
                    <div class="user-progress">
                      <h6 class="mb-0 text-success">{{ $status['published'] }}</h6>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <!--/ Delivery Performance -->
        <!-- Reasons for delivery exceptions -->
        <div class="col-md-6 col-xxl-6 mb-4 order-1 order-xxl-3">
          <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
              <div class="card-title mb-0">
                <h5 class="m-0 me-2">{{ __('label.articles_graph') }}</h5>
              </div>

              <div class="dropdown">
                <button
                  class="btn p-0"
                  type="button"
                  id="deliveryExceptions"
                  data-bs-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="deliveryExceptions">
                  <!-- <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                  <a class="dropdown-item" href="javascript:void(0);">Refresh</a> -->
                  <!-- <a class="dropdown-item" href="javascript:void(0);">Share</a> -->
                </div>
              </div>
            </div>
            <div class="card-body">
              <div id="deliveryExceptionsChartCustom"></div>
            </div>
          </div>
        </div>
        <!--/ Reasons for delivery exceptions -->
        </div>
    </div>
</div>
@php
$series_values = [
    'submitted'         => $status['submitted'],
    'screening'         => $status['screening'],
    'under_review'      => $status['under_review'],
    'revision_required' => $status['revision_required'],
    'accepted'          => $status['accepted'],
    'rejected'          => $status['rejected'],
    'published'         => $status['published'],
];


$max_key = array_keys($series_values, max($series_values))[0];

$max_status_label = __('label.' . $max_key);
$max_status_value = $series_values[$max_key];

$series_keys = ['submitted', 'screening', 'under_review', 'revision_required', 'accepted', 'rejected', 'published'];
$series_labels = array_map(fn($key) => __('label.' . $key), $series_keys);
@endphp

@script
<script>
  (function () {

    let labelColor, headingColor;

    if (isDarkStyle) {
      labelColor = config.colors_dark.textMuted;
      headingColor = config.colors_dark.headingColor;
    } else {
      labelColor = config.colors.textMuted;
      headingColor = config.colors.headingColor;
    }

      // Chart Colors
  const chartColors = {
    donut: {
      series1:  '#5a8dee',
      series2: '#69809a',
      series3: '#00cfdd',
      series4: '#fdac41',
      series5: '#c4f4e3',
      series6: '#ff5b5c',
      series7: '#39da8a',
    },
    line: {
      series1: config.colors.warning,
      series2: config.colors.primary,
      series3: '#f4f3f529'
    }
  };
   const deliveryExceptionsChartE1 = document.querySelector('#deliveryExceptionsChartCustom'),
    deliveryExceptionsChartConfig = {
      chart: {
        height: 420,
        parentHeightOffset: 0,
        type: 'donut'
      },
      labels: @json($series_labels),
      series: [{{ $status['submitted'] }}, {{ $status['screening'] }}, {{ $status['under_review'] }},
       {{ $status['revision_required'] }},{{ $status['accepted'] }},{{ $status['rejected'] }},{{ $status['published'] }}],
      colors: [
        chartColors.donut.series1,
        chartColors.donut.series2,
        chartColors.donut.series3,
        chartColors.donut.series4,
        chartColors.donut.series5,
        chartColors.donut.series6,
        chartColors.donut.series7,
      ],
      stroke: {
        width: 0
      },
      dataLabels: {
        enabled: false,
        formatter: function (val, opt) {
          return parseInt(val);
        }
      },
      legend: {
        show: true,
        position: 'bottom',
        offsetY: 10,
        markers: {
          width: 8,
          height: 8,
          offsetX: -3
        },
        itemMargin: {
          horizontal: 15,
          vertical: 5
        },
        fontSize: '13px',
        fontFamily: '"Vazirmatn" !important',
        fontWeight: 400,
        labels: {
          colors: headingColor,
          useSeriesColors: false
        }
      },
      tooltip: {
        theme: false
      },
      grid: {
        padding: {
          top: 15
        }
      },
      plotOptions: {
        pie: {
          donut: {
            size: '75%',
            labels: {
              show: true,
              value: {
                fontSize: '26px',
                fontFamily: '"Vazirmatn" !important',
                color: headingColor,
                fontWeight: 500,
                offsetY: -30,
                formatter: function (val) {
                  return parseInt(val);
                }
              },
              name: {
                offsetY: 20,
                fontFamily: '"Vazirmatn" !important',
              },
              total: {
                show: true,
                fontSize: '0.9rem',
                label: '{{ $max_status_label }}',
                color: labelColor,
                formatter: function (w) {
                  return {{ $max_status_value }};
                }
              }
            }
          }
        }
      },
      responsive: [
        {
          breakpoint: 420,
          options: {
            chart: {
              height: 360
            }
          }
        }
      ]
    };
  if (typeof deliveryExceptionsChartE1 !== undefined && deliveryExceptionsChartE1 !== null) {
    const deliveryExceptionsChart = new ApexCharts(deliveryExceptionsChartE1, deliveryExceptionsChartConfig);
    deliveryExceptionsChart.render();
  }

 })();
</script>
@endscript