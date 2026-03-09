<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Printer;
use App\Models\PrinterStatusLog;
use App\Models\TonerLevel;
use App\Models\PaperInventory;
use App\Models\PrintJob;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    /**
     * Update single printer status
     */
    public function updatePrinter(Request $request)
    {
        $data = $request->validate([
            'printer_id' => 'required|exists:printers,id',
            'status' => 'required|string',
            'toner_levels' => 'array',
            'paper_levels' => 'array',
            'print_jobs' => 'array',
            'timestamp' => 'required|date',
        ]);

        $branchId = $request->attributes->get('branch_id');
        $printer = Printer::where('id', $data['printer_id'])
            ->where('branch_id', $branchId)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Update printer status
            $oldStatus = $printer->status;
            $printer->update([
                'status' => $data['status'],
                'last_polled_at' => now(),
            ]);

            // Log status change
            if ($oldStatus != $data['status']) {
                PrinterStatusLog::create([
                    'printer_id' => $printer->id,
                    'status' => $data['status'],
                    'previous_status' => $oldStatus,
                    'logged_at' => $data['timestamp'],
                ]);
            }

            // Update toner levels
            foreach ($data['toner_levels'] as $toner) {
                TonerLevel::updateOrCreate(
                    [
                        'printer_id' => $printer->id,
                        'toner_color' => $toner['color'],
                    ],
                    [
                        'current_level' => $toner['level'],
                        'is_low' => $toner['level'] <= 15,
                        'is_critical' => $toner['level'] <= 5,
                        'last_replaced_at' => isset($toner['replaced']) ? now() : null,
                    ]
                );
            }

            // Update paper levels
            foreach ($data['paper_levels'] as $paper) {
                PaperInventory::updateOrCreate(
                    [
                        'printer_id' => $printer->id,
                        'tray_name' => $paper['tray'],
                    ],
                    [
                        'paper_size' => $paper['size'],
                        'current_sheets' => $paper['sheets'],
                        'is_low' => $paper['sheets'] <= 100,
                        'is_empty' => $paper['sheets'] <= 0,
                        'last_refilled_at' => isset($paper['refilled']) ? now() : null,
                    ]
                );
            }

            // Record new print jobs
            foreach ($data['print_jobs'] as $job) {
                $printJob = PrintJob::firstOrCreate(
                    [
                        'printer_id' => $printer->id,
                        'job_id' => $job['job_id'],
                    ],
                    [
                        'document_name' => $job['document_name'],
                        'pages' => $job['pages'],
                        'copies' => $job['copies'] ?? 1,
                        'color_mode' => $job['color_mode'],
                        'print_side' => $job['print_side'] ?? 'simplex',
                        'status' => 'completed',
                        'completed_at' => $job['timestamp'],
                        'total_price' => $this->calculateJobPrice($job),
                    ]
                );

                // Update printer counters
                $printer->increment('total_pages_count', $job['pages'] * ($job['copies'] ?? 1));
                if ($job['color_mode'] === 'color') {
                    $printer->increment('color_pages', $job['pages'] * ($job['copies'] ?? 1));
                } else {
                    $printer->increment('black_white_pages', $job['pages'] * ($job['copies'] ?? 1));
                }
            }

            DB::commit();

            // Check for alerts
            $this->checkPrinterAlerts($printer);

            return response()->json([
                'success' => true,
                'message' => 'Printer updated successfully',
                'next_poll' => now()->addSeconds(60)->timestamp
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Printer update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update multiple printers
     */
    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'printers' => 'required|array',
            'printers.*.printer_id' => 'required|exists:printers,id',
            'printers.*.status' => 'required|string',
            'printers.*.toner_levels' => 'array',
            'printers.*.paper_levels' => 'array',
            'printers.*.print_jobs' => 'array',
            'timestamp' => 'required|date',
        ]);

        $branchId = $request->attributes->get('branch_id');
        $results = [];

        foreach ($data['printers'] as $printerData) {
            try {
                // Clone request for each printer
                $printerRequest = new Request($printerData);
                $printerRequest->merge(['timestamp' => $data['timestamp']]);

                $result = $this->updatePrinter($printerRequest);
                $results[] = [
                    'printer_id' => $printerData['printer_id'],
                    'success' => true
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'printer_id' => $printerData['printer_id'],
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    /**
     * Upload cached data from agent
     */
    public function uploadCache(Request $request)
    {
        $data = $request->validate([
            'printer_data' => 'array',
            'attendance_data' => 'array',
            'device_id' => 'required|string',
        ]);

        $branchId = $request->attributes->get('branch_id');

        // Process cached printer data
        foreach ($data['printer_data'] as $cached) {
            // Similar to updatePrinter but with timestamps
            $this->processCachedPrinterData($cached, $branchId);
        }

        // Process cached attendance data
        foreach ($data['attendance_data'] as $cached) {
            $this->processCachedAttendance($cached, $branchId);
        }

        return response()->json([
            'success' => true,
            'processed' => [
                'printers' => count($data['printer_data']),
                'attendance' => count($data['attendance_data']),
            ]
        ]);
    }

    /**
     * Get printer configuration for agent
     */
    public function getPrinterConfig($printerId, Request $request)
    {
        $branchId = $request->attributes->get('branch_id');

        $printer = Printer::with('printerModel')
            ->where('id', $printerId)
            ->where('branch_id', $branchId)
            ->firstOrFail();

        return response()->json([
            'printer' => [
                'id' => $printer->id,
                'name' => $printer->name,
                'ip' => $printer->ip_address,
                'brand' => $printer->printerModel->brand,
                'model' => $printer->printerModel->model_number,
                'snmp_community' => $printer->snmp_community,
                'snmp_port' => $printer->snmp_port,
                'poll_interval' => 60, // seconds
            ]
        ]);
    }

    private function calculateJobPrice($job)
    {
        // Implement your pricing logic here
        $pricePerPage = $job['color_mode'] === 'color' ? 5 : 1; // ETB
        return ($job['pages'] * ($job['copies'] ?? 1)) * $pricePerPage;
    }

    private function checkPrinterAlerts($printer)
    {
        // Check toner levels
        $lowToner = $printer->tonerLevels()
            ->where('is_low', true)
            ->exists();

        if ($lowToner) {
            // Send notification to managers
            \Filament\Notifications\Notification::make()
                ->title('Low Toner Alert')
                ->body("Printer {$printer->name} has low toner levels")
                ->danger()
                ->sendToDatabase($printer->branch->managers);
        }
    }
}
