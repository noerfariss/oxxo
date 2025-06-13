<button title="Filter" type="button" class="btn btn-sm mt-1 rounded btn-outline-dark" data-bs-toggle="modal"
    data-bs-target="#exampleModal"><i class='bx bx-filter'></i></button>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Filter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @isset($office)
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            <select name="office_filter" id="office_filter" class="form-control office-select"
                                data-ajax--url="{{ route('drop-office') }}"></select>
                        </div>
                    </div>
                @endisset

                @isset($division)
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            <select name="division_filter" id="division_filter" class="form-control division-select"
                                data-ajax--url="{{ route('drop-division') }}"></select>
                        </div>
                    </div>
                @endisset

                @isset($position)
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            <select name="position_filter" id="position_filter" class="form-control position-select"
                                data-ajax--url="{{ route('drop-position') }}"></select>
                        </div>
                    </div>
                @endisset

            </div>
            <div class="modal-footer">
                <div class="d-grid">
                    <button type="button" class="btn btn-primary btn-sm" onclick="datatables.ajax.reload()">Filter</button>
                </div>
            </div>
        </div>
    </div>
</div>
