@extends('layouts.main')
@section('content')
<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="row mb-4 text-center">
        <div class="col-12">
            <h2 class="font-weight-bold">Find Your Doctor</h2>
            <p class="text-muted">Choose from our top specialists to get the best care.</p>
        </div>
        <div class="col-12 mt-3">
            <ul class="nav nav-pills justify-content-center" id="specialty-pills" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#" role="tab">All Specialties</a>
                </li>
                @foreach($specialties as $speciality)
                <li class="nav-item">
                    <a class="nav-link" href="#" role="tab">{{$speciality->name}}</a>
                </li>                
                @endforeach
            </ul>
        </div>
    </div>
    <div class="row">
        @forelse($doctors as $doctor)

        @php
        $fullName = trim($doctor->prefix_name . ' ' . $doctor->first_name . ' ' . $doctor->middle_name . ' ' . $doctor->last_name);
        $fullNameWithTitle = $fullName . ($doctor->suffix_name ? ', ' . $doctor->suffix_name : '');
        @endphp

        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card h-100 shadow-sm doctor-card border">
                {{-- Dummy Profile Picture --}}
                <img class="card-img-top" src="https://ui-avatars.com/api/?name={{ urlencode($fullName) }}&background=random&size=250" alt="Doctor Picture" style="height: 200px; object-fit: cover;">

                <div class="card-body text-center">
                    <h5 class="card-title font-weight-bold mb-1">{{ $fullNameWithTitle }}</h5>
                    <p class="text-muted mb-2">
                        <small>
                            {{ $doctor->doctorSpecialty->first()?->specialties?->name ?? 'General Practitioner' }}
                        </small>
                    </p>

                    <div class="mb-3">
                        <span class="text-warning">⭐ {{ number_format($doctor->rating_avg, 1) }}</span>
                        <span class="text-muted" style="font-size: 0.85rem;">({{ $doctor->rating_count }} reviews)</span>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-block rounded-pill" data-toggle="modal" data-target="#doctorModal-{{ $doctor->username }}">
                        View Profile
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal detail doctor -->
        <div class="modal fade" id="doctorModal-{{ $doctor->username }}" tabindex="-1" role="dialog" aria-labelledby="doctorModalLabel-{{ $doctor->username }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center pt-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($fullName) }}&background=random&size=150" class="rounded-circle mb-3 shadow" alt="Doctor Picture">
                        <h4 class="font-weight-bold">{{ $fullNameWithTitle }}</h4>
                        <p class="text-primary mb-2">Username: {{ $doctor->username }}</p>

                        <div class="d-flex justify-content-center mb-3">
                            <div class="px-3 border-right">
                                <h5 class="mb-0 text-warning">⭐ {{ number_format($doctor->rating_avg, 1) }}</h5>
                                <small class="text-muted">Rating</small>
                            </div>
                            <div class="px-3">
                                <h5 class="mb-0">{{ $doctor->rating_count }}</h5>
                                <small class="text-muted">Patients</small>
                            </div>
                        </div>

                        <hr>

                        <div class="text-left px-3">
                            <p class="mb-1"><strong><i class="fas fa-birthday-cake"></i> Date of Birth:</strong> <br>
                                {{ \Carbon\Carbon::parse($doctor->date_of_birth)->format('d F Y') }}
                            </p>
                            <p class="mb-1 mt-3"><strong><i class="fas fa-map-marker-alt"></i> Address:</strong> <br>
                                {{ $doctor->address }}
                            </p>
                            <p class="mb-1 mt-3"><strong><i class="fas fa-map"></i> District ID:</strong> <br>
                                {{ $doctor->districts->name }}
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 justify-content-center">
                        <a href="/consultations/create?doctor={{ $doctor->username }}" class="btn btn-primary px-4 rounded-pill">Book Consultation</a>
                    </div>
                </div>
            </div>
        </div>
        {{-- End Modal --}}

        @empty
        <div class="col-12 text-center mt-5">
            <h5 class="text-muted">No doctors currently available.</h5>
        </div>
        @endforelse
    </div>
</div>
<style>
    .doctor-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }

    .doctor-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .nav-pills .nav-link {
        color: #495057;
        border-radius: 50px;
        margin: 0 5px;
        padding: 8px 20px;
        transition: all 0.3s;
    }

    .nav-pills .nav-link.active,
    .nav-pills .show>.nav-link {
        background-color: #007bff;
        color: white;
        box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
    }

    .nav-pills .nav-link:hover:not(.active) {
        background-color: #f8f9fa;
    }
</style>
@endsection