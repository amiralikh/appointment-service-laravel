<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        protected AppointmentService $appointmentService
    ) {}

    /**
     * Store a newly created appointment.
     */
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $appointment = $this->appointmentService->createAppointment(
            $request->validated()
        );

        return new AppointmentResource($appointment)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified appointment.
     */
    public function show(int $id): AppointmentResource|JsonResponse
    {
        $appointment = $this->appointmentService->getAppointment($id);

        if (!$appointment) {
            return response()->json([
                'message' => 'Appointment not found'
            ], 404);
        }

        return new AppointmentResource($appointment);
    }
}
