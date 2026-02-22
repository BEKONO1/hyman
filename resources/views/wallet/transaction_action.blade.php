@if($exists || $query->status == 'paid')
<span class="text-center d-block"> - </span>
@else
<a href="#" onclick="confirmAction({{ $query->id }})" class="btn btn-md btn-link float-right"><u>{{__('messages.paid_elsewhere')}}</u></a>

@endif

<script>
function confirmAction(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "Are you sure you want to pay the provider?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            var url = '{{ route("wallet.wallet_transaction_payout", ":id") }}'.replace(':id', id);
            window.location.href = url;
        }
    })
}
</script>