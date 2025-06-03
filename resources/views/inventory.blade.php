@extends('layouts.master')

@section('title', 'Inventory - Item Audit Tool')

@section('content')
<div class="container">
    <h2 class="text-center my-4">Inventory Shot</h2>

    {{-- Upload Form --}}
    <form action="{{ route('inventory.upload') }}" method="POST" enctype="multipart/form-data" class="mb-3">
        @csrf
        <div class="d-flex justify-content-center">
            <div class="row g-3 align-items-center w-auto">
                <div class="col-auto">
                    <div class="custom-file">
                        <input type="file" name="inventory_file" class="custom-file-input" id="inventory_file" required onchange="updateFileName('inventory_file')">
                        <label class="custom-file-label" for="inventory_file">Choose file</label>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-info">GO!</button>
                </div>
            </div>
        </div>
    </form>

    {{-- QR Code Search --}}
    <form action="{{ route('inventory.search') }}" method="GET" class="mb-3">
        <div class="d-flex justify-content-end">
            <div class="d-flex align-items-end gap-1">
                <input type="text" name="qrcode" id="qrcodeInput" class="form-control form-control-sm" placeholder="Scan QR code..." autofocus>
                <button type="submit" class="btn btn-sm btn-info align-self-end">Search</button>
            </div>
        </div>
    </form>

    {{-- Display Table --}}
    @isset($inventory)
        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
            <table class="table table-striped table-hover mb-0">
                <thead class="table text-center">
                    <tr>
                        <th class="table-info text-nowrap" style="position: sticky; top: 0;">ITEM CODE</th>
                        <th class="table-info text-nowrap" style="position: sticky; top: 0;">SUPPLIER</th>
                        <th class="table-info text-nowrap" style="position: sticky; top: 0;">SI#</th>
                        <th class="table-info text-nowrap" style="position: sticky; top: 0;">CATEGORY</th>
                        <th class="table-info text-nowrap" style="position: sticky; top: 0;">SUB CATEGORY</th>
                        <th class="table-info text-nowrap" style="position: sticky; top: 0;">NAME</th>
                        <th class="table-info text-nowrap" style="position: sticky; top: 0;">OTHERS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventory as $index => $row)
                        @if($index >= 2)
                            @php $itemCode = trim($row[0] ?? '') @endphp
                            <tr data-item-code="{{ $itemCode }}">
                                <td>{{ $row[0] ?? '' }}</td>
                                <td>{{ $row[1] ?? '' }}</td>
                                <td>{{ $row[2] ?? '' }}</td>
                                <td>{{ $row[3] ?? '' }}</td>
                                <td>{{ $row[4] ?? '' }}</td>
                                <td>{{ $row[5] ?? '' }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-info" onclick="toggleDetails({{ $index }})" id="toggle-btn-{{ $index }}">
                                        Show
                                    </button>
                                </td>
                            </tr>
                            <tr class="detail-row" id="details-{{ $index }}" data-item-code="{{ $itemCode }}" style="display: none;">
                                <td colspan="7" class="bg-light p-3">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="table-light text-center">
                                                <tr>
                                                    <th class="table-info">QUANTITY</th>
                                                    <th class="table-info">COST</th>
                                                    <th class="table-info">QTY X COST</th>
                                                    <th class="table-info">SRP</th>
                                                    <th class="table-info">QTY X SRP</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-center">
                                                <tr>
                                                    <td>{{ $row[6] ?? '' }}</td>
                                                    <td>{{ $row[7] ?? '' }}</td>
                                                    <td>{{ $row[8] ?? '' }}</td>
                                                    <td>{{ $row[9] ?? '' }}</td>
                                                    <td>{{ $row[10] ?? '' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endisset
</div>

{{-- Scripts --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function toggleDetails(index) {
        const detailRow = document.getElementById('details-' + index);
        const button = document.getElementById('toggle-btn-' + index);
        if (detailRow.style.display === 'none') {
            detailRow.style.display = '';
            button.textContent = 'Hide';
        } else {
            detailRow.style.display = 'none';
            button.textContent = 'Show';
        }
    }

    function updateFileName(inputId) {
        const fileInput = document.getElementById(inputId);
        const fileName = fileInput.files.length > 0 ? fileInput.files[0].name : 'Choose file';
        const label = document.querySelector(`label[for="${inputId}"]`);
        if (label) {
            label.textContent = fileName;
        }
    }

    function startScanner() {
        const scanner = document.getElementById('scanner');
        scanner.style.display = 'block';

        const html5QrCode = new Html5Qrcode("scanner");
        html5QrCode.start(
            { facingMode: "environment" }, 
            { fps: 10, qrbox: 250 },
            qrCodeMessage => {
                document.getElementById('qrcodeInput').value = qrCodeMessage;
                html5QrCode.stop().then(() => {
                    scanner.style.display = 'none';
                    document.querySelector('form[action="{{ route('inventory.search') }}"]').submit();
                });
            },
            errorMessage => {
                console.warn(`QR Scan error: ${errorMessage}`);
            }
        ).catch(err => {
            console.error("Unable to start QR scanner:", err);
        });
    }

    // === Highlight All Matches Persistently ===
    const query = '{{ request("qrcode") }}'.trim();
    if (query) {
        let matchedCodes = JSON.parse(sessionStorage.getItem('matchedCodes') || '[]');
        if (!matchedCodes.includes(query)) {
            matchedCodes.push(query);
            sessionStorage.setItem('matchedCodes', JSON.stringify(matchedCodes));
        }
    }

    // On page load, apply highlights
    window.addEventListener('DOMContentLoaded', () => {
        const matchedCodes = JSON.parse(sessionStorage.getItem('matchedCodes') || '[]');
        matchedCodes.forEach(code => {
            const rows = document.querySelectorAll(`tr[data-item-code="${code}"]`);
            rows.forEach(row => {
                row.classList.add('table-success');
                if (row.querySelector('button')) {
                    row.classList.add('fw-bold');
                }
            });
        });
    });
</script>
@endsection
