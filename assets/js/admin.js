// assets/js/admin.js - Admin Functions
document.addEventListener('DOMContentLoaded', function() {
    // Confirm delete actions
    document.querySelectorAll('.confirm-delete').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // Auto-refresh for order status updates
    var orderStatusSelects = document.querySelectorAll('.order-status-select');
    orderStatusSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            var form = this.closest('form');
            if (form) {
                form.submit();
            }
        });
    });
    
    // Product image preview
    var imageInput = document.getElementById('product_image');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            var preview = document.getElementById('imagePreview');
            if (preview && this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});

// Load dashboard statistics via AJAX
function loadDashboardStats() {
    fetch('/admin/api/stats.php')
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                document.getElementById('totalRevenue').textContent = formatCurrency(data.revenue);
                document.getElementById('totalOrders').textContent = data.orders;
                document.getElementById('totalProducts').textContent = data.products;
                document.getElementById('totalUsers').textContent = data.users;
            }
        })
        .catch(function(error) {
            console.error('Error loading stats:', error);
        });
}

// Export data as CSV
function exportTableToCSV(tableId, filename) {
    var table = document.getElementById(tableId);
    if (!table) return;
    
    var rows = table.querySelectorAll('tr');
    var csv = [];
    
    rows.forEach(function(row) {
        var cols = row.querySelectorAll('td, th');
        var rowData = [];
        cols.forEach(function(col) {
            rowData.push('"' + col.textContent.replace(/"/g, '""') + '"');
        });
        csv.push(rowData.join(','));
    });
    
    var csvContent = csv.join('\n');
    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename || 'export.csv';
    link.click();
}

// Print order invoice
function printInvoice() {
    window.print();
}

// Quick search in admin tables
function filterAdminTable(inputId, tableId) {
    var input = document.getElementById(inputId);
    var table = document.getElementById(tableId);
    
    if (input && table) {
        input.addEventListener('keyup', function() {
            var filter = this.value.toLowerCase();
            var rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
}