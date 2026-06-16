@extends('dashboard.layouts.master')
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
@endsection

@section('title')
{{ $title }}
@endsection
@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">{{ucfirst(get_user_data()?->name) . ' ' . trans('dashboard/header.main_dashboard') }}</h1>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body bg-primary rounded-3">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-12">
                                    <div class="d-lg-flex justify-content-between align-items-center ">
                                        <div class="d-md-flex align-items-center">
                                            <img src="{{ asset('dashboard/assets/images/user/avatar-2.jpg') }}" alt="Image"
                                                class="rounded-circle avatar avatar-xl">
                                            <div class="mt-3 ms-md-4">
                                                <h2 class="mb-1 text-white fw-600">
                                                    {{ucfirst(get_user_data()?->name)}}
                                                </h2>
                                                <p class="text-white">
                                                    {{ get_user_data()?->phone }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="border rounded card-body border-success bg-light-success">
                            <div class="d-flex align-items-center">
                                <div class="numbers flex-grow-1 pe-3">
                                    <p class="mb-1 fw-600 text-muted">Today's Money</p>
                                    <h4 class="mb-0 fw-700 text-dark-black">$53,000 <span
                                            class="text-sm text-success fw-700">+55%</span></h4>
                                </div>
                                <div class="icon-shape bg-success ">
                                    <i class="ti ti-report-money"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="border rounded card-body border-success bg-light-success">
                            <div class="d-flex align-items-center">
                                <div class="numbers flex-grow-1 pe-3">
                                    <p class="mb-1 fw-600 text-muted">Today's Users</p>
                                    <h4 class="mb-0 fw-700 text-dark-black">2,300 <span
                                            class="text-sm text-success fw-700">+3%</span></h4>
                                </div>
                                <div class="icon-shape bg-success ">
                                    <i class="ti ti-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="border rounded card-body border-danger bg-light-danger">
                            <div class="d-flex align-items-center">
                                <div class="numbers flex-grow-1 pe-3">
                                    <p class="mb-1 fw-600 text-muted">New Clients</p>
                                    <h4 class="mb-0 fw-700 text-dark-black">+3,462 <span
                                            class="text-sm text-danger fw-700">-2%</span></h4>
                                </div>
                                <div class="icon-shape bg-danger ">
                                    <i class="ti ti-click"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="border rounded card-body border-danger bg-light-danger">
                            <div class="d-flex align-items-center">
                                <div class="numbers flex-grow-1">
                                    <p class="mb-1 fw-600 text-muted">Sales</p>
                                    <h4 class="mb-0 fw-700 text-dark-black">$103,430 <span
                                            class="text-sm text-danger fw-700">+5%</span></h4>
                                </div>
                                <div class="icon-shape bg-danger ">
                                    <i class="ti ti-shopping-cart"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xxl-4 col-lg-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Daily Sales</h4>
                        </div>
                        <div class="card-body">
                            <div id="Sales-chart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-8 col-lg-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Statistics</h4>
                        </div>
                        <div class="card-body">
                            <div id="traffic-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header">
                            <h4>Latest Projects</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Project Name</th>
                                            <th>Start Date</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Assign</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Admin v1</td>
                                            <td>01/01/2017</td>
                                            <td>26/04/2017</td>
                                            <td><span class="badge bg-primary">Released</span></td>
                                            <td>Coderthemes</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Frontend v1</td>
                                            <td>01/01/2017</td>
                                            <td>26/04/2017</td>
                                            <td><span class="badge bg-success">Released</span></td>
                                            <td>admin</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Admin v1.1</td>
                                            <td>01/05/2017</td>
                                            <td>10/05/2017</td>
                                            <td><span class="badge bg-danger">Pending</span></td>
                                            <td>Coderthemes</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Frontend v1.1</td>
                                            <td>01/01/2017</td>
                                            <td>31/05/2017</td>
                                            <td><span class="badge bg-info">Work in Progress</span>
                                            </td>
                                            <td>admin</td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td>Admin v1.3</td>
                                            <td>01/01/2017</td>
                                            <td>31/05/2017</td>
                                            <td><span class="badge bg-warning">Coming soon</span></td>
                                            <td>Coderthemes</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection
