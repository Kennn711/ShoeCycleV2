/* -------------------------------------------------------------------------- */
/* GLOBAL STATE                                 */
/* -------------------------------------------------------------------------- */
const variantsData = {
    1: [
        {
            id: 101,
            color: "Black/White",
            size: 42,
            sku: "NKE-001",
            price: 1299000,
            stock: 10,
            color_code: "#000000",
        },
    ],
    2: [
        {
            id: 201,
            color: "Core Black",
            size: 40,
            sku: "ADS-001",
            price: 2100000,
            stock: 12,
            color_code: "#1f2937",
        },
    ],
};

// State untuk Modal Create
const createModalState = {
    shoeId: null,
    shoeName: "",
    uploadedFiles: [], // Menyimpan file gambar
};

/* -------------------------------------------------------------------------- */
/* 1. LOGIC MODAL CREATE                            */
/* -------------------------------------------------------------------------- */

// Fungsi Membuka Modal
function openCreateVariantModal(shoeId, shoeName) {
    // 1. Reset State & Form
    createModalState.shoeId = shoeId;
    createModalState.shoeName = shoeName;
    createModalState.uploadedFiles = [];

    const form = document.getElementById("form-create-variant");
    form.reset();

    // 2. Reset UI Elements
    document.getElementById("create-shoe-id").value = shoeId;
    document.getElementById(
        "create-shoe-info"
    ).innerText = `Untuk sepatu: ${shoeName}`;
    document.getElementById("image-preview-container").innerHTML = "";
    document.getElementById("image-preview-container").classList.add("hidden");
    document.getElementById("sku-preview-text").innerText = "-";

    // 3. Reset Color Picker
    updateColorInputs("#000000");

    // 4. Show Modal
    document.getElementById("modal_create_varian").showModal();
}

// --- A. Color Picker Logic ---
const colorPicker = document.getElementById("color-picker");
const colorHex = document.getElementById("color-hex");

function updateColorInputs(val) {
    colorPicker.value = val;
    colorHex.value = val;
}

