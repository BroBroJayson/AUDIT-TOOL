<!-- resources/views/item-audit.blade.php -->
@extends('layouts.master')

@section('title', 'Item Audit Tool - Upload Files')

@section('content')
<!-- <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>404 page</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
    }

    .page_404 {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      height: 100vh;
      background: #fff;
      font-family: 'Arial', sans-serif;
      text-align: center;
      padding: 20px;
    }

    .four_zero_four_bg {
      background-image: url('https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif');
      height: 400px;
      width: 100%;
      background-position: center;
      background-repeat: no-repeat;
      background-size: contain;
      margin-bottom: 20px;
    }

    .four_zero_four_bg h1 {
      font-size: 60px;
      margin-top: 20px;
    }

    .contant_box_404 h3 {
      font-size: 24px;
      margin-bottom: 10px;
    }

    .contant_box_404 p {
      font-size: 16px;
      color: #333;
    }
  </style>
</head>
<body>
  <section class="page_404">
  <h1>WELCOME</h1>
    <div class="four_zero_four_bg">
   
    </div>
    <div class="contant_box_404">
      <h3 class="h2">This page is currently under construction.</h3>     
      <p>Just use the feature INVENTORY and RCV-DR</p>
    </div>
  </section>
</body>
</html> -->

    <div class="container mt-5">
        <!-- File upload form -->
        <form action="{{ url('/DrSalesCompare') }}" method="POST" enctype="multipart/form-data" class="d-flex justify-content-center mb-4">
            @csrf
            <div class="custom-file mr-2">
                <input type="file" name="file1" required class="custom-file-input" id="file1" onchange="updateFileName('file1')">
                <label class="custom-file-label" for="file1">Choose file</label>
            </div>
            <div class="custom-file mr-2">
                <input type="file" name="file2" required class="custom-file-input" id="file2" onchange="updateFileName('file2')">
                <label class="custom-file-label" for="file2">Choose file</label>
            </div>
            <button type="submit" class="btn btn-info">GO!</button>
        </form>

        @if(isset($comparisonResults) && count($comparisonResults) > 0)
            <!-- Match Results Table -->
            <div class="match-section active" id="matchDrSalesSection">
                <h3 class="text-center mb-4">MATCH DR-DRRCV</h3>
                <div class="mt-4 text-left">
                    <a href="{{ url('/export') }}" class="btn btn-outline-success">
                        <i class="bi bi-printer"></i> Excel
                    </a>
                </div>
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-striped table-hover" id="inventoryShot">
                        <thead class="text-black">
                            <tr>
                                <th class="table-info" style="position: sticky; top: 0;">DELIVERY</th>
                                <th class="table-info" style="position: sticky; top: 0;">ITEM CODE</th>
                                <th class="table-info" style="position: sticky; top: 0;">ITEM NAME</th>
                                <th class="table-info" style="position: sticky; top: 0;">UOM</th>
                                <th class="table-info" style="position: sticky; top: 0;">QTY</th>
                                <th class="table-info" style="position: sticky; top: 0;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($file1Data as $index => $row)
                                <tr class="table-row {{ isset($row[6]) && !in_array($row[6], array_column($file2Data, 9)) ? 'unmatched-row' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row[6] }}</td>
                                    <td>{{ $row[7] }}</td>
                                    <td>{{ $row[8] }}</td>
                                    <td>{{ $row[11] }}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm" onclick="toggleFile2Row('{{ $row[6] }}')">DELIVERY RECEIVE</button>
                                    </td>
                                </tr>
                                <tr class="matching-rows file2-row {{ $row[6] }}">
                                    <td colspan="6" class="bg-light">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>DELIVERY RECEIVE</th>
                                                    <th>ITEM CODE</th>
                                                    <th>ITEM NAME</th>
                                                    <th>UOM</th>
                                                    <th>QTY</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($file2Data as $file2Index => $file2Row)
                                                    @if($file2Row[9] == $row[6]) 
                                                        <tr>
                                                            <td>{{ $file2Index + 1 }}</td>
                                                            <td>{{ $file2Row[9] }}</td>
                                                            <td>{{ $file2Row[10] }}</td>
                                                            <td>{{ $file2Row[11] }}</td>
                                                            <td>{{ $file2Row[14] }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            @include('UM_DR_SALES') <!-- Include Unmatch Section -->

            @else
                <section style="text-align: center;">
                    <div>
                        <p>Please upload Delivery and Delivery Recieve File.</p>
                    </div>
                    <div>
                        <img src="https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif" 
                            alt="404 animation" 
                            style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
                    </div>
                </section>
            @endif


    </div>

    <script>
    <!-- // Function to update the file input label to show the selected file name
    function updateFileName(inputId) {
        const fileInput = document.getElementById(inputId);
        const fileName = fileInput.files.length > 0 ? fileInput.files[0].name : 'Choose file';
        const label = fileInput.nextElementSibling;
        label.textContent = fileName;
    }

    // Toggle between match and unmatch sections
    function toggleView(view) {
        const matchDrSalesSection = document.getElementById('matchDrSalesSection');
        const unmatchDrSalesSection = document.getElementById('unmatchDrSalesSection');
        if (view === 'DR-SALES') {
            matchDrSalesSection.classList.add('active');
            unmatchDrSalesSection.classList.remove('active');
        } else if (view === 'UM-DR-SALES') {
            matchDrSalesSection.classList.remove('active');
            unmatchDrSalesSection.classList.add('active');
        }
    }

    // Toggle visibility of file2 rows when clicking on a "View Delivery" button
    function toggleFile2Row(itemCode) {
        let matchingRows = document.querySelectorAll('.file2-row.' + itemCode);
        matchingRows.forEach(function(row) {
            row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
        });
    }
</script>
@endsection
