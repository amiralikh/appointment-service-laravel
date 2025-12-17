<?php

use App\Jobs\SendAppointmentConfirmation;
use App\Mail\AppointmentConfirmationMail;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;

describe('SendAppointmentConfirmation', function () {
    beforeEach(function () {
        Mail::fake();
    });

    it('sends appointment confirmation email', function () {
        $appointment = Appointment::factory()->create([
            'customer_email' => 'customer@example.com',
        ]);

        $job = new SendAppointmentConfirmation($appointment);
        $job->handle();

        Mail::assertSent(AppointmentConfirmationMail::class, function ($mail) use ($appointment) {
            return $mail->hasTo('customer@example.com')
                && $mail->appointment->id === $appointment->id;
        });
    });

    it('sends email to correct recipient', function () {
        $appointment = Appointment::factory()->create([
            'customer_email' => 'test@example.com',
        ]);

        $job = new SendAppointmentConfirmation($appointment);
        $job->handle();

        Mail::assertSent(AppointmentConfirmationMail::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    });

    it('has correct retry configuration', function () {
        $appointment = Appointment::factory()->create();
        $job = new SendAppointmentConfirmation($appointment);

        expect($job->tries)->toBe(3)
            ->and($job->backoff)->toBe(60);
    });

    it('is queueable', function () {
        $appointment = Appointment::factory()->create();
        $job = new SendAppointmentConfirmation($appointment);

        expect($job)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
    });

    it('serializes appointment correctly', function () {
        $appointment = Appointment::factory()->create();
        $job = new SendAppointmentConfirmation($appointment);

        expect($job->appointment)->toBeInstanceOf(Appointment::class)
            ->and($job->appointment->id)->toBe($appointment->id);
    });
});
