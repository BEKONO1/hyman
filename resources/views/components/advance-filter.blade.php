<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel"
    data-bs-backdrop="false" data-bs-keyboard="false">
    <div class="offcanvas-header border-bottom">
        @if (isset($title))
            {{ $title }}
        @endif
        <div>
            <button type="reset" id="refresh-filter" class="btn-icon btn-refresh me-2" title="Refresh">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <span aria-hidden="true"></span>
            </button>
        </div>
    </div>
    <div class="offcanvas-body">
        {{ $slot }}
    </div>
    
    <div class="offcanvas-footer p-3">
        <div class="text-end">
            <button type="reset" class="btn btn-danger" data-bs-dismiss="offcanvas"
            id="reset-filter">{{ __('messages.reset_all_button') }}</button>
        </div>        
    </div>
</div>

<script>
   document.addEventListener('DOMContentLoaded', function () {
    // Function to reset all filters
    function resetFilters() {
        // Reset filters object
        selectedFilters = {
            booking_status: [],
            payment_status: [],
            payment_type: [],
            advance_paid: [],
            date_range: ''
        };

        // Reset all filter buttons
        document.querySelectorAll('.filter-button').forEach(button => {
            button.classList.remove('active');
            button.classList.add('inactive');
        });

        // Reset dropdowns (using Select2)
        document.querySelectorAll('.select2').forEach(select => {
            $(select).val(null).trigger('change'); // Reset Select2 elements
        });

        // Reset date range picker
        document.getElementById('datepicker1').value = '';

        // Reload the DataTable
        if ($.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().ajax.reload();
        }
    }

    // Reset All Button
    document.getElementById('reset-filter').addEventListener('click', function () {
        resetFilters();
    });

    // Refresh Button
    document.getElementById('refresh-filter').addEventListener('click', function () {
        // Get the offcanvas element
        const offcanvas = document.querySelector('#offcanvasExample');

        // Refresh all inputs within the offcanvas
        const datePicker = document.querySelector('#datepicker1');
        if (datePicker) {
            datePicker._flatpickr.clear(); // Clear the Flatpickr instance
            selectedFilters.date_range = ''; // Reset the selected filter's date range
        }

        // Refresh Select2 elements
        document.querySelectorAll('.select2').forEach(select => {
            $(select).val(null).trigger('change'); // Reset Select2 elements
        });


        // Optional: Refresh DataTable
        if ($.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().ajax.reload();
        }
    });

    // Reinitialize Select2 when the offcanvas is shown
    const offcanvasElem = document.querySelector('#offcanvasExample');
    offcanvasElem.addEventListener('shown.bs.offcanvas', function () {
        $('.datatable-filter .select2').select2({
            dropdownParent: $('#offcanvasExample')
        });
    });
});

</script>
