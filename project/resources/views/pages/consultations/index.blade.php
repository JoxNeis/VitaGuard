@extends('layouts.navbar.main')

@section('content')
<div class="container" style="margin-top: 64px; margin-bottom: 36px;">
    <div class="row text-center">
        <div class="col-12">
            <h2 class="font-weight-bold">Chat doctor in VitaGuard</h2>
            <p class="text-muted">Our top doctors are always ready to help you</p>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-center">
            @auth
                <a href="{{ route('consultations.member') }}" class="btn btn-outline-primary px-4 py-2">
                    <i class="bi bi-clipboard2-heart me-1"></i> Konsultasi Saya
                </a>
            @endauth
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-md-10 col-lg-8 mx-auto">
            <div id="carouselExampleIndicators" class="carousel slide shadow-sm rounded" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                </ol>

                <div class="carousel-inner rounded-lg" style="border-radius: 15px; overflow: hidden;">

                    <div class="carousel-item active">
                        <picture>
                            <source srcset="/assets/images/hero-bg.webp" type="image/webp">
                            <img class="d-block w-100" src="/assets/images/hero-bg.jpg" style="height: 350px; object-fit: cover;" alt="Slide 1">
                        </picture>
                        <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 10px; padding: 10px;">
                            <h5>Expert Consultations</h5>
                            <p>Get the best medical advice from your home.</p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <picture>
                            <source srcset="/assets/images/hero-bg.webp" type="image/webp">
                            <img class="d-block w-100" src="/assets/images/hero-bg.jpg" style="height: 350px; object-fit: cover;" alt="Slide 2">
                        </picture>
                        <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 10px; padding: 10px;">
                            <h5>24/7 Availability</h5>
                            <p>Our doctors are available around the clock.</p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <picture>
                            <source srcset="/assets/images/doctor.webp" type="image/webp">
                            <img class="d-block w-100" src="/assets/images/hero-bg.jpg" style="height: 350px; object-fit: cover;" alt="Slide 3">
                        </picture>
                        <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 10px; padding: 10px;">
                            <h5>Trusted Professionals</h5>
                            <p>Certified and experienced healthcare providers.</p>
                        </div>
                    </div>

                </div>

                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4 text-center">
        <div class="col-12">
            <h2 class="font-weight-bold">Find Your Doctor</h2>
            <p class="text-muted">Choose from our top specialists to get the best care.</p>
        </div>
        <div class="col-12 mt-3">
            <ul class="nav nav-pills justify-content-center" id="specialty-pills" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ request('specialty') ? '' : 'active' }}"
                    href="/consultations">
                        All Specialties
                    </a>
                </li>
                @foreach($specialties as $speciality)
                <li class="nav-item">
                    <a class="nav-link {{ request('specialty') == $speciality->id ? 'active' : '' }}"
                        href="?specialty={{ $speciality->id }}">
                            {{ $speciality->name }}
                    </a>
                </li>
                @endforeach
                
            </ul>
        </div>
    </div>
    @php
    $selected = request('specialty');

    $filteredDoctors = $selected
        ? $doctors->filter(function ($doctor) use ($selected) {
            return $doctor->specialties->contains(function ($ds) use ($selected) {
                return $ds->specialty_id == $selected;
            });
        })
        : $doctors;
