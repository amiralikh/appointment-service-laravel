<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthProfessional;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HealthProfessionalController extends Controller
{
    /**
     * Display a listing of health professionals.
     */
    public function index(): AnonymousResourceCollection
    {
        $professionals = HealthProfessional::select([
            'id',
            'name',
            'specialization',
            'email',
            'phone'
        ])
            ->orderBy('name')
            ->get();

        return HealthProfessionalResource::collection($professionals);
    }

    /**
     * Display the specified health professional.
     */
    public function show(int $id): HealthProfessionalResource|JsonResponse
    {
        $professional = HealthProfessional::find($id);

        if (!$professional) {
            return response()->json([
                'message' => 'Health professional not found'
            ], 404);
        }

        return new HealthProfessionalResource($professional);
    }
}
