<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index(): AnonymousResourceCollection
    {
        $services = Service::select([
            'id',
            'name',
            'description',
            'duration_minutes',
            'price'
        ])
            ->orderBy('name')
            ->get();

        return ServiceResource::collection($services);
    }

    /**
     * Display the specified service.
     */
    public function show(int $id): ServiceResource|JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'message' => 'Service not found'
            ], 404);
        }

        return new ServiceResource($service);
    }
}
