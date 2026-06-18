document.addEventListener('DOMContentLoaded', () => {
    // 1. Alert Banner Dismissal (Session-based via JS/SessionStorage)
    const alertBanners = document.querySelectorAll('.alert-banner');
    alertBanners.forEach(banner => {
        const id = banner.dataset.bloodtype + '_' + banner.dataset.level;
        if (sessionStorage.getItem('dismiss_' + id)) {
            banner.style.display = 'none';
        }

        const closeBtn = banner.querySelector('.alert-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                banner.style.display = 'none';
                sessionStorage.setItem('dismiss_' + id, 'true');
            });
        }
    });

    // 2. Table Sorting Utility
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            header.addEventListener('click', () => {
                const rows = Array.from(table.querySelectorAll('tbody tr'));
                const isAscending = header.classList.contains('th-sort-asc');
                
                // Reset headers
                headers.forEach(h => h.classList.remove('th-sort-asc', 'th-sort-desc'));
                
                rows.sort((rowA, rowB) => {
                    const cellA = rowA.cells[index].innerText.trim();
                    const cellB = rowB.cells[index].innerText.trim();
                    
                    if (!isNaN(cellA) && !isNaN(cellB)) {
                        return isAscending ? cellB - cellA : cellA - cellB;
                    }
                    
                    return isAscending 
                        ? cellB.localeCompare(cellA) 
                        : cellA.localeCompare(cellB);
                });
                
                if (isAscending) {
                    header.classList.add('th-sort-desc');
                } else {
                    header.classList.add('th-sort-asc');
                }
                
                const tbody = table.querySelector('tbody');
                tbody.innerHTML = '';
                rows.forEach(row => tbody.appendChild(row));
            });
        });
    });

    // 3. Search and Dynamic Filtering Utility (Combined for Inventory, simple for others)
    const inventoryTable = document.getElementById('inventoryTable');
    if (inventoryTable) {
        const bloodTypeFilter = document.getElementById('filter_blood_type');
        const statusFilter = document.getElementById('filter_status');
        const expiryFilter = document.getElementById('filter_expiry');
        const searchInput = document.querySelector('.live-search[data-target-table="inventoryTable"]');
        const resetBtn = document.getElementById('resetFiltersBtn');

        function applyInventoryFilters() {
            const bloodType = bloodTypeFilter ? bloodTypeFilter.value : '';
            const status = statusFilter ? statusFilter.value : '';
            const expiry = expiryFilter ? expiryFilter.value : '';
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

            const rows = inventoryTable.querySelectorAll('tbody tr');
            let visibleCount = 0;

            rows.forEach(row => {
                if (row.classList.contains('no-results-row')) return;
                
                const rowBloodType = row.dataset.bloodType || '';
                const rowStatus = row.dataset.status || '';
                const rowExpiryDateStr = row.dataset.expiryDate || '';

                let match = true;

                // 1. Blood Type Filter
                if (bloodType && rowBloodType !== bloodType) {
                    match = false;
                }

                // 2. Status Filter
                if (status && rowStatus !== status) {
                    match = false;
                }

                // 3. Expiry Filter
                if (expiry && rowExpiryDateStr) {
                    const expiryDate = new Date(rowExpiryDateStr);
                    const now = new Date();
                    now.setHours(0, 0, 0, 0);
                    expiryDate.setHours(0, 0, 0, 0);
                    const diffTime = expiryDate - now;
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    if (expiry === 'expired') {
                        if (diffDays >= 0) match = false;
                    } else if (expiry === 'expiring_soon') {
                        if (diffDays < 0 || diffDays > 7) match = false;
                    }
                }

                // 4. Text Search Query
                if (query) {
                    const rowText = row.innerText.toLowerCase();
                    if (!rowText.includes(query)) {
                        match = false;
                    }
                }

                if (match) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Handle no results message
            let noResultsRow = inventoryTable.querySelector('.no-results-row');
            if (visibleCount === 0) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results-row';
                    noResultsRow.innerHTML = `<td colspan="7" style="text-align: center; color: var(--text-muted);">No blood units found matching the criteria.</td>`;
                    inventoryTable.querySelector('tbody').appendChild(noResultsRow);
                } else {
                    noResultsRow.style.display = '';
                }
            } else {
                if (noResultsRow) {
                    noResultsRow.style.display = 'none';
                }
            }
        }

        // Add listeners
        if (bloodTypeFilter) bloodTypeFilter.addEventListener('change', applyInventoryFilters);
        if (statusFilter) statusFilter.addEventListener('change', applyInventoryFilters);
        if (expiryFilter) expiryFilter.addEventListener('change', applyInventoryFilters);
        if (searchInput) {
            searchInput.addEventListener('input', applyInventoryFilters);
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                if (bloodTypeFilter) bloodTypeFilter.value = '';
                if (statusFilter) statusFilter.value = '';
                if (expiryFilter) expiryFilter.value = '';
                if (searchInput) searchInput.value = '';
                applyInventoryFilters();
            });
        }
    }

    const otherSearchInputs = document.querySelectorAll('.live-search');
    otherSearchInputs.forEach(input => {
        const targetTableId = input.dataset.targetTable;
        if (targetTableId === 'inventoryTable') return;
        const table = document.getElementById(targetTableId);
        if (table) {
            input.addEventListener('input', () => {
                const filter = input.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });
        }
    });

    // 4. Auto-submit filter forms on select dropdown change
    const autoSubmitSelects = document.querySelectorAll('.auto-submit-select');
    autoSubmitSelects.forEach(select => {
        select.addEventListener('change', () => {
            select.closest('form').submit();
        });
    });
});