colorPicker.addEventListener("input", (e) => (colorHex.value = e.target.value));
colorHex.addEventListener("input", (e) => {
    if (e.target.value.match(/^#[0-9A-F]{6}$/i))
        colorPicker.value = e.target.value;
});

// --- B. Image Upload Logic (Refactored) ---
const dropzone = document.getElementById("image-dropzone");
const fileInput = document.getElementById("image-upload");
const previewContainer = document.getElementById("image-preview-container");

// Drag & Drop Events
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
fileInput.addEventListener("change", () => handleFiles(fileInput.files));

// Handle Files Processing
function handleFiles(files) {
    if (!files.length) return;
    Array.from(files).forEach((file) => {
        if (!file.type.startsWith("image/"))
            return alert("Hanya file gambar yang diperbolehkan!");

        // Simpan ke state
        const fileId = Date.now() + Math.random().toString(36).substr(2, 9);
        createModalState.uploadedFiles.push({
            id: fileId,
            file: file,
        });

        // Render Preview
        renderImagePreview(file, fileId);
    });
    previewContainer.classList.remove("hidden");
}

// Render Single Image Preview
function renderImagePreview(file, id) {
    const reader = new FileReader();
    reader.onload = (e) => {
        const div = document.createElement("div");
        div.className =
            "relative group aspect-square rounded-lg overflow-hidden border border-gray-200 image-preview-item";
        div.setAttribute("data-id", id);
        div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-full object-cover">
                <button type="button" class="btn-remove-img absolute top-1 right-1 btn btn-xs btn-circle btn-error opacity-0 transition-opacity" onclick="removeImage('${id}')">
                    <i class="fa-solid fa-times"></i>
                </button>
            `;
        previewContainer.appendChild(div);
    };
    reader.readAsDataURL(file);
}

// Remove Image
function removeImage(id) {
    createModalState.uploadedFiles = createModalState.uploadedFiles.filter(
        (f) => f.id !== id
    );
    const el = previewContainer.querySelector(`[data-id="${id}"]`);
    if (el) el.remove();
    if (createModalState.uploadedFiles.length === 0)
        previewContainer.classList.add("hidden");
}

// --- C. SKU Generator Logic ---
const skuToggle = document.getElementById("auto-sku-toggle");
const manualSkuBox = document.getElementById("manual-sku-container");
const autoSkuBox = document.getElementById("auto-sku-preview");
const inputColor = document.getElementById("input-color");
const inputSize = document.getElementById("input-size");

// Toggle Mode
skuToggle.addEventListener("change", function () {
    if (this.checked) {
        manualSkuBox.classList.add("hidden");
        autoSkuBox.classList.remove("hidden");
        generateSKU(); // Trigger generate saat switch ke auto
    } else {
        manualSkuBox.classList.remove("hidden");
        autoSkuBox.classList.add("hidden");
    }
});

// Listeners untuk Auto Generate
[inputColor, inputSize].forEach((el) =>
    el.addEventListener("input", generateSKU)
);

function generateSKU() {
    if (!skuToggle.checked) return;

    const shoeCode =
        createModalState.shoeName
            .substring(0, 3)
            .toUpperCase()
            .replace(/\s/g, "") || "SHO";
    const colorPart =
        inputColor.value.substring(0, 3).toUpperCase().replace(/\s/g, "") ||
        "CLR";
    const sizePart = inputSize.value || "00";
    const randomPart = Math.floor(Math.random() * 900) + 100;

    document.getElementById(
        "sku-preview-text"
    ).innerText = `${shoeCode}-${colorPart}-${sizePart}-${randomPart}`;
}

/* -------------------------------------------------------------------------- */
/* 2. LOGIC MODAL LAINNYA                           */
/* -------------------------------------------------------------------------- */

// Logic Detail Modal (Table)
function showShoeVariantsModal(shoeId) {
    const shoeName = document
        .querySelector(`.shoe-card button[onclick*="${shoeId}"]`)
        .closest(".shoe-card")
        .querySelector("h3").innerText;
    document.getElementById("modal-shoe-name").innerText = shoeName;

    const variants = variantsData[shoeId] || [];
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
                    <td class="font-mono text-gray-500">${v.sku}</td>
                    <td>${new Intl.NumberFormat("id-ID").format(v.price)}</td>
                    <td>${v.stock}</td>
                    <td>${
                        v.stock > 0
                            ? '<span class="badge badge-success text-white badge-sm">Tersedia</span>'
                            : '<span class="badge badge-error text-white badge-sm">Habis</span>'
                    }</td>
                    <td class="text-right">
                        <div class="flex gap-1 justify-end">
                            <button class="btn btn-xs bg-yellow-100 text-yellow-600 hover:bg-yellow-200 border-none" onclick="alert('Edit logic here')"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn btn-xs bg-red-100 text-red-600 hover:bg-red-200 border-none" onclick="openDeleteModal(${
                                v.id
                            }, '${shoeName}', '${v.color}', '${
                v.size
            }')"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    </td>
                `;
            tbody.appendChild(tr);
        });
    }

    // Connect button "Tambah" di footer modal list ke modal create
    document.getElementById("btn-add-variant-from-list").onclick = () => {
        document.getElementById("modal_detail_varian").close();
        openCreateVariantModal(shoeId, shoeName);
    };

    document.getElementById("modal-variant-count").innerText = variants.length;
    document.getElementById("modal_detail_varian").showModal();
}

// Logic Delete Modal
function openDeleteModal(id, shoeName, color, size) {
    document.getElementById("delete-variant-id").value = id;
    document.getElementById("delete-shoe-name").innerText = shoeName;
    document.getElementById("delete-variant-color").innerText = color;
    document.getElementById("delete-variant-size").innerText = size;
    document.getElementById("modal_hapus_varian").showModal();
}

// Helpers
function openShoeSelectionModal() {
    document.getElementById("modal_select_shoe").showModal();
}

function selectShoeHelper(id, name) {
    document.getElementById("modal_select_shoe").close();
    openCreateVariantModal(id, name);
}
