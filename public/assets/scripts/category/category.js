// category.js
$(document).ready(function () {
    let isFormValid = false;
    let isFormEditValid = false;
    let originalEditValue = ""; // Untuk menyimpan nilai asli saat edit

    // ========== FUNGSI TAMBAH KATEGORI ==========

    function closeModal() {
        const modal = document.getElementById("modal_tambah_kategori");
        if (modal && modal.open) {
            modal.close();
        }

        setTimeout(() => {
            $("#form-tambah-kategori")[0].reset();
            $("#error-message").addClass("hidden");
            $("#backend-error").remove();
            $("#category_name").removeClass("input-error");
            $("#category_name").val("");
            isFormValid = false;
            updateButtonState();
        }, 100);
    }

    function openModalTambah() {
        const modal = document.getElementById("modal_tambah_kategori");
        if (modal) {
            if (modal.open) {
                modal.close();
            }
            setTimeout(() => {
                modal.showModal();
            }, 100);
        }
    }

    $(document).on(
        "click",
        "#btn-tambah-kategori, #btn-tambah-kategori-pertama",
        function (e) {
            e.preventDefault();
            openModalTambah();
        }
    );

    $("#btn-close-modal").on("click", function (e) {
        e.preventDefault();
        closeModal();
    });

    $(document).on("click", ".modal-backdrop", function (e) {
        const modalTambah = document.getElementById("modal_tambah_kategori");
        const modalEdit = document.getElementById("modal_edit_kategori");
        const modalDelete = document.getElementById("modal_hapus_kategori");

        if (modalTambah && modalTambah.open) {
            e.preventDefault();
            closeModal();
        } else if (modalEdit && modalEdit.open) {
            e.preventDefault();
            closeModalEdit();
        } else if (modalDelete && modalDelete.open) {
            e.preventDefault();
            closeModalDelete();
        }
    });

    const modalTambah = document.getElementById("modal_tambah_kategori");
    if (modalTambah) {
        modalTambah.addEventListener("close", function () {
            setTimeout(() => {
                $("#form-tambah-kategori")[0].reset();
                $("#error-message").addClass("hidden");
                $("#backend-error").remove();
                $("#category_name").removeClass("input-error");
                $("#category_name").val("");
                isFormValid = false;
                updateButtonState();
            }, 50);
        });
    }

    function updateButtonState() {
        const btnSubmit = $("#btn-submit");

        if (isFormValid) {
            btnSubmit
                .prop("disabled", false)
                .removeClass("opacity-50 cursor-not-allowed")
                .addClass("cursor-pointer");
        } else {
            btnSubmit
                .prop("disabled", true)
                .addClass("opacity-50 cursor-not-allowed")
                .removeClass("cursor-pointer");
        }
    }

    $("#category_name").on("input", function () {
        const value = $(this).val().trim();
        const charCount = value.length;

        if ($("#backend-error").length) {
            $("#backend-error").fadeOut(200, function () {
                $(this).remove();
            });
        }

        $("#error-message").addClass("hidden");
        $(this).removeClass("input-error");
        isFormValid = false;

        if (value === "") {
            showError("Nama kategori tidak boleh kosong");
            updateButtonState();
            return;
        }

        if (charCount < 3) {
            showError("Nama kategori minimal 3 karakter");
            updateButtonState();
            return;
        }

        if (charCount > 30) {
            showError("Nama kategori maksimal 30 karakter");
            updateButtonState();
            return;
        }

        isFormValid = true;
        updateButtonState();
    });

    function showError(message) {
        $("#error-message").text(message).removeClass("hidden");
        $("#category_name").addClass("input-error");
        isFormValid = false;
    }

    $("#form-tambah-kategori").on("submit", function (e) {
        const value = $("#category_name").val().trim();

        if (value === "" || value.length < 3 || value.length > 30) {
            e.preventDefault();
            showError("Harap perbaiki kesalahan sebelum menyimpan");
            updateButtonState();
            return false;
        }

        const btnSubmit = $("#btn-submit");
        const btnText = $("#btn-text");
        const btnLoading = $("#btn-loading");

        btnSubmit
            .prop("disabled", true)
            .removeClass("cursor-pointer")
            .addClass("cursor-not-allowed opacity-50");
        btnText.addClass("hidden");
        btnLoading.removeClass("hidden");
    });

    const modal = document.getElementById("modal_tambah_kategori");
    if (modal && modal.hasAttribute("open")) {
        setTimeout(() => {
            if (!modal.open) {
                modal.showModal();
            }
        }, 100);
    }

    updateButtonState();

    // ========== FUNGSI EDIT KATEGORI ==========

    function openModalEdit(categoryId, categoryName) {
        const modal = document.getElementById("modal_edit_kategori");
        if (modal) {
            if (modal.open) {
                modal.close();
            }

            setTimeout(() => {
                // Set nilai asli (simpan untuk perbandingan)
                originalEditValue = categoryName;

                // Set data ke form
                $("#edit_category_name").val(categoryName);
                $("#edit_category_id").val(categoryId);

                // Set action form
                const editUrl = "/category/" + categoryId;
                $("#form-edit-kategori").attr("action", editUrl);

                // Reset validasi
                $("#error-message-edit").addClass("hidden");
                $("#backend-error-edit").remove();
                $("#edit_category_name").removeClass("input-error");

                // Validasi awal - button disabled karena belum ada perubahan
                isFormEditValid = true; // Format valid
                updateButtonStateEdit(); // Tapi button tetap disabled karena value sama

                // Buka modal
                modal.showModal();
            }, 100);
        }
    }

    $(document).on("click", ".btn-edit", function (e) {
        e.preventDefault();
        const categoryId = $(this).data("id");
        const categoryName = $(this).data("name");

        openModalEdit(categoryId, categoryName);
    });

    function closeModalEdit() {
        const modal = document.getElementById("modal_edit_kategori");
        if (modal && modal.open) {
            modal.close();
        }

        setTimeout(() => {
            resetModalEditData();
        }, 100);
    }

    function resetModalEditData() {
        $("#form-edit-kategori")[0].reset();
        $("#form-edit-kategori").attr("action", "");
        $("#error-message-edit").addClass("hidden");
        $("#backend-error-edit").remove();
        $("#edit_category_name").removeClass("input-error");
        $("#edit_category_name").val("");
        originalEditValue = "";
        isFormEditValid = false;
        resetEditButtonState();
    }

    function updateButtonStateEdit() {
        const btnSubmit = $("#btn-submit-edit");
        const currentValue = $("#edit_category_name").val().trim();

        // Button enabled HANYA jika:
        // 1. Format valid (isFormEditValid = true)
        // 2. Ada perubahan (value berbeda dari original)
        if (
            isFormEditValid &&
            currentValue !== originalEditValue &&
            currentValue !== ""
        ) {
            btnSubmit
                .prop("disabled", false)
                .removeClass("opacity-50 cursor-not-allowed")
                .addClass("cursor-pointer");
        } else {
            btnSubmit
                .prop("disabled", true)
                .addClass("opacity-50 cursor-not-allowed")
                .removeClass("cursor-pointer");
        }
    }

    function resetEditButtonState() {
        const btnSubmit = $("#btn-submit-edit");
        const btnText = $("#btn-text-edit");
        const btnLoading = $("#btn-loading-edit");

        btnSubmit
            .prop("disabled", true)
            .addClass("opacity-50 cursor-not-allowed")
            .removeClass("cursor-pointer");
        btnText.removeClass("hidden");
        btnLoading.addClass("hidden");
    }

    const modalEdit = document.getElementById("modal_edit_kategori");
    if (modalEdit) {
        modalEdit.addEventListener("close", function () {
            setTimeout(() => {
                resetModalEditData();
            }, 50);
        });
    }

    $("#btn-close-modal-edit, #btn-batal-edit").on("click", function (e) {
        e.preventDefault();
        closeModalEdit();
    });

    $("#edit_category_name").on("input", function () {
        const value = $(this).val().trim();
        const charCount = value.length;

        // Hilangkan error backend saat user mengetik
        if ($("#backend-error-edit").length) {
            $("#backend-error-edit").fadeOut(200, function () {
                $(this).remove();
            });
        }

        $("#error-message-edit").addClass("hidden");
        $(this).removeClass("input-error");
        isFormEditValid = false;

        // Validasi kosong
        if (value === "") {
            showErrorEdit("Nama kategori tidak boleh kosong");
            updateButtonStateEdit();
            return;
        }

        // Validasi minimal karakter
        if (charCount < 3) {
            showErrorEdit("Nama kategori minimal 3 karakter");
            updateButtonStateEdit();
            return;
        }

        // Validasi maksimal karakter
        if (charCount > 30) {
            showErrorEdit("Nama kategori maksimal 30 karakter");
            updateButtonStateEdit();
            return;
        }

        // Cek apakah value sama dengan nilai asli
        if (value === originalEditValue) {
            showErrorEdit("Tidak ada perubahan yang dilakukan");
            isFormEditValid = false; // Set false agar button disabled
            updateButtonStateEdit();
            return;
        }

        // Semua validasi lolos DAN ada perubahan
        isFormEditValid = true;
        updateButtonStateEdit();
    });

    function showErrorEdit(message) {
        $("#error-message-edit").text(message).removeClass("hidden");
        $("#edit_category_name").addClass("input-error");
        isFormEditValid = false;
    }

    $("#form-edit-kategori").on("submit", function (e) {
        const value = $("#edit_category_name").val().trim();

        // Validasi format
        if (value === "" || value.length < 3 || value.length > 30) {
            e.preventDefault();
            showErrorEdit("Harap perbaiki kesalahan sebelum menyimpan");
            updateButtonStateEdit();
            return false;
        }

        // Validasi perubahan - mencegah submit jika tidak ada perubahan
        if (value === originalEditValue) {
            e.preventDefault();
            showErrorEdit("Tidak ada perubahan yang dilakukan");
            updateButtonStateEdit();
            return false;
        }

        const btnSubmit = $("#btn-submit-edit");
        const btnText = $("#btn-text-edit");
        const btnLoading = $("#btn-loading-edit");

        btnSubmit
            .prop("disabled", true)
            .removeClass("cursor-pointer")
            .addClass("cursor-not-allowed opacity-50");
        btnText.addClass("hidden");
        btnLoading.removeClass("hidden");
    });

    // Auto-open modal edit jika ada error dari backend
    const modalEditCheck = document.getElementById("modal_edit_kategori");
    if (modalEditCheck && modalEditCheck.hasAttribute("open")) {
        setTimeout(() => {
            if (!modalEditCheck.open) {
                modalEditCheck.showModal();

                // Jika ada old value dari backend, set sebagai originalEditValue
                const oldValue = $("#edit_category_name").val();
                if (oldValue) {
                    // Ambil category_id dari session
                    const categoryId = $("#edit_category_id").val();
                    if (categoryId) {
                        // Set originalEditValue sesuai dengan data yang ada
                        // Karena kita tidak bisa ambil original value dari server saat ini,
                        // kita set originalEditValue = oldValue agar validasi tetap jalan
                        originalEditValue = oldValue;
                        isFormEditValid = true;
                        updateButtonStateEdit();
                    }
                }
            }
        }, 100);
    }

    // ========== FUNGSI DELETE KATEGORI ==========

    function openModalDelete(categoryId, categoryName, categoryDate) {
        const modal = document.getElementById("modal_hapus_kategori");
        if (modal) {
            if (modal.open) {
                modal.close();
            }

            setTimeout(() => {
                $("#delete-category-name").text(categoryName);
                $("#delete-category-date").text(categoryDate);

                const deleteUrl = "/category/" + categoryId;
                $("#form-hapus-kategori").attr("action", deleteUrl);

                resetDeleteButtonState();
                modal.showModal();
            }, 100);
        }
    }

    $(document).on("click", ".btn-delete", function (e) {
        e.preventDefault();
        const categoryId = $(this).data("id");
        const categoryName = $(this).data("name");
        const categoryDate = $(this).data("created");

        openModalDelete(categoryId, categoryName, categoryDate);
    });

    function closeModalDelete() {
        const modal = document.getElementById("modal_hapus_kategori");
        if (modal && modal.open) {
            modal.close();
        }

        setTimeout(() => {
            resetModalDeleteData();
        }, 100);
    }

    function resetModalDeleteData() {
        $("#form-hapus-kategori")[0].reset();
        $("#form-hapus-kategori").attr("action", "");
        $("#delete-category-name").text("-");
        $("#delete-category-date").text("-");
        resetDeleteButtonState();
    }

    function resetDeleteButtonState() {
        const btnKonfirmasi = $("#btn-konfirmasi-hapus");
        const btnHapusText = $("#btn-hapus-text");
        const btnHapusLoading = $("#btn-hapus-loading");

        btnKonfirmasi.prop("disabled", false);
        btnHapusText.removeClass("hidden");
        btnHapusLoading.addClass("hidden");
    }

    const modalDelete = document.getElementById("modal_hapus_kategori");
    if (modalDelete) {
        modalDelete.addEventListener("close", function () {
            setTimeout(() => {
                resetModalDeleteData();
            }, 50);
        });
    }

    $("#btn-close-modal-delete, #btn-batal-hapus").on("click", function (e) {
        e.preventDefault();
        closeModalDelete();
    });

    $("#form-hapus-kategori").on("submit", function (e) {
        const btnKonfirmasi = $("#btn-konfirmasi-hapus");
        const btnHapusText = $("#btn-hapus-text");
        const btnHapusLoading = $("#btn-hapus-loading");

        btnKonfirmasi.prop("disabled", true);
        btnHapusText.addClass("hidden");
        btnHapusLoading.removeClass("hidden");
    });

    // Tambahkan di akhir file category.js, setelah fungsi delete

    // ========== FUNGSI DAFTAR SEPATU ==========

    function openModalShoes(categoryId, categoryName) {
        const modal = document.getElementById("modal_daftar_sepatu");
        if (modal) {
            if (modal.open) {
                modal.close();
            }

            setTimeout(() => {
                // Set nama kategori
                $("#shoes-category-name").text(categoryName);

                // Show loading
                $("#shoes-loading").removeClass("hidden");
                $("#shoes-list-container").html("");

                // Buka modal
                modal.showModal();

                // Load data sepatu via AJAX
                loadShoesByCategory(categoryId);
            }, 100);
        }
    }

    function loadShoesByCategory(categoryId) {
        $.ajax({
            url: `/category/${categoryId}/shoes`,
            method: "GET",
            success: function (response) {
                // Hide loading
                $("#shoes-loading").addClass("hidden");

                // Render shoes list
                renderShoesList(response.shoes);
            },
            error: function (xhr) {
                $("#shoes-loading").addClass("hidden");
                $("#shoes-list-container").html(`
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-exclamation-circle text-4xl text-red-500"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Gagal Memuat Data</h4>
                    <p class="text-gray-500 text-sm">Terjadi kesalahan saat memuat data sepatu</p>
                </div>
            `);
            },
        });
    }

    function renderShoesList(shoes) {
        if (shoes.length === 0) {
            $("#shoes-list-container").html(`
            <div class="flex flex-col items-center justify-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-shoe-prints text-5xl text-gray-400"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Sepatu</h4>
                <p class="text-gray-500 text-sm">Kategori ini belum memiliki sepatu</p>
            </div>
        `);
            return;
        }

        let html = `
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-gray-700">No</th>
                        <th class="text-gray-700">Gambar</th>
                        <th class="text-gray-700">Nama Sepatu</th>
                        <th class="text-gray-700">Merk</th>
                        <th class="text-gray-700">Ukuran</th>
                        <th class="text-gray-700">Harga</th>
                        <th class="text-gray-700">Stok</th>
                        <th class="text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody>
    `;

        shoes.forEach((shoe, index) => {
            const statusBadge =
                shoe.stock > 0
                    ? '<span class="badge badge-success badge-sm">Tersedia</span>'
                    : '<span class="badge badge-error badge-sm">Habis</span>';

            html += `
            <tr>
                <td>${index + 1}</td>
                <td>
                    <div class="avatar">
                        <div class="w-12 h-12 rounded-lg">
                            ${
                                shoe.image
                                    ? `<img src="${shoe.image}" alt="${shoe.name}" class="object-cover" />`
                                    : `<div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <i class="fa-solid fa-image text-gray-400"></i>
                                    </div>`
                            }
                        </div>
                    </div>
                </td>
                <td class="font-medium">${shoe.name}</td>
                <td>${shoe.brand || "-"}</td>
                <td>${shoe.size || "-"}</td>
                <td class="font-semibold text-blue-600">Rp ${formatRupiah(
                    shoe.price
                )}</td>
                <td>
                    <span class="badge badge-outline ${
                        shoe.stock > 10
                            ? "badge-success"
                            : shoe.stock > 0
                            ? "badge-warning"
                            : "badge-error"
                    }">
                        ${shoe.stock} unit
                    </span>
                </td>
                <td>${statusBadge}</td>
            </tr>
        `;
        });

        html += `
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-sm text-gray-500 text-right">
            Total: <span class="font-semibold text-gray-900">${shoes.length}</span> sepatu
        </div>
    `;

        $("#shoes-list-container").html(html);
    }

    // Helper function untuk format rupiah
    function formatRupiah(number) {
        return new Intl.NumberFormat("id-ID").format(number);
    }

    // Event handler untuk tombol daftar sepatu
    $(document).on("click", ".btn-list-shoes", function (e) {
        e.preventDefault();
        const categoryId = $(this).data("id");
        const categoryName = $(this).data("name");

        openModalShoes(categoryId, categoryName);
    });

    // Fungsi untuk close modal shoes
    function closeModalShoes() {
        const modal = document.getElementById("modal_daftar_sepatu");
        if (modal && modal.open) {
            modal.close();
        }

        setTimeout(() => {
            resetModalShoesData();
        }, 100);
    }

    function resetModalShoesData() {
        $("#shoes-category-name").text("-");
        $("#shoes-list-container").html("");
        $("#shoes-loading").addClass("hidden");
    }

    // Event listener untuk close modal shoes
    const modalShoes = document.getElementById("modal_daftar_sepatu");
    if (modalShoes) {
        modalShoes.addEventListener("close", function () {
            setTimeout(() => {
                resetModalShoesData();
            }, 50);
        });
    }

    $("#btn-close-modal-shoes").on("click", function (e) {
        e.preventDefault();
        closeModalShoes();
    });

    // Update backdrop handler untuk shoes modal
    $(document).on("click", ".modal-backdrop", function (e) {
        const modalTambah = document.getElementById("modal_tambah_kategori");
        const modalEdit = document.getElementById("modal_edit_kategori");
        const modalDelete = document.getElementById("modal_hapus_kategori");
        const modalShoes = document.getElementById("modal_daftar_sepatu");

        if (modalTambah && modalTambah.open) {
            e.preventDefault();
            closeModal();
        } else if (modalEdit && modalEdit.open) {
            e.preventDefault();
            closeModalEdit();
        } else if (modalDelete && modalDelete.open) {
            e.preventDefault();
            closeModalDelete();
        } else if (modalShoes && modalShoes.open) {
            e.preventDefault();
            closeModalShoes();
        }
    });
});
