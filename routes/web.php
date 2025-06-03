<?php

use App\Http\Controllers\RCV_DR_Controller;
use App\Http\Controllers\DR_SALES_Controller;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::get('/RCV_DR', [RCV_DR_Controller::class, 'showRcvDr'])->name('RCV_DR');
Route::post('/RcvDrCompare', [RCV_DR_Controller::class, 'compareRcvDr']);
Route::get('/RcvDrExport', [RCV_DR_Controller::class, 'RcvDrExport']);
// This route is responsible for exporting unmatched results
Route::get('/export-unmatchedRcvDr', [RCV_DR_Controller::class, 'exportRcvDrUnmatched'])->name('export-unmatched');

Route::get('/', [DR_SALES_Controller::class, 'showDrSales'])->name('DR_SALES');
Route::post('/DrSalesCompare', [DR_SALES_Controller::class, 'compareDrSales']);
Route::get('/export', [DR_SALES_Controller::class, 'export']);
// This route is responsible for exporting unmatched results
Route::get('/export-unmatchedDrSales', [DR_SALES_Controller::class, 'exportUnmatched'])->name('export-unmatched');

Route::get('/inventory', [InventoryController::class, 'showInventory'])->name('inventory');
Route::post('/inventory/upload', [InventoryController::class, 'uploadInventory'])->name('inventory.upload');
Route::get('/inventory/search', [InventoryController::class, 'searchInventory'])->name('inventory.search');
