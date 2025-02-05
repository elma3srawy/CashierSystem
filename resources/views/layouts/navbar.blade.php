<nav class="topnav navbar navbar-light">
    <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
      <i class="fe fe-menu navbar-toggler-icon"></i>
    </button>
    {{-- <form class="form-inline mr-auto searchform text-muted">
      <input class="form-control mr-sm-2 bg-transparent border-0 pl-4 text-muted" type="search" placeholder="Type something..." aria-label="Search">
    </form> --}}
    <ul class="nav">
      <li class="nav-item">
        <a class="nav-link text-muted my-2" href="#" id="modeSwitcher" data-mode="light">
          <i class="fe fe-sun fe-16"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-muted my-2" href="./#" data-toggle="modal" data-target=".modal-shortcut">
          <span class="fe fe-grid fe-16"></span>
        </a>
      </li>
      <!-- Navigation Link with Notification Icon -->
    <li class="nav-item nav-notif">
        <a class="nav-link text-muted my-2" href="#" data-toggle="modal" data-target=".modal-notif">
            <span class="fe fe-bell fe-16"></span>
            @php
                $unreadNotificationsCount = auth()->user()->unreadNotifications->count();
            @endphp

            @if ($unreadNotificationsCount > 0)
                <span class="dot dot-md bg-success"></span>
            @endif

        </a>
    </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-muted pr-0" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="avatar avatar-sm mt-2">
            <img src="/assets/avatars/hegran.jpg" alt="..." class="avatar-img rounded-circle">
          </span>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
           @cannot('access-superAdmin')
            <a data-toggle="modal" data-target="#changeRoleModal" class="dropdown-item" href="#"><span class="ml-1 item-text">تغير الصلاحيات</span>
            </a>
           @endcannot
           @can('access-superAdmin')
           <form id="changeRole-form" action="{{ route('admin.deleteRole') }}" method="POST" style="display: none;">
                @method('DELETE')
                @csrf
            </form>
            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('changeRole-form').submit();">
                تغيير الصلاحيات
            </a>
           @endcan

            <a class="dropdown-item" href="#">الملف الشخصي</a>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            تسجيل الخروج
        </a>
        </div>
      </li>
    </ul>
  </nav>



  <!-- Modal for Changing Password -->
<div class="modal fade" id="changeRoleModal" tabindex="-1" role="dialog" aria-labelledby="changeRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeRoleModalLabel">تغير الصلاحيات</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.changeRole') }}" method="POST">
                @csrf
                <div class="modal-body-1" style="padding: 10px">
                    <div class="form-group">
                        <label for="password">كلمة السر:</label>
                        <input type="password" name="secret" class="form-control" id="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">غلق</button>
                    <button type="submit" class="btn btn-primary">تحديث الصلاحيات</button>
                </div>
            </form>
        </div>
    </div>
</div>

