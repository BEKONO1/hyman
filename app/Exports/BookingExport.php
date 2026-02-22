<?php
namespace App\Exports;

use App\Models\Booking;
use App\Models\BookingStatus;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class BookingExport implements FromQuery, WithHeadings, WithMapping
{
    protected $columns;
    protected $query;

    public function __construct($columns = [], $query = null)
    {
        $this->columns = $columns;
        $this->query = $query ?? Booking::query();
    }

    public function query()
    {
        return $this->query->with(['service', 'customer', 'provider', 'payment', 'handymanAdded']);
    }

    public function map($booking): array
    {
        $data = [];

        $columnMap = [
            'colID' => fn() => $booking->id,
            'colPatientName' => fn() => optional($booking->service)->name ?? '-',
            'colStartDateTime' => fn() => $booking->date ? Carbon::parse($booking->date)->format('Y-m-d H:i:s') : '-',
            'colServices' => fn() => optional($booking->customer)->display_name . ' (' . optional($booking->customer)->email . ')',
            'colPrice' => fn() => optional($booking->provider)->display_name . ' (' . optional($booking->provider)->email . ')',
            'colStatus' => fn() => strip_tags(bookingstatus(BookingStatus::bookingStatus($booking->status))),
            'colDoctor' => fn() => $booking->total_amount ? getPriceFormat($booking->total_amount) : '-',
            // 'colPaymentStatus' => fn() => $booking->payment->payment_status ?? __('messages.pending'),
            'colPaymentStatus' => fn() => ucfirst($booking->payment->payment_status ?? __('messages.pending')),

        ];

        foreach ($this->columns as $column) {
            if (isset($columnMap[$column])) {
                $data[] = $columnMap[$column]();
            }
        }

        return $data;
    }

    public function headings(): array
    {
        $headingsMap = [
            'colID' => 'ID',
            'colPatientName' => 'Service',
            'colStartDateTime' => 'Booking Date',
            'colServices' => 'User',
            'colPrice' => 'Provider',
            'colStatus' => 'Status',
            'colDoctor' => 'Total Amount',
            'colPaymentStatus' => 'Payment Status',
        ];

        return array_filter(
            array_map(fn($column) => $headingsMap[$column] ?? null, $this->columns)
        );
    }
}
