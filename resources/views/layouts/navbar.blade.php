<!-- resources/views/layouts/navbar.blade.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-info">
    <div class="container-fluid px-0">
        <a class="navbar-brand ms-3" href="#">ITEM AUDIT TOOL</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('inventory') }}">INVENTORY</a>
                </li>
                <li class="nav-item active">
                <a class="nav-link" href="{{ route('RCV_DR') }}" id="compareRcvDr">RCV-DR</a>
                    <!-- Dropdown menu will be shown on right-click -->
                    <ul class="dropdown-menu" id="compareDropdownRcvDr" style="display: none;">
                        <li><a class="dropdown-item" href="#" id="showMatchRcvDr" onclick="toggleView('RCV-DR')">MATCH-RCV-DR</a></li>
                        <li><a class="dropdown-item" href="#" id="showUnmatchRcvDr" onclick="toggleView('UM-RCV-DR')">UNMATCH-RCV-DR</a></li>
                    </ul>
                </li>
                <li class="nav-item active">
                <a class="nav-link" href="{{ route('DR_SALES') }}" id="compareDrSales">DR-DRRVC</a>
                    <!-- Dropdown menu will be shown on right-click -->
                    <ul class="dropdown-menu" id="compareDropdownDrSales" style="display: none;">
                        <li><a class="dropdown-item" href="#" id="showMatchDrSales" onclick="toggleView('DR-SALES')">MATCH-DR-DRRVC</a></li>
                        <li><a class="dropdown-item" href="#" id="showUnmatchDrSales" onclick="toggleView('UM-DR-SALES')">UNMATCH-DR-DRRVC</a></li>
                    </ul>
                </li>
        </div>
    </div>
</nav>

<script>
    // JavaScript to handle the right-click context menu
    document.getElementById('compareRcvDr').addEventListener('contextmenu', function(event) {
        event.preventDefault(); // Prevent default right-click menu
        
        // Show the custom dropdown menu
        var dropdown = document.getElementById('compareDropdownRcvDr');
        dropdown.style.display = 'block';

        // Position the dropdown menu based on mouse location
        dropdown.style.left = event.pageX + 'px';
        dropdown.style.top = event.pageY + 'px';
    });

    // Hide the dropdown menu if clicking anywhere else
    document.addEventListener('click', function() {
        document.getElementById('compareDropdownRcvDr').style.display = 'none';
    });
    // JavaScript to handle the right-click context menu
    document.getElementById('compareDrSales').addEventListener('contextmenu', function(event) {
        event.preventDefault(); // Prevent default right-click menu
        
        // Show the custom dropdown menu
        var dropdown = document.getElementById('compareDropdownDrSales');
        dropdown.style.display = 'block';

        // Position the dropdown menu based on mouse location
        dropdown.style.left = event.pageX + 'px';
        dropdown.style.top = event.pageY + 'px';
    });

    // Hide the dropdown menu if clicking anywhere else
    document.addEventListener('click', function() {
        document.getElementById('compareDropdownDrSales').style.display = 'none';
    });
</script>