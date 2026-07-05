@if(auth()->user()->role === \App\Data\Value\Account\Role::DOCTOR->value)
    @include('pages.profiles._doctor')
@elseif(auth()->user()->role === \App\Data\Value\Account\Role::MEMBER->value)
    @include('pages.profiles._member')
@endif