<main role="main" class="main-content">
    <div class="modal fade" id="varyModal" tabindex="-1" role="dialog" aria-labelledby="varyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyModalLabel">اضافة قسم جديد</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('section.store') }}" method="POST">
                @csrf
                    <div class="row">
                        <!-- First Form Field -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group mb-3">
                                <label for="recipient-name1" class="col-form-label">اسم القسم :</label>
                                <input type="text" required name="name" class="form-control" id="recipient-name1">
                            </div>
                        </div>

                        <!-- Second Form Field -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group mb-3">
                                <label for="recipient-name2" class="col-form-label">العنوان:</label>
                                <input type="text" name="title" class="form-control" id="recipient-name2">
                            </div>
                        </div>
                    </div>
                <div id="status" class="form-group mb-3">
                    <label for="custom-select-1">اختر القسم الرئيسى :</label>
                    <select class="custom-select" id="custom-select-1" name="status">
                      <option selected>-- الحالة --</option>
                      <option value="1">ايجار</option>
                      <option value="0">بيع</option>
                    </select>
                  </div>
                <div id='section' class="form-group mb-3">
                    <label for="custom-select">اختر القسم الرئيسى :</label>
                    <select class="custom-select" id="custom-select" name="section_id">
                      <option value="" selected>-- اختر قسم --</option>
                        @foreach($parent_sections as $section)
                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                        @endforeach
                    </select>
                  </div>
                  <div class="modal-footer">
                      <button id="restore" type="button" class="btn mb-2 btn-info">رجوع</button>
                    <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">غلق</button>
                    <button type="submit" onclick="this.form.submit(); this.disabled = true;" class="btn mb-2 btn-primary">حفظ</button>
                    </div>
                </form>
                </div>
            </div>
         </div>
        </div>
    @yield('content')
    <footer class="footer">
        <p class="footer-text">
            © 2024 جميع الحقوق محفوظة | المعصراوي
        </p>
    </footer>
 <!-- Modal for Notifications -->
 <div class="modal fade modal-notif" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">الإشعارات</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    @php
                    $notifications = auth()->user()->unreadNotifications;
                    @endphp

                    @forelse($notifications as $notification)
                        @php
                        $data = $notification->data;
                        $invoices = $data['invoices'] ?? [];
                        @endphp

                        @forelse ($invoices as $invoice)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-primary">
                                    <a href="{{ route('invoice.show' , $invoice['invoice_id']) }}"
                                       style="text-decoration: none; color: inherit;">
                                        {{ $invoice['client_name'] }}
                                    </a>
                                </strong>
                                <p class="mb-1" style="font-weight: normal;">
                                    <span></span>
                                </p>
                                <small class="text-muted">تاريخ الاستلام: <span class="text-success">{{ $invoice['date_of_receipt'] }}</span></small>
                            </div>
                            <div>
                                <span class="badge badge-pill badge-info">{{ $notification->created_at->diffInDays() }} يوم(s) مضى</span>
                            </div>
                        </div>
                        @empty
                            <div class="list-group-item text-center">
                                <em>لا توجد فواتير متاحة.</em>
                            </div>
                        @endforelse
                    @empty
                        <div class="list-group-item text-center">
                            <em>لا توجد إشعارات غير مقروءة.</em>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('notifications.update') }}" class="btn btn-success">تحديث</a>
                <button type="button" class="btn btn-danger" id="removeAllBtn">
                    إزالة الكل
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>



      <div class="modal fade modal-shortcut modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="defaultModalLabel">Shortcuts</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body px-5">
              <div class="row align-items-center">
                <div class="col-6 text-center">
                  <div class="squircle bg-success justify-content-center">
                    <i class="fe fe-cpu fe-32 align-self-center text-white"></i>
                  </div>
                  <p>Control area</p>
                </div>
                <div class="col-6 text-center">
                  <div class="squircle bg-primary justify-content-center">
                    <i class="fe fe-activity fe-32 align-self-center text-white"></i>
                  </div>
                  <p>Activity</p>
                </div>
              </div>
              <div class="row align-items-center">
                <div class="col-6 text-center">
                  <div class="squircle bg-primary justify-content-center">
                    <i class="fe fe-droplet fe-32 align-self-center text-white"></i>
                  </div>
                  <p>Droplet</p>
                </div>
                <div class="col-6 text-center">
                  <div class="squircle bg-primary justify-content-center">
                    <i class="fe fe-upload-cloud fe-32 align-self-center text-white"></i>
                  </div>
                  <p>Upload</p>
                </div>
              </div>
              <div class="row align-items-center">
                <div class="col-6 text-center">
                  <div class="squircle bg-primary justify-content-center">
                    <i class="fe fe-users fe-32 align-self-center text-white"></i>
                  </div>
                  <p>Users</p>
                </div>
                <div class="col-6 text-center">
                  <div class="squircle bg-primary justify-content-center">
                    <i class="fe fe-settings fe-32 align-self-center text-white"></i>
                  </div>
                  <p>Settings</p>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
</main> <!-- main -->
 <!-- Footer -->


 <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle "Remove All" button click
        $('#removeAllBtn').on('click', function() {
                $.ajax({
                    url: '{{ route("notifications.clearAll") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Handle successful response
                        $('.modal-body').html('<div class="list-group-item text-center"><em>تمت إزالة جميع الإشعارات.</em></div>');
                        $('#removeAllBtn').hide(); // Hide the button after successful removal
                    },
                    error: function(xhr) {
                        // Handle error response
                        alert('حدث خطأ أثناء إزالة الإشعارات.');
                    }
                });
        });

        // Handle notification item click
        $('.modal-body').on('click', '.notification-item', function() {
            var notificationId = $(this).data('id');
            $.ajax({
                url: '{{ route("notifications.markAsRead") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: notificationId
                },
                success: function(response) {
                    // Handle successful response
                    $(this).remove(); // Optionally remove the notification from the UI
                }.bind(this),
                error: function(xhr) {
                    // Handle error response
                    alert('حدث خطأ أثناء تحديث حالة الإشعار.');
                }
            });
        });
    });
</script>

<script>
    const select = document.getElementById('custom-select');
    const select_1 = document.getElementById('custom-select-1');
    const div = document.getElementById('status');
    const section = document.getElementById('section');



    select.addEventListener('change', function() {
        if(div){
            div.innerHTML = '';
        }
        // console.log('Selected value:', this.value); // Log the selected value
        // console.log('Selected text:', this.options[this.selectedIndex].text); // Log the selected text
    });
    // select_1.addEventListener('change', function() {
    //     // console.log(select_1);
    //     // console.log(section);

    //     if(section){
    //         section.innerHTML = '';
    //     }
    // });

    const restoreBottom = document.getElementById('restore');
    restoreBottom.addEventListener('click' , function()
    {
        div.innerHTML = ` <label for="custom-select-1">اختر القسم الرئيسى :</label>
                    <select class="custom-select" id="custom-select-1" name="status">
                      <option selected>-- الحالة --</option>
                      <option value="1">ايجار</option>
                      <option value="0">بيع</option>
                    </select>`;
        select.selectedIndex = 0;

        // select.innerHTML = '';

            // Add the default option
            // const defaultOption = document.createElement('option');
            // defaultOption.value = '';
            // defaultOption.textContent = '-- اختر قسم --';
            // defaultOption.selected = true;
            // select.appendChild(defaultOption);

            // select.forEach(section => {
            //     const option = document.createElement('option');
            //     option.value = section.id; // 
            //     option.textContent = section.name;
            //     select.appendChild(option);
            // });
    });
</script>


