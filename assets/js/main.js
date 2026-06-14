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

    // 3. Search and Dynamic Filtering Utility
    const searchInputs = document.querySelectorAll('.live-search');
    searchInputs.forEach(input => {
        const targetTableId = input.dataset.targetTable;
        const table = document.getElementById(targetTableId);
        if (table) {
            input.addEventListener('keyup', () => {
                const filter = input.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });
        }
    });
});
