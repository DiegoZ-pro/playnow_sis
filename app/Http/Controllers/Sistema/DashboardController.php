<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $reportService;
    protected $inventoryService;

    public function __construct(ReportService $reportService, InventoryService $inventoryService)
    {
        $this->reportService = $reportService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Mostrar dashboard principal
     */
    public function index()
    {
        $metrics = $this->reportService->getDashboardMetrics();
        $salesChart = $this->reportService->getSalesChartData(7);
        $topProducts = $this->reportService->getTopSellingProducts(5);
        $lowStockProducts = $this->inventoryService->getLowStockProducts();

        return view('sistema.dashboard', compact(
            'metrics',
            'salesChart',
            'topProducts',
            'lowStockProducts'
        ));
    }

    /**
     * API: Obtener métricas actualizadas (AJAX)
     */
    public function getMetrics()
    {
        return response()->json([
            'success' => true,
            'data' => $this->reportService->getDashboardMetrics(),
        ]);
    }

    /**
     * API: Obtener datos para gráficos (AJAX)
     */
    public function getChartData(Request $request)
    {
        $days = $request->input('days', 7);
        
        return response()->json([
            'success' => true,
            'data' => $this->reportService->getSalesChartData($days),
        ]);
    }
}