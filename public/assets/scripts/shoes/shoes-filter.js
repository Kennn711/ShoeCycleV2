// ========== SHOES FILTER, SEARCH & PAGINATION ==========
$(document).ready(function () {
    // ========== DATA & STATE MANAGEMENT ==========
    let allShoesData = [];
    let filteredData = [];

    // State variables
    let currentPage = 1;
    let itemsPerPage = 10;
    let currentCategory = "all";
    let currentSort = "no";
    let currentOrder = "asc";
    let searchQuery = "";

    // ========== INISIALISASI DATA ==========
    function initializeData() {
        allShoesData = [];

        $("#tableBody tr").each(function () {
            const row = $(this);

            // Skip empty state row
            if (row.find("td[colspan]").length > 0) {
                return;
            }

            // Extract data dari row
            const shoe = {
                no: parseInt(row.find("td").eq(0).text().trim()) || 0,
                name: row.find("td").eq(1).text().trim(),
                category: row.find("td").eq(2).find(".badge").text().trim(),
                brand: row.find("td").eq(3).text().trim(),
                created_at: row.find("td").eq(5).text().trim(),
                updated_at: row.find("td").eq(6).text().trim(),
                // Simpan data untuk edit/delete buttons
                id: row.find(".btn-edit-shoe").data("id"),
                description:
                    row.find(".btn-edit-shoe").data("description") || "",
                category_id: row.find(".btn-edit-shoe").data("category"),
                is_active: row.find(".btn-edit-shoe").data("active"),
                created_date: row.find(".btn-delete-shoe").data("created"),
                rowElement: row.clone(), // Clone element untuk render ulang
            };

            allShoesData.push(shoe);
        });

        filteredData = [...allShoesData];
        renderTable();
        updatePagination();
        updateDisplayInfo();
    }

    // ========== DROPDOWN HANDLERS ==========

    // Handle Items Per Page
    $(document).on("click", ".dropdown-content li a", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const clickedText = $(this).text().trim();
        const parent = $(this).closest(".dropdown");
        const label = parent.find('[role="button"] span').first();
        const labelParent = parent
            .prevAll("span.text-sm")
            .first()
            .text()
            .trim();

        // Update button text
        label.text(clickedText);

        // Tentukan action berdasarkan label parent
        if (labelParent.includes("Tampilkan")) {
            // Items per page
            const perPage = parseInt(clickedText.match(/\d+/)[0]);
            if (perPage !== itemsPerPage) {
                itemsPerPage = perPage;
                currentPage = 1;
                renderTable();
                updatePagination();
                updateDisplayInfo();
            }
        } else if (labelParent.includes("Kategori")) {
            // Category filter
            const newCategory =
                clickedText === "Semua Kategori" ? "all" : clickedText;
            if (newCategory !== currentCategory) {
                currentCategory = newCategory;
                currentPage = 1;
                applyFilters();
            }
        } else if (labelParent.includes("Urutkan")) {
            // Sort field
            const sortMap = {
                Nama: "name",
                Kategori: "category",
                Stok: "stock",
                Harga: "price",
            };
            const newSort = sortMap[clickedText] || "name";
            if (newSort !== currentSort) {
                currentSort = newSort;
                currentPage = 1;
                applyFilters();
            }
        } else if (labelParent.includes("Urutan")) {
            // Sort order
            const newOrder =
                clickedText.includes("A-Z") || clickedText.includes("Terkecil")
                    ? "asc"
                    : "desc";
            if (newOrder !== currentOrder) {
                currentOrder = newOrder;
                currentPage = 1;
                applyFilters();
            }
        }

        // Close dropdown manually
        parent.find('[role="button"]').blur();
        $("body").click();
    });

    // ========== SEARCH FUNCTION ==========
    let searchTimeout;
    $("#searchInput").on("input", function () {
        clearTimeout(searchTimeout);
        const query = $(this).val().trim().toLowerCase();

        searchTimeout = setTimeout(function () {
            if (query !== searchQuery) {
                searchQuery = query;
                currentPage = 1;
                applyFilters();
            }
        }, 300);
    });

    // ========== APPLY ALL FILTERS ==========
    function applyFilters() {
        // Start dengan semua data
        filteredData = [...allShoesData];

        // 1. Filter by category
        if (currentCategory !== "all") {
            filteredData = filteredData.filter(
                (shoe) =>
                    shoe.category.toLowerCase() ===
                    currentCategory.toLowerCase()
            );
        }

        // 2. Filter by search query
        if (searchQuery) {
            filteredData = filteredData.filter((shoe) => {
                const searchText =
                    `${shoe.name} ${shoe.category} ${shoe.brand} ${shoe.description}`.toLowerCase();
                return searchText.includes(searchQuery);
            });
        }

        // 3. Sort data
        filteredData.sort((a, b) => {
            let aVal = a[currentSort];
            let bVal = b[currentSort];

            // Handle undefined/null
            aVal = aVal || "";
            bVal = bVal || "";

            // Convert to lowercase for string
            if (typeof aVal === "string") aVal = aVal.toLowerCase();
            if (typeof bVal === "string") bVal = bVal.toLowerCase();

            // Compare
            let comparison = 0;
            if (aVal > bVal) comparison = 1;
            if (aVal < bVal) comparison = -1;

            return currentOrder === "asc" ? comparison : -comparison;
        });

        // 4. Render hasil
        renderTable();
        updatePagination();
        updateDisplayInfo();
    }

    // ========== RENDER TABLE ==========
    function renderTable() {
        const tbody = $("#tableBody");
        tbody.empty();

        // Check if no data
        if (filteredData.length === 0) {
            const isFiltered = searchQuery || currentCategory !== "all";
            const message = isFiltered
                ? "Tidak ada data yang sesuai dengan filter/pencarian"
                : "Tidak ada data sepatu";
            const subMessage = isFiltered
                ? "Coba ubah filter atau kata kunci pencarian"
                : 'Klik "Tambah" untuk menambahkan data baru';

            tbody.html(`
                <tr>
                    <td colspan="8" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-shoe-prints text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">${message}</p>
                            <p class="text-gray-400 text-sm">${subMessage}</p>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        // Calculate pagination
        const startIdx = (currentPage - 1) * itemsPerPage;
        const endIdx = Math.min(startIdx + itemsPerPage, filteredData.length);

        // Render rows
        for (let i = startIdx; i < endIdx; i++) {
            const shoe = filteredData[i];
            const row = shoe.rowElement.clone();

            // Update nomor urut
            row.find("td")
                .first()
                .text(i + 1);

            tbody.append(row);
        }
    }

    // ========== PAGINATION ==========
    function updatePagination() {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        const container = $("#paginationContainer");
        container.empty();

        if (totalPages <= 1) {
            return;
        }

        // Previous button
        const prevDisabled = currentPage === 1 ? "btn-disabled" : "";
        container.append(`
            <button class="join-item btn btn-sm ${prevDisabled}" data-page="${currentPage - 1}">
                <i class="fas fa-chevron-left"></i>
            </button>
        `);

        // Page numbers
        const pageNumbers = generatePageNumbers(currentPage, totalPages);
        pageNumbers.forEach((page) => {
            if (page === "...") {
                container.append(`
                    <button class="join-item btn btn-sm btn-disabled">...</button>
                `);
            } else {
                const active = currentPage === page ? "btn-active" : "";
                container.append(`
                    <button class="join-item btn btn-sm ${active}" data-page="${page}">
                        ${page}
                    </button>
                `);
            }
        });

        // Next button
        const nextDisabled = currentPage === totalPages ? "btn-disabled" : "";
        container.append(`
            <button class="join-item btn btn-sm ${nextDisabled}" data-page="${currentPage + 1}">
                <i class="fas fa-chevron-right"></i>
            </button>
        `);
    }

    // Generate page numbers with ellipsis
    function generatePageNumbers(current, total) {
        const pages = [];
        const delta = 1; // Pages to show around current

        if (total <= 7) {
            for (let i = 1; i <= total; i++) {
                pages.push(i);
            }
            return pages;
        }

        // Always show first page
        pages.push(1);

        // Calculate range around current page
        const rangeStart = Math.max(2, current - delta);
        const rangeEnd = Math.min(total - 1, current + delta);

        // Add ellipsis after first if needed
        if (rangeStart > 2) {
            pages.push("...");
        }

        // Add range around current
        for (let i = rangeStart; i <= rangeEnd; i++) {
            pages.push(i);
        }

        // Add ellipsis before last if needed
        if (rangeEnd < total - 1) {
            pages.push("...");
        }

        // Always show last page
        pages.push(total);

        return pages;
    }

    // Handle pagination click
    $(document).on(
        "click",
        "#paginationContainer button:not(.btn-disabled)",
        function (e) {
            e.preventDefault();
            const page = parseInt($(this).data("page"));

            if (page && page !== currentPage && page >= 1) {
                currentPage = page;
                renderTable();
                updatePagination();
                updateDisplayInfo();

                // Smooth scroll to table
                $("html, body").animate(
                    {
                        scrollTop:
                            $(
                                ".bg-white.rounded-xl.shadow-sm.border.border-gray-100.overflow-hidden"
                            ).offset().top - 100,
                    },
                    300
                );
            }
        }
    );

    // ========== UPDATE DISPLAY INFO ==========
    function updateDisplayInfo() {
        const total = filteredData.length;

        if (total === 0) {
            $("#showingStart").text("0");
            $("#showingEnd").text("0");
            $("#totalItems").text("0");
            return;
        }

        const start = (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, total);

        $("#showingStart").text(start);
        $("#showingEnd").text(end);
        $("#totalItems").text(total);
    }

    // ========== TABLE HEADER SORTING ==========
    $(document).on("click", ".sortable", function (e) {
        e.preventDefault();
        const sortField = $(this).data("sort");

        if (!sortField) return;

        // Toggle order if same field
        if (currentSort === sortField) {
            currentOrder = currentOrder === "asc" ? "desc" : "asc";
        } else {
            currentSort = sortField;
            currentOrder = "asc";
        }

        // Update icons
        $(".sortable i")
            .removeClass("fa-sort-up fa-sort-down")
            .addClass("fa-sort");

        const icon = $(this).find("i");
        icon.removeClass("fa-sort").addClass(
            currentOrder === "asc" ? "fa-sort-up" : "fa-sort-down"
        );

        // Apply filters
        applyFilters();
    });

    // ========== RESET FILTERS ==========
    function resetFilters() {
        // Reset state
        currentPage = 1;
        itemsPerPage = 10;
        currentCategory = "all";
        currentSort = "no";
        currentOrder = "asc";
        searchQuery = "";

        // Reset UI
        $("#searchInput").val("");

        // Reset dropdown labels
        const dropdowns = $(".dropdown");
        $(dropdowns[0]).find('[role="button"] span').first().text("10 Data");
        $(dropdowns[1])
            .find('[role="button"] span')
            .first()
            .text("Pilih Kategori");
        $(dropdowns[2]).find('[role="button"] span').first().text("Nama");
        $(dropdowns[3])
            .find('[role="button"] span')
            .first()
            .text("A-Z / Terkecil");

        // Reset sort icons
        $(".sortable i")
            .removeClass("fa-sort-up fa-sort-down")
            .addClass("fa-sort");

        // Apply
        applyFilters();
    }

    // Add reset button if not exists
    if ($("#btn-reset-filters").length === 0) {
        $(".flex.items-end.gap-2").append(`
            <button class="btn btn-sm btn-ghost text-gray-600" id="btn-reset-filters" title="Reset semua filter">
                <i class="fas fa-redo"></i>
            </button>
        `);
    }

    $(document).on("click", "#btn-reset-filters", function (e) {
        e.preventDefault();
        resetFilters();
    });

    // ========== PUBLIC FUNCTION FOR REINITIALIZE ==========
    window.reinitializeShoeTable = function () {
        initializeData();
    };

    // ========== INITIALIZE ==========
    initializeData();

    console.log("✅ Shoes filter, search & pagination initialized");
});