@endphp
    @foreach($filteredDoctors as $doctor)

    @php
    $fullName = trim($doctor->prefix_name . ' ' . $doctor->first_name . ' ' . $doctor->middle_name . ' ' . $doctor->last_name);
    $fullNameWithTitle = $fullName . ($doctor->suffix_name ? ', ' . $doctor->suffix_name : '');
    @endphp
    <div class="card shadow-sm mb-4 border" style="border-radius: 4px; overflow: hidden;">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-2 text-center mb-3 mb-md-0 d-flex justify-content-center align-items-center">
                    <img src="https://ui-avatars.com/api/?&background=f0f0f0&color=333&size=120"
                        class="rounded-circle img-fluid"
                        alt="FOto Doctor"
                        style="width: 110px; height: 110px; object-fit: cover; border: 4px solid #f8f9fa;">
                </div>

                <div class="col-md-7">
                    <h4 class="font-weig    ht-bold mb-2 text-dark">{{$fullName}}</h4>

                    <div class="text-muted mb-2" style="font-size: 0.95rem;">
                        <i class="fas fa-stethoscope fa-fw text-secondary"></i>
                            @if($doctor->specialties->count() > 0)
                                @for($i = 0; $i < $doctor->specialties->count(); $i++)
                                    {{ $doctor->specialties[$i]->specialty?->name }}
                                    @if($i < $doctor->specialties->count() - 1), @endif
                                @endfor
                            @else
                                General Practitioner
                            @endif
                    </div>

                    <div class="text-muted mb-3" style="font-size: 0.95rem;">
                        <i class="far fa-building fa-fw text-secondary"></i>{{ $doctor->address }}
                    </div>
                    <div class="d-inline-block px-3 py-1 rounded mb-3" style="background-color: #f0f7ff; color: #1a56db; font-size: 0.85rem; font-weight: 500;">
                        <i class="fas fa-users"></i>{{$doctor->rating_count}} patients have made appointments with this doctor
                    </div>
                </div>

                <div class="col-md-3 custom-border-left text-md-left text-center mt-3 mt-md-0 pl-md-4">
                    <div class="mb-3">
                        <p class="text-muted mb-1" style="font-size: 0.9rem;">Doctor Review</p>
                        <div class="mb-3">
                            <span class="text-warning">⭐ {{ number_format($doctor->rating_avg, 1) }}</span>
                            <span class="text-muted" style="font-size: 0.85rem;">({{ $doctor->rating_count }} reviews)</span>
                        </div>
                    </div>
                    <div class="text-md-right">
                        @auth
                            @if($doctor->can_chat && $doctor->consultation_id)
                            <a href="{{ route('chat', $doctor->consultation_id) }}"
                                class="btn btn-block text-white font-weight-bold py-2 shadow-sm"
                                style="background-color: #ea580c; border-radius: 8px;">
                                Chat
                            </a>
                        @else
                            <button
                                class="btn btn-block btn-secondary font-weight-bold py-2"
                                disabled
                                title="Anda harus memiliki appointment dengan dokter ini terlebih dahulu">
                                Chat
                            </button>
                        @endif
                    @endauth

                        @guest
                        <button type="button"
                            class="btn btn-block text-white font-weight-bold py-2 shadow-sm"
                            style="background-color: #ea580c; border-radius: 8px;"
                            data-toggle="modal" data-target="#loginModal">
                            Chat
                        </button>
                        @endguest

                    </div>
                </div>

            </div>
            <hr class="my-3 border-light">

            <div class="row align-items-center mt-3">
                <div class="col-md-12">
                    <div class="text-muted mb-2" style="font-size: 0.95rem;">
                        <i class="far fa-calendar-alt fa-fw mr-2"></i>
                        <strong>Pilih Jadwal Konsultasi</strong>
                    </div>

            @if($doctor->schedules->count() > 0)
                        <form class="booking-form" action="{{ route('consultation.store') }}" method="POST" data-doctor-id="{{ $doctor->id }}">
                            @csrf
                            <input type="hidden" name="doctor_id" value="{{ $doctor->username }}">
                            
                            <div class="form-group">
                                <label class="font-weight-bold text-secondary mb-2">Pilih Jam & Hari Tersedia:</label>
                                <div class="list-group">
                                    @foreach($doctor->schedules as $schedule)

                    <label class="list-group-item d-flex justify-content-between align-items-center mb-2 style-schedule-item"
                        style="
                            cursor: {{ $doctor->has_booked ? 'not-allowed' : 'pointer' }};
                            border-radius:6px;
                            {{ $doctor->has_booked ? 'background:#f3f4f6;' : '' }}
                        ">

                        <div class="d-flex align-items-center">

                            <input
                                type="radio"
                                name="schedule_id"
                                value="{{ $schedule->id }}"
                                required
                                {{ $doctor->has_booked ? 'disabled' : '' }}

                            <span class="ml-3 font-weight-bold {{ $doctor->has_booked ? 'text-muted' : 'text-dark' }}">
                                {{ \Carbon\Carbon::parse($schedule->open_time)->format('H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($schedule->close_time)->format('H:i') }}
                                WIB
                            </span>

                            <span class="badge badge-light ml-3">
                                {{ $schedule->day_of_week }}
                            </span>

                        </div>

                        @if($doctor->has_booked)
                            <span class="badge badge-secondary">
                                You Have Booked This
                            </span>
                        @else
                            <span class="badge badge-info">
                                Available
                            </span>
                        @endif

                    </label>

                @endforeach
                </div>
            </div>

            <div class="form-group mt-3">
                <label class="font-weight-bold text-secondary">
                    Tanggal Konsultasi
                </label>

                <input
                    type="date"
                    name="date"
                    class="form-control"
                    min="{{ date('Y-m-d') }}"
                    required>
            </div>

            <div class="form-group mt-4">
                <label for="notes-{{ $doctor->id }}" class="font-weight-bold text-secondary">Catatan Keluhan / Keperluan:</label>
                <textarea 
                    name="notes" 
                    id="notes-{{ $doctor->id }}" 
                    class="form-control" 
                    rows="3" 
                    maxlength="255"
                ></textarea>
                <small class="form-text text-muted">Informasikan keluhan singkat Anda agar dokter dapat mempersiapkan pemeriksaan dengan lebih baik.</small>
            </div>

            <button type="submit" class="btn btn-success btn-block mt-4 font-weight-bold btn-submit-booking py-2" style="background-color: #22c55e; border: none; font-size: 1.05rem; border-radius: 6px; box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.2);">
                Konfirmasi & Pilih Jadwal
            </button>
        </form>
                    @else
                        <p class="text-muted italic">Tidak ada jadwal tersedia untuk dokter ini.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
    @endforeach
</div>


@push('modal')
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title font-weight-bold" id="loginModalLabel">Login to access chat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{route('login')}}">
                @csrf
                <div class="modal-body px-4 pt-3 pb-2">

                    <div class="form-group mb-3">
                        <label for="username" class="font-weight-bold text-muted" style="font-size: 0.9rem;">Username</label>                        
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Input your username" id="username" autofocus required>
                        @error('username')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="password" class="font-weight-bold text-muted" style="font-size: 0.9rem;">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Input your password" id="password" required>
                    </div>

                </div>                
                <div class="modal-footer border-top-0 flex-column px-4 pb-4 pt-0">
                    <button type="submit" class="btn btn-block btn-primary text-white font-weight-bold py-2 shadow-sm">
                        Log In
                    </button>
                    
                    <p class="text-center mt-3 mb-0" style="font-size: 0.95rem;">
                        Don't have any account?
                        <a href="/register" class="font-weight-bold text-decoration-none">Register</a>
                    </p>
                </div>
            </form>

        </div>
    </div>
</div>
@endpush
<style>
    @media (min-width: 768px) {
        .custom-border-left {
            border-left: 1px solid #e5e7eb;
        }
    }

    .btn[style*="background-color: #ea580c"]:hover {
        background-color: #c2410c !important;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.booking-form').on('submit', function(e) {
        e.preventDefault(); 

        let form = $(this);
        let submitBtn = form.find('.btn-submit-booking');
        let actionUrl = form.attr('action');

        // Mengambil semua data input dari form termasuk input hidden doctor_id
        let formData = form.serialize(); 

        submitBtn.prop('disabled', true).text('Processing Booking...');

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData, 
            dataType: 'json',
            headers: {
                'Accept': 'application/json' 
            },
            success: function(response) {
                if(response.success) {
                    alert('Sukses! ' + response.message + '\nNomor Antrean Anda: ' + response.queue_order);
                    window.location.reload(); 
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).text('Konfirmasi & Pilih Jadwal');

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    
                    $.each(errors, function(key, value) {
                        errorMessage += value[0] + '\n';
                    });
                    
                    alert('Gagal Validasi:\n' + errorMessage);
                } else {
                    alert('Error ' + xhr.status + ': ' + (xhr.responseJSON.message || 'Terjadi kesalahan pada server.'));
                }
            }
        });
    });
});
</script>

@endsection