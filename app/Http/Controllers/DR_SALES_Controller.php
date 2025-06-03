<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FileImport;
use App\Exports\DR_SALES_Export;
use App\Exports\UM_DR_SALES_Export;

class DR_SALES_Controller extends Controller
{
    // Show the form for file comparison
    public function showDrSales()
    {
        return view('DR_SALES');
    }

    // Handle file upload and comparison
    public function compareDrSales(Request $request)
    {
        // Validate the uploaded files
        $request->validate([
            'file1' => 'required|file|mimes:xlsx,xls',
            'file2' => 'required|file|mimes:xlsx,xls',
        ]);

        // Read both files
        $file1Data = Excel::toArray(new FileImport, $request->file('file1'))[0];
        $file2Data = Excel::toArray(new FileImport, $request->file('file2'))[0];

        // Remove the first two rows (headers) and the last row (footer)
        $file1Data = array_slice($file1Data, 2, -1); // Skips the first two rows and last row (footer)
        $file2Data = array_slice($file2Data, 2, -1); // Skips the first two rows and last row (footer)

        // Store data in session
        session()->put('file1Data', $file1Data);
        session()->put('file2Data', $file2Data);

        // Compare the data between the two files
        $comparisonResults = $this->compareData($file1Data, $file2Data);

        // Store results in session
        session()->put('comparisonResults', $comparisonResults);

        // Return the comparison results to the view
        return view('DR_SALES', [
            'file1Data' => $file1Data,
            'file2Data' => $file2Data,
            'comparisonResults' => $comparisonResults
        ]);
    }

    // Compare the data between File 1 and File 2
    private function compareData($file1Data, $file2Data)
    {
        $results = [];

        // Compare each row in File 1 with File 2
        foreach ($file1Data as $index => $row1) {
            $key = $row1[6] . '-' . $row1[7] . '-' . $row1[8];  // ITEM CODE, ITEM NAME, CATEGORY

            // Find matching rows in File 2
            $matchingRows = [];
            foreach ($file2Data as $row2) {
                if ($row2[9] == $row1[6] && $row2[10] == $row1[7] && $row2[11] == $row1[8]) {
                    $matchingRows[] = [
                        'item_code' => $row2[9],
                        'item_name' => $row2[10],
                        'uom' => $row2[11],
                        'qty' => $row2[14],
                        'file_1_number' => $row2[2]
                    ];
                }
            }

            // Store the result for each item in File 1
            $results[] = [
                'item_code' => $row1[6],
                'item_name' => $row1[7],
                'uom' => $row1[8],
                'file1_qty' => $row1[11],  // QTY in File 1
                'dr_number' => $row1[2],
                'matching_rows' => $matchingRows  // Matching rows from File 2
            ];
        }

        return $results;
    }

    public function export(Request $request)
{
    $comparisonResults = $request->session()->get('comparisonResults');
    $file1Data = $request->session()->get('file1Data');

    // Check existence
    if (!$comparisonResults || !$file1Data) {
        return redirect()->back()->with('error', 'Missing data or invalid file format.');
    }

    // Get first and last date from column 4
    $dates = [];
    foreach ($file1Data as $row) {
        if (!empty($row[5])) {
            try {
                $dates[] = \Carbon\Carbon::parse($row[5]);
            } catch (\Exception $e) {
                // skip invalid date
            }
        }
    }

    if (count($dates) >= 1) {
        $firstDate = $dates[0]->format('m/d/Y h:i A');
        $lastDate = end($dates)->format('m/d/Y h:i A');
        $dateRange = "{$firstDate} - {$lastDate}";
    } else {
        $dateRange = 'UNKNOWN DATE RANGE';
    }

    return Excel::download(new DR_SALES_Export($comparisonResults, $dateRange), 'DR-DRRCV_results.xlsx');
}

    // Export unmatched items (items that exist only in File 1 or only in File 2)
    public function exportUnmatched()
{
    $file1Data = session('file1Data');
    $file2Data = session('file2Data');

    $unmatchedFile1 = [];
    $unmatchedFile2 = [];

    // Unmatched from File 1
    foreach ($file1Data as $row1) {
        $foundMatch = false;
        foreach ($file2Data as $row2) {
            if ($row2[9] == $row1[6] && $row2[10] == $row1[7] && $row2[11] == $row1[8]) {
                $foundMatch = true;
                break;
            }
        }
        if (!$foundMatch) {
            $row1['__source'] = 'file1'; // Add source identifier
            $unmatchedFile1[] = $row1;
        }
    }

    // Unmatched from File 2
    foreach ($file2Data as $row2) {
        $foundMatch = false;
        foreach ($file1Data as $row1) {
            if ($row1[6] == $row2[9] && $row1[7] == $row2[10] && $row1[8] == $row2[11]) {
                $foundMatch = true;
                break;
            }
        }
        if (!$foundMatch) {
            $row2['__source'] = 'file2'; // Add source identifier
            $unmatchedFile2[] = $row2;
        }
    }

    $unmatchedResults = array_merge($unmatchedFile1, $unmatchedFile2);

    return Excel::download(new UM_DR_SALES_Export($unmatchedResults), 'UNMATCHED_DR-DRRCV.xlsx');
}

}
