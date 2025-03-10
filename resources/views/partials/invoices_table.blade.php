@foreach ($invoices as $invoice)
<tr>
  <td><a href="{{ route('clients.show' , $invoice->id) }}" style="
      text-decoration: none;
      color: #3498db; /* Link color */
      font-weight: 600; /* Bold text */
      border-radius: 5px; /* Rounded corners */
      transition: background-color 0.3s ease, color 0.3s ease; /* Smooth transition */
  ">{{ $invoice->name }}</a></td>
  <td>
      <a style="text-decoration: none;" href="{{ route('invoice.show' , $invoice->id) }}"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: red">{{ $invoice->count }}</span></a>
  </td>
  {{-- <td>{{ $invoice->orders->sum('price') }}</td> --}}
  {{-- <td>{{ $invoice->orders->sum('payment') }}</td> --}}
  @can('access-superAdmin')
  <td>{{ $invoice->price - $invoice->payment }}</td>
  @endcan
  <td>{{\Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') }}</td>
  <td>{{ $invoice->date_of_receipt ? \Carbon\Carbon::parse($invoice->date_of_receipt)->format('d-m-Y') : 'لا يوجد' }}</td>
  <td>{{ $invoice->return_date ? \Carbon\Carbon::parse($invoice->return_date)->format('d-m-Y') : 'لا يوجد' }}</td>
  @if($invoice->status == 'pending')
      <td><span class="badge badge-warning">ايجار</span></td>
  @else
      <td><span class="badge badge-danger">بيع</span></td>
  @endif
  <td class="text-center">
    {{-- Pay Button (outside dropdown) --}}
    @can('access-superAdmin')
        @if ($invoice->price - $invoice->payment != 0)
            <form action="{{ route('invoice.pay', $invoice->id) }}" method="post" class="d-inline-block">
                @csrf
                <button type="button" class="btn btn-sm btn-outline-info pay-btn"
                    data-toggle="modal" data-target="#verticalModal2"
                    data-invoice-id="{{ $invoice->id }}">
                    <i class="fe fe-dollar-sign"></i> دفع
                </button>
            </form>
        @endif

        {{-- Restore Button (outside dropdown) --}}
        @if (is_null($invoice->restored_at))
            <form action="{{ route('invoice.restore', $invoice->id) }}" method="post" class="d-inline-block">
                @csrf
                @method("PUT")
                <button type="button" class="btn btn-sm btn-outline-warning restore-btn"
                    data-toggle="modal" data-target="#verticalModal"
                    data-invoice-id="{{ $invoice->id }}">
                    <i class="fe fe-rotate-ccw"></i> استرجاع
                </button>
            </form>
        @endif
    @endcan

    {{-- Dropdown menu for edit, delete, print --}}
    <div class="dropdown d-inline-block">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
            id="invoiceMenu{{ $invoice->id }}" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fe fe-more-vertical"></i>
        </button>

        <div class="dropdown-menu dropdown-menu-right"
            aria-labelledby="invoiceMenu{{ $invoice->id }}"
            style="min-width: 200px; font-size: 14px; direction: rtl; text-align: right; padding: 10px; border-radius: 8px;">
            <a href="{{ route('invoice.print', $invoice->id) }}"
                class="dropdown-item d-flex align-items-center py-2 text-success">
                 <i class="fe fe-printer ml-2"></i> طباعة
             </a>
            @can('access-superAdmin')
                <a href="{{ route('invoice.edit', [$invoice->id, $invoice->status]) }}"
                   class="dropdown-item d-flex align-items-center py-2 text-primary">
                    <i class="fe fe-edit ml-2"></i> تعديل
                </a>

                <button type="button"
                    class="dropdown-item d-flex align-items-center py-2 text-danger delete-btn"
                    data-toggle="modal" data-target="#verticalModal1"
                    data-invoice-id="{{ $invoice->id }}">
                    <form action="{{ route('invoice.destroy', $invoice->id) }}" method="post" class="d-inline m-0 p-0">
                        @csrf
                        @method("DELETE")
                    </form>
                    <i class="fe fe-trash-2 ml-2"></i> حذف
                </button>
            @endcan

        </div>
    </div>
</td>
</tr>
@endforeach

