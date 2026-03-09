<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Shift;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * QR Code Check-in
     */
    public function qrCheckIn(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'employee_id' => 'required|exists:employees,id',
            'device_id' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $branchId = $request->attributes->get('branch_id');

        // Verify QR code
        $cacheKey = "qr_{$request->employee_id}";
        $cachedCode = Cache::get($cacheKey);

        if (!$cachedCode || $cachedCode !== $request->qr_code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired QR code'
            ], 400);
        }

        // Verify employee belongs to this branch
        $employee = Employee::where('id', $request->employee_id)
            ->where('branch_id', $branchId)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found in this branch'
            ], 404);
        }

        // Verify location if provided
        if ($request->latitude && $request->longitude) {
            $branch = Branch::find($branchId);
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $branch->latitude,
                $branch->longitude
            );

            if ($distance > 100) { // 100 meters radius
                return response()->json([
                    'success' => false,
                    'message' => 'You are outside the allowed check-in area'
                ], 400);
            }
        }

        // Process check-in
        $result = $this->attendanceService->checkIn(
            $employee,
            'qr_code',
            [
                'device_id' => $request->device_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'qr_code' => $request->qr_code,
            ]
        );

        // Delete used QR code
        Cache::forget($cacheKey);

        return response()->json($result);
    }

    /**
     * Biometric Check-in (Fingerprint/Face)
     */
    public function biometricCheckIn(Request $request)
    {
        $request->validate([
            'biometric_data' => 'required|string',
            'device_id' => 'required|string',
            'timestamp' => 'required|date',
        ]);

        $branchId = $request->attributes->get('branch_id');

        // Find employee by biometric data
        $employee = Employee::where('biometric_template', $request->biometric_data)
            ->where('branch_id', $branchId)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Biometric data not recognized'
            ], 404);
        }

        $result = $this->attendanceService->checkIn(
            $employee,
            'biometric',
            [
                'device_id' => $request->device_id,
                'timestamp' => $request->timestamp,
            ]
        );

        return response()->json($result);
    }

    /**
     * GPS Check-in via Mobile App
     */
    public function gpsCheckIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'device_id' => 'required|string',
        ]);

        $branchId = $request->attributes->get('branch_id');

        $employee = Employee::where('id', $request->employee_id)
            ->where('branch_id', $branchId)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        // Verify GPS accuracy
        if ($request->accuracy && $request->accuracy > 50) {
            return response()->json([
                'success' => false,
                'message' => 'GPS signal too weak. Please move to an open area.'
            ], 400);
        }

        // Get branch location
        $branch = Branch::find($branchId);
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $branch->latitude,
            $branch->longitude
        );

        // Check if within allowed radius (configurable)
        $allowedRadius = $branch->check_in_radius ?? 100; // meters

        if ($distance > $allowedRadius) {
            return response()->json([
                'success' => false,
                'message' => "You are " . round($distance) . "m away from office. Please move closer.",
                'distance' => $distance,
                'allowed_radius' => $allowedRadius
            ], 400);
        }

        $result = $this->attendanceService->checkIn(
            $employee,
            'gps',
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'device_id' => $request->device_id,
                'distance' => $distance,
            ]
        );

        return response()->json($result);
    }

    /**
     * Check-out
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'method' => 'required|in:qr_code,biometric,manual,gps',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $branchId = $request->attributes->get('branch_id');

        $employee = Employee::where('id', $request->employee_id)
            ->where('branch_id', $branchId)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $result = $this->attendanceService->checkOut(
            $employee,
            $request->method,
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        );

        return response()->json($result);
    }

    /**
     * Generate QR code for check-in
     */
    public function generateQRCode(Request $request, $employeeId)
    {
        $branchId = $request->attributes->get('branch_id');

        $employee = Employee::where('id', $employeeId)
            ->where('branch_id', $branchId)
            ->firstOrFail();

        // Generate unique QR code
        $qrCode = uniqid() . '-' . $employeeId . '-' . time();

        // Store in cache with 5 minute expiry
        Cache::put("qr_{$employeeId}", $qrCode, now()->addMinutes(5));

        // Generate QR code image (you'll need a package like simplesoftwareio/simple-qrcode)
        $qrImage = \QrCode::format('png')
            ->size(300)
            ->generate($qrCode);

        return response()->json([
            'success' => true,
            'qr_code' => $qrCode,
            'qr_image' => base64_encode($qrImage),
            'expires_in' => 300, // seconds
        ]);
    }

    /**
     * Get today's attendance summary for dashboard
     */
    public function getTodaySummary(Request $request)
    {
        $branchId = $request->attributes->get('branch_id');

        $summary = $this->attendanceService->getTodaySummary($branchId);

        return response()->json($summary);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return PHP_INT_MAX;
        }

        $earthRadius = 6371000; // meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
