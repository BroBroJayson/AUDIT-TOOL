<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends Controller
{
    public function showInventory()
    {
        // Load inventory data from session if available
        $inventory = session('inventory_data', []);
        return view('inventory', compact('inventory'));
    }

    public function uploadInventory(Request $request)
    {
        $request->validate([
            'inventory_file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        $file = $request->file('inventory_file');
        $data = Excel::toArray([], $file);

        $sheet = $data[0]; // Only the first sheet

        // Remove the last row (footer)
        array_pop($sheet);

        // Store cleaned data to the session
        session(['inventory_data' => $sheet]);

        return view('inventory', ['inventory' => $sheet]);
    }

    public function searchInventory(Request $request)
    {
        $qrcode = $request->input('qrcode');
        $inventory = session('inventory_data', []);

        return view('inventory', compact('inventory'))->with('qrcode', $qrcode);
    }
}
