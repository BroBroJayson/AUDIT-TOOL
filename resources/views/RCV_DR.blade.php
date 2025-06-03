<!-- resources/views/item-audit.blade.php -->
@extends('layouts.master')

@section('title', 'Item Audit Tool - Upload Files')

@section('content')
    <div class="container mt-5">
        <!-- File upload form -->
        <form action="{{ url('/RcvDrCompare') }}" method="POST" enctype="multipart/form-data" class="d-flex justify-content-center mb-4">
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

        @if(isset($RcvDrResults) && count($RcvDrResults) > 0)
            <!-- Match Results Table -->
            <div class="match-section active" id="matchRcvDrSection">
                <h3 class="text-center mb-4 text-info fw-bold fst-italic">MATCH ITEMS</h3>
                <div class="mt-4 text-left">
                    <a href="{{ url('/RcvDrExport') }}" class="btn btn-outline-success">
                        <i class="bi bi-printer"></i> Excel
                    </a>
                </div>
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-striped table-hover" id="inventoryShot">
                        <thead class="text-black">
                            <tr>
                                <th scope="col" class="table-info" style="position: sticky; top: 0;">RECEIVING</th>
                                <th scope="col" class="table-info" style="position: sticky; top: 0;">ITEM CODE</th>
                                <th scope="col" class="table-info" style="position: sticky; top: 0;">ITEM NAME</th>
                                <th scope="col" class="table-info" style="position: sticky; top: 0;">CATEGORY</th>
                                <th scope="col" class="table-info" style="position: sticky; top: 0;">QTY</th>
                                <th scope="col" class="table-info" style="position: sticky; top: 0;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($file1Data as $index => $row)
                                <tr class="table-row {{ isset($row[5]) && !in_array($row[5], array_column($file2Data, 9)) ? 'unmatched-row' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row[5] }}</td>
                                    <td>{{ $row[6] }}</td>
                                    <td>{{ $row[7] }}</td>
                                    <td>{{ $row[8] }}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm" onclick="toggleFile2Row('{{ $row[5] }}')">Delivered</button>
                                    </td>
                                </tr>
                                <tr class="matching-rows file2-row {{ $row[5] }}">
                                    <td colspan="6" class="bg-light">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col" >DELIVERY</th>
                                                    <th scope="col" >ITEM CODE</th>
                                                    <th scope="col" >ITEM NAME</th>
                                                    <th scope="col" >CATEGORY</th>
                                                    <th scope="col" >QTY</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($file2Data as $file2Index => $file2Row)
                                                    @if($file2Row[9] == $row[5]) 
                                                        <tr>
                                                            <td>{{ $file2Index + 1 }}</td>
                                                            <td>{{ $file2Row[9] }}</td>
                                                            <td>{{ $file2Row[10] }}</td>
                                                            <td>{{ $file2Row[7] }}</td>
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
            
            @include('UM_RCV_DR') <!-- Include Unmatch Section -->

            @else
                <section style="text-align: center;">
                    <div>
                        <p>Please upload Recieve and Delivery File.</p>
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
    // Function to update the file input label to show the selected file name
    function updateFileName(inputId) {
        const fileInput = document.getElementById(inputId);
        const fileName = fileInput.files.length > 0 ? fileInput.files[0].name : 'Choose file';
        const label = fileInput.nextElementSibling;
        label.textContent = fileName;
    }

    // Toggle between match and unmatch sections
    function toggleView(view) {
        const matchRcvDrSection = document.getElementById('matchRcvDrSection');
        const unmatchRcvDrSection = document.getElementById('unmatchRcvDrSection');
        if (view === 'RCV-DR') {
            matchRcvDrSection.classList.add('active');
            unmatchRcvDrSection.classList.remove('active');
        } else if (view === 'UM-RCV-DR') {
            matchRcvDrSection.classList.remove('active');
            unmatchRcvDrSection.classList.add('active');
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
