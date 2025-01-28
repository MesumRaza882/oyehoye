<div class="col-auto d-flex align-items-start">


    @isset($backArrow)
    <div>
        {{ $backArrow }}
    </div>
    @endisset

    <p class="d-block p-1 px-2 bg-primary text-white rounded">Records:<span class="ms-1">
            {{ $totalRecords }}
        </span></p>

    @isset($pinned)
    <div>
        {{ $pinned }}
    </div>
    @endisset

</div>