<div>
    <div class="content-wrapper">
            <!-- Content -->
      <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Hour chart  -->
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
                    <span class="avatar-initial rounded bg-label-info"><i class="bx bx-time-five"></i></span>
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
        <!-- Hour chart End  -->

        <!-- Topic and Instructors -->
        <div class="row mb-4 g-4">
          <div class="col-12 col-xl-12">
            <div class="card h-100">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">{{ __('label.my_assigned_papers_status') }}</h5>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="topic"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="topic">
                    <!-- <a class="dropdown-item" href="javascript:void(0);">Highest Views</a>
                    <a class="dropdown-item" href="javascript:void(0);">See All</a> -->
                  </div>
                </div>
              </div>
              <div class="card-body row g-3">
                <div class="col-md-6">
                  <div id="horizontalBarChartCustom"></div>
                </div>
                <div class="col-md-6 d-flex justify-content-around align-items-center">
                  <div>
                    <div class="d-flex align-items-baseline">
                      <span class="text-warning me-2"><i class="bx bxs-circle"></i></span>
                      <div>
                        <p class="mb-2">{{__('label.pending')}}</p>
                        <h5>{{ $review['pending'] }}</h5>
                      </div>
                    </div>
                    <div class="d-flex align-items-baseline">
                      <span class="text-info me-2"><i class="bx bxs-circle"></i></span>
                      <div>
                        <p class="mb-2">{{__('label.completed')}}</p>
                        <h5>{{ $review['completed'] }}</h5>
                      </div>
                    </div>
                  </div>

                  <div>
                    
                    <div class="d-flex align-items-baseline my-3">
                      <span class="text-success me-2"><i class="bx bxs-circle"></i></span>
                      <div>
                        <p class="mb-2">{{__('label.accepted')}}</p>
                        <h5>{{ $review['accepted'] }}</h5>
                      </div>
                    </div>
                    <div class="d-flex align-items-baseline">
                      <span class="text-danger me-2"><i class="bx bxs-circle"></i></span>
                      <div>
                        <p class="mb-2">{{__('label.declined')}}</p>
                        <h5>{{ $review['declined'] }}</h5>
                      </div>
                    </div>
                    
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Topic and Instructors end -->
      </div>
    </div>
</div>

@php
    $seriesData = [
        'completed' => $review['completed'],
        'declined'  => $review['declined'],
        'accepted'  => $review['accepted'],
        'pending'   => $review['pending'],
    ];

    $max_value = max($seriesData);
    $max_key   = array_search($max_value, $seriesData);
@endphp
@script
<script>
  (function () {

  let labelColor, headingColor, borderColor;

  if (isDarkStyle) {
    labelColor = config.colors_dark.textMuted;
    headingColor = config.colors_dark.headingColor;
    borderColor = config.colors_dark.borderColor;
  } else {
    labelColor = config.colors.textMuted;
    headingColor = config.colors.headingColor;
    borderColor = config.colors.borderColor;
  }



const horizontalBarChartEl = document.querySelector('#horizontalBarChartCustom'),
    horizontalBarChartConfig = {
      chart: {
        height: 270,
        type: 'bar',
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          horizontal: true,
          barHeight: '70%',
          distributed: true,
          startingShape: 'rounded',
          borderRadius: 7
        }
      },
      grid: {
        strokeDashArray: 10,
        borderColor: borderColor,
        xaxis: {
          lines: {
            show: true
          }
        },
        yaxis: {
          lines: {
            show: false
          }
        },
        padding: {
          top: -35,
          bottom: -12
        }
      },

      colors: [
        config.colors.info,
        config.colors.danger,
        config.colors.success,
        config.colors.warning,
      ],
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#fff'],
          fontWeight: 200,
          fontSize: '13px',
          fontFamily: '"Vazirmatn" !important'
        },
        formatter: function (val, opts) {
          return horizontalBarChartConfig.labels[opts.dataPointIndex];
        },
        offsetX: 0,
        dropShadow: {
          enabled: false
        }
      },
      labels: ['{{__('label.completed')}} ({{ $review['completed'] }})', '{{__('label.declined')}} ({{ $review['declined'] }})', '{{__('label.accepted')}} ({{ $review['accepted'] }})','{{__('label.pending')}} ({{ $review['pending'] }})'],
      series: [
        {
          data: [{{ $review['completed'] }}, {{ $review['declined'] }}, {{ $review['accepted'] }}, {{ $review['pending'] }}]
        }
      ],

      xaxis: {
        categories: ['4', '3', '2', '1'],
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          style: {
            colors: labelColor,
            fontSize: '13px',
            fontFamily: ' "Vazirmatn" !important'
          },
          formatter: function (val) {
            return `${val}`;
          }
        }
      },
      yaxis: {
        max: {{ $max_value }},
        labels: {
          style: {
            colors: [labelColor],
            fontFamily: 'Vazirmatn !important',
            fontSize: '13px'
          }
        }
      },
      tooltip: {
        enabled: true,
        style: {
          fontSize: '12px'
        },
        onDatasetHover: {
          highlightDataSeries: false
        },
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
          return '<div class="px-3 py-2">' + '<span>' + series[seriesIndex][dataPointIndex] + '</span>' + '</div>';
        }
      },
      legend: {
        show: false
      }
    };
  if (typeof horizontalBarChartEl !== undefined && horizontalBarChartEl !== null) {
    const horizontalBarChart = new ApexCharts(horizontalBarChartEl, horizontalBarChartConfig);
    horizontalBarChart.render();
  }
})();
</script>
@endscript