@if(auth()->user()->role === \App\Data\Value\Account\Role::DOCTOR->value)
    @include('pages.admin.dashboard._doctor')
@elseif(auth()->user()->role === \App\Data\Value\Account\Role::ADMIN->value)
    @include('pages.admin.dashboard._admin')
@endif