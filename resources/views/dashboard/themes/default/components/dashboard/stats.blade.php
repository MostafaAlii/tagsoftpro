<div class="row">
    @foreach([
    [
    'title'=>'Today Money',
    'value'=>'$53,000',
    'icon'=>'ti-report-money',
    'color'=>'success'
    ],
    [
    'title'=>'Today Users',
    'value'=>'2,300',
    'icon'=>'ti-users',
    'color'=>'success'
    ],
    [
    'title'=>'New Clients',
    'value'=>'+3,462',
    'icon'=>'ti-click',
    'color'=>'danger'
    ],
    [
    'title'=>'Sales',
    'value'=>'$103,430',
    'icon'=>'ti-shopping-cart',
    'color'=>'danger'
    ]
    ] as $item)

    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="border rounded card-body border-{{$item['color']}} bg-light-{{$item['color']}}">
                <div class="d-flex align-items-center">
                    <div class="numbers flex-grow-1 pe-3">
                        <p class="mb-1 fw-600 text-muted">
                            {{$item['title']}}
                        </p>
                        <h4 class="mb-0 fw-700 text-dark-black">
                            {{$item['value']}}
                        </h4>
                    </div>
                    <div class="icon-shape bg-{{$item['color']}}">
                        <i class="ti {{$item['icon']}}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>