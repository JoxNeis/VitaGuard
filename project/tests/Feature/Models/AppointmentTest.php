<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use Tests\TestCase;

class OrmAppointmentTest extends TestCase
{
    public function test_get_all_appointments(): void
    {
        $this->assertCount(10, Appointment::all());
    }
    public function test_get_appointment(): void
    {
        $this->assertEquals(1, Appointment::find(1)->id);
    }

    public function test_get_appointment_schedule(): void
    {
        $this->assertEquals(
            1,
            Appointment::find(1)->schedule->id
        );
    }

    public function test_get_appointment_doctor(): void
    {
        $this->assertEquals(
            "doc001",
            Appointment::find(1)->doctor->username
        );
    }
}
