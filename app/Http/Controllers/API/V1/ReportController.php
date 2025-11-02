<?php

namespace App\Http\Controllers\API\V1;

use App\Services\Report\ReportService;
use App\Services\Property\PropertyService;
use App\Services\Invoice\InvoiceService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ReportController extends BaseController
{
    protected ReportService $reportService;
    protected PropertyService $propertyService;
    protected InvoiceService $invoiceService;
    protected PaymentService $paymentService;

    public function __construct(
        ReportService $reportService,
        PropertyService $propertyService,
        InvoiceService $invoiceService,
        PaymentService $paymentService
    ) {
        $this->reportService = $reportService;
        $this->propertyService = $propertyService;
        $this->invoiceService = $invoiceService;
        $this->paymentService = $paymentService;
    }

    /**
     * Get financial report
     */
    public function financialReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'property_id' => 'nullable|exists:properties,id',
            'group_by' => 'nullable|in:day,week,month,year',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Only landlords can access reports', [], 403);
            }

            $report = $this->reportService->generateFinancialReport(
                $user,
                Carbon::parse($request->from_date),
                Carbon::parse($request->to_date),
                $request->only(['property_id', 'group_by'])
            );

            return $this->sendResponse($report, 'Financial report generated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to generate financial report', [$e->getMessage()], 500);
        }
    }

    /**
     * Get occupancy report
     */
    public function occupancyReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'property_id' => 'nullable|exists:properties,id',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Only landlords can access reports', [], 403);
            }

            $fromDate = $request->from_date ? Carbon::parse($request->from_date) : now()->startOfMonth();
            $toDate = $request->to_date ? Carbon::parse($request->to_date) : now()->endOfMonth();

            $report = $this->reportService->generateOccupancyReport(
                $user,
                $fromDate,
                $toDate,
                $request->only(['property_id'])
            );

            return $this->sendResponse($report, 'Occupancy report generated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to generate occupancy report', [$e->getMessage()], 500);
        }
    }

    /**
     * Get tenant report
     */
    public function tenantReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'nullable|exists:users,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'include_payment_history' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Only landlords can access reports', [], 403);
            }

            $fromDate = $request->from_date ? Carbon::parse($request->from_date) : now()->startOfYear();
            $toDate = $request->to_date ? Carbon::parse($request->to_date) : now();

            $report = $this->reportService->generateTenantReport(
                $user,
                $fromDate,
                $toDate,
                $request->only(['tenant_id', 'include_payment_history'])
            );

            return $this->sendResponse($report, 'Tenant report generated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to generate tenant report', [$e->getMessage()], 500);
        }
    }

    /**
     * Get revenue report
     */
    public function revenueReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'property_id' => 'nullable|exists:properties,id',
            'breakdown' => 'nullable|in:property,room,category',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Only landlords can access reports', [], 403);
            }

            $report = $this->reportService->generateRevenueReport(
                $user,
                Carbon::parse($request->from_date),
                Carbon::parse($request->to_date),
                $request->only(['property_id', 'breakdown'])
            );

            return $this->sendResponse($report, 'Revenue report generated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to generate revenue report', [$e->getMessage()], 500);
        }
    }

    /**
     * Get expense report
     */
    public function expenseReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'property_id' => 'nullable|exists:properties,id',
            'category' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Only landlords can access reports', [], 403);
            }

            // For now, return a basic expense structure
            // You can expand this with actual expense tracking
            $report = [
                'period' => [
                    'from' => $request->from_date,
                    'to' => $request->to_date,
                ],
                'total_expenses' => 0,
                'categories' => [
                    'maintenance' => 0,
                    'utilities' => 0,
                    'management' => 0,
                    'other' => 0,
                ],
                'expense_list' => [],
                'net_income' => 0,
            ];

            return $this->sendResponse($report, 'Expense report generated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to generate expense report', [$e->getMessage()], 500);
        }
    }

    /**
     * Get utility report
     */
    public function utilityReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'property_id' => 'nullable|exists:properties,id',
            'room_id' => 'nullable|exists:rooms,id',
            'meter_type' => 'nullable|in:electricity,water,gas',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Only landlords can access reports', [], 403);
            }

            $report = $this->reportService->generateUtilityReport(
                $user,
                Carbon::parse($request->from_date),
                Carbon::parse($request->to_date),
                $request->only(['property_id', 'room_id', 'meter_type'])
            );

            return $this->sendResponse($report, 'Utility report generated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to generate utility report', [$e->getMessage()], 500);
        }
    }

    /**
     * Export report
     */
    public function exportReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_type' => 'required|in:financial,occupancy,tenant,revenue,expense,utility',
            'format' => 'required|in:pdf,excel,csv',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'property_id' => 'nullable|exists:properties,id',
            'email' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Only landlords can export reports', [], 403);
            }

            // Generate the report based on type
            $reportData = null;
            $fromDate = Carbon::parse($request->from_date);
            $toDate = Carbon::parse($request->to_date);

            switch ($request->report_type) {
                case 'financial':
                    $reportData = $this->reportService->generateFinancialReport($user, $fromDate, $toDate);
                    break;
                case 'occupancy':
                    $reportData = $this->reportService->generateOccupancyReport($user, $fromDate, $toDate);
                    break;
                case 'tenant':
                    $reportData = $this->reportService->generateTenantReport($user, $fromDate, $toDate);
                    break;
                case 'revenue':
                    $reportData = $this->reportService->generateRevenueReport($user, $fromDate, $toDate);
                    break;
                case 'utility':
                    $reportData = $this->reportService->generateUtilityReport($user, $fromDate, $toDate);
                    break;
            }

            // Generate export file based on format
            $fileName = $request->report_type . '_report_' . now()->format('Y-m-d_His');
            $filePath = null;

            switch ($request->format) {
                case 'csv':
                    $filePath = $this->exportToCsv($reportData, $fileName);
                    break;
                case 'excel':
                    $filePath = $this->exportToExcel($reportData, $fileName);
                    break;
                case 'pdf':
                    $filePath = $this->exportToPdf($reportData, $fileName, $request->report_type);
                    break;
            }

            if ($request->has('email')) {
                // Queue email with attachment
                // You would implement email sending here
                return $this->sendResponse([
                    'message' => "Report will be sent to {$request->email}",
                    'file_url' => asset($filePath),
                ], 'Report export initiated');
            }

            return $this->sendResponse([
                'file_url' => asset($filePath),
                'file_name' => basename($filePath),
            ], 'Report exported successfully');

        } catch (\Exception $e) {
            return $this->sendError('Failed to export report', [$e->getMessage()], 500);
        }
    }

    /**
     * Get report summary
     */
    public function getSummary(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Only landlords can access reports', [], 403);
            }

            // Get current month dates
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();

            // Get previous month dates
            $startOfPrevMonth = now()->subMonth()->startOfMonth();
            $endOfPrevMonth = now()->subMonth()->endOfMonth();

            // Generate mini reports
            $currentFinancial = $this->reportService->generateFinancialReport($user, $startOfMonth, $endOfMonth);
            $prevFinancial = $this->reportService->generateFinancialReport($user, $startOfPrevMonth, $endOfPrevMonth);

            $occupancy = $this->reportService->generateOccupancyReport($user, $startOfMonth, $endOfMonth);

            $summary = [
                'current_month' => [
                    'revenue' => $currentFinancial['summary']['total_revenue'] ?? 0,
                    'collected' => $currentFinancial['summary']['total_collected'] ?? 0,
                    'pending' => $currentFinancial['summary']['total_pending'] ?? 0,
                    'occupancy_rate' => $occupancy['summary']['average_occupancy'] ?? 0,
                ],
                'previous_month' => [
                    'revenue' => $prevFinancial['summary']['total_revenue'] ?? 0,
                    'collected' => $prevFinancial['summary']['total_collected'] ?? 0,
                ],
                'trends' => [
                    'revenue_change' => $this->calculatePercentageChange(
                        $prevFinancial['summary']['total_revenue'] ?? 0,
                        $currentFinancial['summary']['total_revenue'] ?? 0
                    ),
                    'collection_change' => $this->calculatePercentageChange(
                        $prevFinancial['summary']['total_collected'] ?? 0,
                        $currentFinancial['summary']['total_collected'] ?? 0
                    ),
                ],
                'top_properties' => $this->reportService->getTopPerformingProperties($user, 5),
                'payment_statistics' => $this->reportService->getPaymentStatistics($user, $startOfMonth, $endOfMonth),
            ];

            return $this->sendResponse($summary, 'Report summary generated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to generate report summary', [$e->getMessage()], 500);
        }
    }

    // ==================== Helper Methods ====================

    /**
     * Calculate percentage change
     */
    protected function calculatePercentageChange($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }

        return round((($newValue - $oldValue) / $oldValue) * 100, 2);
    }

    /**
     * Export to CSV
     */
    protected function exportToCsv($data, $fileName)
    {
        $filePath = 'exports/' . $fileName . '.csv';
        $fullPath = storage_path('app/public/' . $filePath);

        // Ensure directory exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $file = fopen($fullPath, 'w');

        // Write headers if data has summary
        if (isset($data['summary'])) {
            fputcsv($file, ['Summary']);
            foreach ($data['summary'] as $key => $value) {
                fputcsv($file, [str_replace('_', ' ', ucfirst($key)), $value]);
            }
            fputcsv($file, []); // Empty line
        }

        // Write main data
        if (isset($data['data']) && is_array($data['data']) && !empty($data['data'])) {
            // Get headers from first row
            $firstRow = reset($data['data']);
            if (is_array($firstRow)) {
                fputcsv($file, array_keys($firstRow));

                // Write data rows
                foreach ($data['data'] as $row) {
                    fputcsv($file, array_values($row));
                }
            }
        }

        fclose($file);

        return 'storage/' . $filePath;
    }

    /**
     * Export to Excel (simplified version)
     */
    protected function exportToExcel($data, $fileName)
    {
        // For a simple implementation, we'll use CSV format
        // In production, you would use a library like PhpSpreadsheet
        return $this->exportToCsv($data, $fileName);
    }

    /**
     * Export to PDF (simplified version)
     */
    protected function exportToPdf($data, $fileName, $reportType)
    {
        // For a simple implementation, we'll create an HTML file
        // In production, you would use a library like DomPDF or TCPDF
        $filePath = 'exports/' . $fileName . '.html';
        $fullPath = storage_path('app/public/' . $filePath);

        // Ensure directory exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $html = $this->generateReportHtml($data, $reportType);
        file_put_contents($fullPath, $html);

        return 'storage/' . $filePath;
    }

    /**
     * Generate HTML for PDF export
     */
    protected function generateReportHtml($data, $reportType)
    {
        $html = '<!DOCTYPE html><html><head>';
        $html .= '<title>' . ucfirst($reportType) . ' Report</title>';
        $html .= '<style>
            body { font-family: Arial, sans-serif; }
            h1 { color: #333; }
            table { border-collapse: collapse; width: 100%; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .summary { background-color: #f9f9f9; padding: 15px; margin-bottom: 20px; }
        </style>';
        $html .= '</head><body>';

        $html .= '<h1>' . ucfirst($reportType) . ' Report</h1>';
        $html .= '<p>Generated on: ' . now()->format('Y-m-d H:i:s') . '</p>';

        // Add summary if exists
        if (isset($data['summary'])) {
            $html .= '<div class="summary"><h2>Summary</h2>';
            foreach ($data['summary'] as $key => $value) {
                $html .= '<p><strong>' . str_replace('_', ' ', ucfirst($key)) . ':</strong> ' . $value . '</p>';
            }
            $html .= '</div>';
        }

        // Add data table if exists
        if (isset($data['data']) && is_array($data['data']) && !empty($data['data'])) {
            $html .= '<h2>Details</h2><table>';

            // Headers
            $firstRow = reset($data['data']);
            if (is_array($firstRow)) {
                $html .= '<tr>';
                foreach (array_keys($firstRow) as $header) {
                    $html .= '<th>' . str_replace('_', ' ', ucfirst($header)) . '</th>';
                }
                $html .= '</tr>';

                // Data rows
                foreach ($data['data'] as $row) {
                    $html .= '<tr>';
                    foreach ($row as $value) {
                        $html .= '<td>' . $value . '</td>';
                    }
                    $html .= '</tr>';
                }
            }

            $html .= '</table>';
        }

        $html .= '</body></html>';

        return $html;
    }
}