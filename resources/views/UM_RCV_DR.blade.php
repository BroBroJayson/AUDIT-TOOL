<!-- Unmatch Results Table -->
<div class="unmatch-section" id="unmatchRcvDrSection">
    <h3 class="text-center mb-4 text-info fw-bold fst-italic">UNMATCH ITEMS</h3>

    <div class="mt-4 text-left">
        <a href="{{ url('/export-unmatchedRcvDr') }}" class="btn btn-outline-success">
            <i class="bi bi-printer"></i> Excel
        </a>
    </div>
    <!-- Table displaying the comparison results for unmatched items -->
    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
        <table class="table table-striped table-hover" id="comparisonTable">
            <thead class="text-black">
                <tr>
                    <th scope="col" class="table-info" style="position: sticky; top: 0;">No.</th>
                    <th scope="col" class="table-info" style="position: sticky; top: 0;">ITEM CODE</th>
                    <th scope="col" class="table-info" style="position: sticky; top: 0;">ITEM NAME</th>
                    <th scope="col" class="table-info" style="position: sticky; top: 0;">CATEGORY</th>
                    <th scope="col" class="table-info" style="position: sticky; top: 0;">QTY</th>
                    <th scope="col" class="table-info" style="position: sticky; top: 0;">SOURCE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($file1Data as $index => $row)
                    @if(!in_array($row[5], array_column($file2Data, 9)))
                        <tr class="unmatched-row">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row[5] }}</td>
                            <td>{{ $row[6] }}</td>
                            <td>{{ $row[7] }}</td>
                            <td>{{ $row[8] }}</td>
                            <td>Receive</td>
                        </tr>
                    @endif
                @endforeach
                @foreach($file2Data as $index => $row)
                    @if(!in_array($row[9], array_column($file1Data, 5)))
                        <tr class="unmatched-row">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row[9] }}</td>
                            <td>{{ $row[10] }}</td>
                            <td>{{ $row[7] }}</td>
                            <td>{{ $row[14] }}</td>
                            <td>Delivery</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
