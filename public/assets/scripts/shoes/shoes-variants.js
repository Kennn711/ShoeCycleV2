$(document).ready(function () {
    // ==========================================
    // GLOBAL STATE & VARIABLES
    // ==========================================
    let uploadedFiles = [];
    let isFormCreateValid = false;

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // ==========================================
    // 1. LOGIC MODAL CREATE & VALIDASI (SAMA)
    // ==========================================
    // ... (Bagian resetCreateForm, validation, image upload, color picker, sku generator SAMA SEPERTI SEBELUMNYA) ...
    // Saya persingkat bagian ini agar fokus ke perubahan yang diminta.
    // Pastikan kode validation dan logic UI create tetap ada di file asli Anda.

    function resetCreateForm() {
        const form = $("#form-create-variant");
        form[0].reset();
        uploadedFiles = [];
        isFormCreateValid = false;
        $(".input-error").removeClass("input-error");
        $("[id^='error-']").addClass("hidden").text("");
        $("#create-global-error").addClass("hidden");
        $("#image-preview-container").html("").addClass("hidden");
        $("#sku-preview-text").text("-");
        updateColorInputs("#000000");
        updateCreateButtonState();
    }

    // Helper: Toggle Error
    function toggleError(inputId, errorId, message) {
        if (message) {
            $(inputId).addClass("input-error");
            $(errorId).text(message).removeClass("hidden");
            return false;
        } else {
            $(inputId).removeClass("input-error");
            $(errorId).addClass("hidden").text("");
            return true;
        }
    }

    // Main Validation
    function validateCreateForm() {
        const color = $("#input-color").val().trim();
        const size = $("#input-size").val();
        const price = $("#input-price").val();
        const stock = $("#input-stock").val();
        const skuMode = $("#auto-sku-toggle").is(":checked");
        const manualSku = $("#input-sku").val().trim();
        let isValid = true;

        if (color === "" || color.length < 3) {
            if (color.length > 0)
                toggleError(
                    "#input-color",
                    "#error-color",
                    "Wajib diisi (min 3 char)"
                );
            isValid = false;
        } else {
            toggleError("#input-color", "#error-color", null);
        }

        if (!size)
            isValid =
                toggleError("#input-size", "#error-size", "Pilih ukuran") &&
                isValid;
        else toggleError("#input-size", "#error-size", null);

        if (price === "" || parseFloat(price) <= 0)
            isValid =
                toggleError("#input-price", "#error-price", "Harga > 0") &&
                isValid;
        else toggleError("#input-price", "#error-price", null);

        if (uploadedFiles.length === 0) {
            isValid = false;
            $("#error-images")
                .removeClass("hidden")
                .text("Wajib upload gambar");
        } else {
            $("#error-images").addClass("hidden");
        }

        if (!skuMode && manualSku === "")
            isValid =
                toggleError("#input-sku", "#error-sku", "SKU manual wajib") &&
                isValid;
        else toggleError("#input-sku", "#error-sku", null);

        isFormCreateValid = isValid;
        updateCreateButtonState();
    }

    function updateCreateButtonState() {
        const btn = $("#btn-submit-create");
        if (isFormCreateValid)
            btn.prop("disabled", false).removeClass(
                "opacity-50 cursor-not-allowed"
            );
        else
            btn.prop("disabled", true).addClass(
                "opacity-50 cursor-not-allowed"
            );
    }

    // Event Listeners Validation
    $("#input-color, #input-price, #input-stock, #input-sku").on(
        "input blur",
        validateCreateForm
    );
    $("#input-size, #auto-sku-toggle").on("change", validateCreateForm);

    // Image Upload Logic
    const dropzone = document.getElementById("image-dropzone");
    const fileInput = document.getElementById("image-upload");
    const previewContainer = document.getElementById("image-preview-container");

    if (dropzone) {
        dropzone.addEventListener("click", () => fileInput.click());
        dropzone.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropzone.classList.add("dragover");
        });
        dropzone.addEventListener("dragleave", () =>
            dropzone.classList.remove("dragover")
        );
        dropzone.addEventListener("drop", (e) => {
            e.preventDefault();
            dropzone.classList.remove("dragover");
            handleFiles(e.dataTransfer.files);
        });
        fileInput.addEventListener("change", () =>
            handleFiles(fileInput.files)
        );
    }

    function handleFiles(files) {
        if (!files.length) return;
        Array.from(files).forEach((file) => {
            if (!file.type.startsWith("image/")) return;
            const fileId = Date.now() + Math.random().toString(36).substr(2, 9);
            uploadedFiles.push({ id: fileId, file: file });
            renderImagePreview(file, fileId);
        });
        $("#image-preview-container").removeClass("hidden");
        validateCreateForm();
    }

    function renderImagePreview(file, id) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement("div");
            div.className =
                "relative group aspect-square rounded-lg overflow-hidden border border-gray-200 image-preview-item bg-gray-100";
            div.setAttribute("data-id", id);
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">
                <div class="primary-badge absolute top-1 left-1 bg-blue-500 text-white text-[10px] px-2 py-0.5 rounded-full items-center gap-1 shadow-sm z-10"><i class="fa-solid fa-star text-[8px]"></i> Utama</div>
                <button type="button" class="btn-remove-img absolute top-1 right-1 btn btn-xs btn-circle btn-error text-white opacity-0 group-hover:opacity-100 transition-opacity z-10" onclick="window.removeImage('${id}')"><i class="fa-solid fa-times"></i></button>`;
            previewContainer.appendChild(div);
        };
        reader.readAsDataURL(file);
    }

    window.removeImage = function (id) {
        uploadedFiles = uploadedFiles.filter((f) => f.id !== id);
        $(`[data-id="${id}"]`).remove();
        if (uploadedFiles.length === 0)
            $("#image-preview-container").addClass("hidden");
        validateCreateForm();
    };

    // Color & SKU
    $("#color-picker").on("input", function () {
        $("#color-hex").val($(this).val());
    });
    $("#color-hex").on("input", function () {
        if (
            $(this)
                .val()
                .match(/^#[0-9A-F]{6}$/i)
        )
            $("#color-picker").val($(this).val());
    });
    function updateColorInputs(val) {
        $("#color-picker").val(val);
        $("#color-hex").val(val);
    }

    function generateSKU() {
        if (!$("#auto-sku-toggle").is(":checked")) return;
        const sName = $("#create-shoe-info")
            .text()
            .replace("Untuk sepatu: ", "");
        const sCode =
            sName.substring(0, 3).toUpperCase().replace(/\s/g, "") || "SHO";
        const cPart =
            $("#input-color")
                .val()
                .substring(0, 3)
                .toUpperCase()
                .replace(/\s/g, "") || "CLR";
        const sPart = $("#input-size").val() || "00";
        $("#sku-preview-text").text(
            `${sCode}-${cPart}-${sPart}-${
                Math.floor(Math.random() * 900) + 100
            }`
        );
    }
    $("#input-color, #input-size").on("input change", generateSKU);
    $("#auto-sku-toggle").on("change", function () {
        if ($(this).is(":checked")) {
            $("#manual-sku-container").addClass("hidden");
            $("#auto-sku-preview").removeClass("hidden");
            generateSKU();
        } else {
            $("#manual-sku-container").removeClass("hidden");
            $("#auto-sku-preview").addClass("hidden");
        }
        validateCreateForm();
    });

    // ==========================================
    // 4. AJAX STORE (UPDATED: NO ALERT)
    // ==========================================
    $("#form-create-variant").on("submit", function (e) {
        e.preventDefault();
        validateCreateForm();
        if (!isFormCreateValid) return;

        const btnSubmit = $("#btn-submit-create");
        const btnText = $("#btn-text-create");
        const btnLoading = $("#btn-loading-create");
        const errorMessage = $("#create-global-error");

        btnSubmit
            .prop("disabled", true)
            .addClass("cursor-not-allowed opacity-50");
        btnText.addClass("hidden");
        btnLoading.removeClass("hidden");
        errorMessage.addClass("hidden");

        let formData = new FormData(this);
        formData.delete("images[]");
        uploadedFiles.forEach((fileObj) => {
            formData.append("images[]", fileObj.file);
        });

        $.ajax({
            url: $(this).attr("action"),
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                // UPDATE: Langsung reload tanpa alert
                window.location.reload();
            },
            error: function (xhr) {
                btnSubmit
                    .prop("disabled", false)
                    .removeClass("cursor-not-allowed opacity-50");
                btnText.removeClass("hidden");
                btnLoading.addClass("hidden");
                let msg = "Terjadi kesalahan.";
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    msg = '<ul class="list-disc pl-5 mt-2">';
                    $.each(errors, function (key, value) {
                        msg += `<li>${value[0]}</li>`;
                        $(`[name="${key}"]`).addClass("input-error");
                        $(`#error-${key}`).text(value[0]).removeClass("hidden");
                    });
                    msg += "</ul>";
                }
                $("#create-error-message").html(msg);
                errorMessage.removeClass("hidden");
            },
        });
    });

    // ==========================================
    // 5. AJAX DELETE (NEW IMPLEMENTATION)
    // ==========================================
    $("#form-hapus-varian").on("submit", function (e) {
        e.preventDefault();

        const variantId = $("#delete-variant-id").val();
        const url = `/shoes-variant/${variantId}`; // Asumsi route resource: shoes-variant.destroy

        const btnConfirm = $("#btn-confirm-delete");
        const loading = $("#loading-delete");
        const icon = $("#icon-delete");

        // UI Loading
        btnConfirm.prop("disabled", true);
        loading.removeClass("hidden");
        icon.addClass("hidden");

        $.ajax({
            url: url,
            method: "POST", // Method POST dengan _method DELETE
            data: $(this).serialize(), // Serialize csrf token & method delete
            success: function (response) {
                // UPDATE: Langsung reload tanpa alert
                window.location.reload();
            },
            error: function (xhr) {
                // Kembalikan UI jika error (jarang terjadi di delete kecuali ID hilang)
                btnConfirm.prop("disabled", false);
                loading.addClass("hidden");
                icon.removeClass("hidden");
                alert("Gagal menghapus data. Silakan coba lagi.");
            },
        });
    });

    // ==========================================
    // 6. HELPER & MODAL TRIGGERS
    // ==========================================

    window.openCreateVariantModal = function (shoeId, shoeName) {
        resetCreateForm();
        $("#create-shoe-id").val(shoeId);
        $("#create-shoe-info").text(`Untuk sepatu: ${shoeName}`);
        document.getElementById("modal_create_varian").showModal();
    };

    function closeCreateModal() {
        document.getElementById("modal_create_varian").close();
        setTimeout(() => resetCreateForm(), 100);
    }

    $("#btn-batal-create, .btn-close-modal").on("click", function (e) {
        e.preventDefault();
        closeCreateModal();
    });
    $(document).on("click", ".modal-backdrop", function (e) {
        const m = document.getElementById("modal_create_varian");
        if (m && m.open) {
            e.preventDefault();
            closeCreateModal();
        }
    });

    // Show List Variant
    window.showShoeVariantsModal = function (shoeId) {
        const shoeName = document
            .querySelector(`.shoe-card button[onclick*="${shoeId}"]`)
            .closest(".shoe-card")
            .querySelector("h3").innerText;
        document.getElementById("modal-shoe-name").innerText = shoeName;
        const variants = window.variantsData[shoeId] || [];
        const tbody = document.getElementById("modal-variants-table");
        tbody.innerHTML = "";

        if (variants.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-gray-500">Belum ada varian.</td></tr>`;
        } else {
            variants.forEach((v) => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td><div class="flex items-center gap-2"><div class="w-4 h-4 rounded border" style="background-color:${
                        v.color_code
                    }"></div>${v.color}</div></td>
                    <td>${v.size}</td>
                    <td class="font-mono text-gray-500">${v.sku || "-"}</td>
                    <td>${new Intl.NumberFormat("id-ID").format(v.price)}</td>
                    <td>${v.stock}</td>
                    <td>${
                        v.stock > 0
                            ? '<span class="badge badge-success text-white badge-sm">Tersedia</span>'
                            : '<span class="badge badge-error text-white badge-sm">Habis</span>'
                    }</td>
                    <td class="text-right">
                        <div class="flex gap-1 justify-end">
                            <button class="btn btn-xs bg-yellow-100 text-yellow-600 hover:bg-yellow-200 border-none" onclick="alert('Fitur edit akan datang')"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn btn-xs bg-red-100 text-red-600 hover:bg-red-200 border-none" onclick="openDeleteModal(${
                                v.id
                            }, '${shoeName}', '${v.color}', '${v.size}', '${
                    v.color_code
                }')"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    </td>`;
                tbody.appendChild(tr);
            });
        }
        document.getElementById("btn-add-variant-from-list").onclick = () => {
            document.getElementById("modal_detail_varian").close();
            openCreateVariantModal(shoeId, shoeName);
        };
        document.getElementById("modal-variant-count").innerText =
            variants.length;
        document.getElementById("modal_detail_varian").showModal();
    };

    // UPDATE: Open Delete Modal dengan Color Code
    window.openDeleteModal = function (id, shoeName, color, size, colorCode) {
        $("#delete-variant-id").val(id);
        $("#delete-shoe-name").text(shoeName);
        $("#delete-variant-color").text(color);
        $("#delete-variant-size").text(size);

        // Update Kotak Preview Warna (Hapus #, Ganti Background)
        $("#delete-color-preview")
            .text("") // Hapus teks #
            .css("background-color", colorCode); // Set warna background

        document.getElementById("modal_hapus_varian").showModal();
    };
});
