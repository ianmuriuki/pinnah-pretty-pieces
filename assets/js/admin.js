// Admin JS – Dynamic tables, modals, AJAX
document.addEventListener('DOMContentLoaded', function() {
    initTables();
    initModals();
    initLogout();
});

function initTables() {
    const tables = document.querySelectorAll('.admin-table');
    tables.forEach(table => {
        // Simple sort on th click
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            header.addEventListener('click', () => sortTable(table, index));
        });

        // Search input
        const searchInput = table.parentNode.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('input', () => searchTable(table, searchInput.value));
        }
    });
}

function sortTable(table, colIndex) {
    const rows = Array.from(table.querySelectorAll('tr')).slice(1);
    const isAsc = table.dataset.sorted === colIndex.toString();
    rows.sort((a, b) => {
        const aVal = a.cells[colIndex].textContent.trim();
        const bVal = b.cells[colIndex].textContent.trim();
        return isAsc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
    });
    if (!isAsc) rows.reverse();
    table.dataset.sorted = colIndex.toString();
    rows.forEach(row => table.appendChild(row));
}

function searchTable(table, query) {
    const rows = table.querySelectorAll('tr');
    rows.forEach(row => {
        const text = Array.from(row.cells).map(cell => cell.textContent).join(' ').toLowerCase();
        row.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
    });
}

// Modals for add/edit
function initModals() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        const closeBtn = modal.querySelector('.close');
        closeBtn.addEventListener('click', () => modal.style.display = 'none');
        window.addEventListener('click', (e) => { if (e.target === modal) modal.style.display = 'none'; });
    });

    // Add buttons open modals
    document.querySelectorAll('.btn-add').forEach(btn => {
        btn.addEventListener('click', () => {
            const modalId = btn.dataset.modal;
            document.getElementById(modalId).style.display = 'block';
        });
    });
}

// AJAX CRUD examples (customize per page)
function addProduct(formData) {
    fetch('../api/products.php?action=add', {
        method: 'POST',
        body: new FormData(formData)  // For file upload
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();  // Refresh table
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function deleteItem(url, id) {
    if (confirm('Delete?')) {
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('Error: ' + data.message);
        });
    }
}

function initLogout() {
    const logoutBtn = document.querySelector('.btn-logout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            fetch('../api/auth.php?action=logout').then(() => location.href = '../');
        });
    }
}

// Edit modal populate (example for products)
function editItem(id) {
    // Fetch single item via API, populate form
    fetch(`../api/products.php?action=get&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const form = document.querySelector('#edit-form');
                form.elements['name'].value = data.data.name;
                // ... populate others
                document.getElementById('edit-modal').style.display = 'block';
            }
        });
}