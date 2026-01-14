<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Página principal de reportes
     */
    public function index()
    {
        return view('sistema.reports.index');
    }

    /**
     * Reporte de ventas
     */
    public function sales(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $userId = $request->input('user_id');

        $report = $this->reportService->getSalesReport($dateFrom, $dateTo, $userId);

        return view('sistema.reports.sales', compact('report', 'dateFrom', 'dateTo'));
    }

    /**
     * Reporte de productos
     */
    public function products()
    {
        $topProducts = $this->reportService->getTopSellingProducts(20);
        $salesByCategory = $this->reportService->getSalesByCategory();

        return view('sistema.reports.products', compact('topProducts', 'salesByCategory'));
    }

    /**
     * Reporte de inventario
     */
    public function inventory()
    {
        $report = $this->reportService->getInventoryReport();

        return view('sistema.reports.inventory', compact('report'));
    }

    /**
     * Reporte de clientes
     */
    public function customers()
    {
        $frequentCustomers = $this->reportService->getFrequentCustomers(20);

        return view('sistema.reports.customers', compact('frequentCustomers'));
    }

    /**
     * Exportar reporte de ventas
     */
    public function exportSales(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $userId = $request->input('user_id');

        $report = $this->reportService->getSalesReport($dateFrom, $dateTo, $userId);

        // Implementar exportación a Excel/PDF según necesidad
        // Por ahora retornamos JSON para descarga

        $filename = 'ventas_' . $dateFrom . '_' . $dateTo . '.json';

        return response()->json($report)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Exportar reporte de inventario
     */
    public function exportInventory()
    {
        $report = $this->reportService->getInventoryReport();

        $filename = 'inventario_' . now()->format('Y-m-d') . '.json';

        return response()->json($report)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * API: Obtener datos para gráficos personalizados
     */
    public function chartData(Request $request)
    {
        $type = $request->input('type', 'sales');
        $days = $request->input('days', 7);

        $data = [];

        switch ($type) {
            case 'sales':
                $data = $this->reportService->getSalesChartData($days);
                break;
            case 'category':
                $data = $this->reportService->getSalesByCategory();
                break;
            case 'top-products':
                $data = $this->reportService->getTopSellingProducts(10);
                break;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}