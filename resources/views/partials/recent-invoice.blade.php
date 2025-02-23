 @foreach ($invoices->toArray(request()) as $invoice)
                      <tr>
                        <td>{{ $invoice['name'] }}</td>
                        <td>{{ $invoice['data'] ?? 'لا يوجد منتج' }}</td>
                        <td>{{ $invoice['price'] }}</td>
                        <td>{{ $invoice['payment'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($invoice['date_of_receipt'])->format('Y-m-d')}}</td>
                        <td>
                            {{ $invoice['return_date'] ? \Carbon\Carbon::parse($invoice['return_date'])->format('Y-m-d') : "لا يوجد" }}
                        </td>
                        @if($invoice['status'] == 'pending')
                            <td><span class="badge badge-warning">ايجار</span></td>
                        @else
                            <td><span class="badge badge-danger">بيع</span></td>
                        @endif
                        {{-- <td class="text-center">
                            <div class="btn-group">
                                <form action="{{ route('invoice.destroy', $invoice->id) }}" method="post">
                                    @csrf
                                    @method("DELETE")
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-toggle="modal" data-target="#verticalModal1" data-invoice-id="{{ $invoice->id }}" disabled>
                                        <i class="fe fe-16 fe-trash-2"></i>&nbsp;حذف
                                    </button>
                                </form>
                            </div>
                        </td> --}}
                    </tr>
@endforeach