let cart = [];
let totalAmount = 0;

// --- 1. FITUR SEARCH & FILTER ---
document
    .getElementById("product-search")
    .addEventListener("input", function (e) {
        filterProducts();
    });

document.querySelectorAll(".btn-category").forEach((btn) => {
    btn.addEventListener("click", function () {
        document
            .querySelectorAll(".btn-category")
            .forEach((b) => b.classList.remove("active"));
        this.classList.add("active");
        filterProducts();
    });
});

function filterProducts() {
    const searchTerm = document
        .getElementById("product-search")
        .value.toLowerCase();
    const activeCategory = document.querySelector(".btn-category.active")
        .dataset.category;

    document.querySelectorAll(".product-item").forEach((item) => {
        const name = item.dataset.name;
        const species = item.dataset.species;
        const category = item.dataset.category;

        const matchesSearch =
            name.includes(searchTerm) || species.includes(searchTerm);
        const matchesCategory =
            activeCategory === "all" || category === activeCategory;

        item.style.display =
            matchesSearch && matchesCategory ? "block" : "none";
    });
}

// --- 2. LOGIKA KERANJANG ---
function addToCart(product) {
    const existing = cart.find((item) => item.id === product.id);
    if (existing) {
        if (existing.qty < product.stock) {
            existing.qty++;
        } else {
            alert("Stok tidak mencukupi!");
        }
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            qty: 1,
            max: product.stock,
        });
    }
    renderCart();
}

function updateQty(index, delta) {
    const newQty = cart[index].qty + delta;
    if (newQty > 0) {
        if (newQty <= cart[index].max) {
            cart[index].qty = newQty;
        } else {
            alert("Maksimal stok tercapai");
        }
    } else {
        cart.splice(index, 1);
    }
    renderCart();
}

// --- FUNGSI HELPER FORMAT RUPIAH ---
// Logika: Mencari grup 3 angka dari belakang dan menyisipkan titik
function formatRupiah(angka) {
    if (angka === null || angka === undefined) return "0";
    
    // Pastikan input adalah angka bulat (menghilangkan .00 jika ada)
    let numberString = Math.floor(angka).toString();
    
    // RegEx: Menambahkan titik setiap 3 digit dari belakang
    return numberString.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function renderCart() {
    const tbody = document.getElementById("cart-table-body");
    const cartCount = document.getElementById("cart-count");
    
    tbody.innerHTML = "";
    totalAmount = 0;
    let totalItems = 0;

    cart.forEach((item, index) => {
        let subtotal = item.price * item.qty;
        totalAmount += subtotal;
        totalItems += item.qty;

        tbody.innerHTML += `
            <tr class="border-bottom">
                <td class="p-2" style="width: 55%">
                    <div class="fw-bold small text-truncate" title="${item.name}">${item.name}</div>
                    <div class="text-muted tiny">Rp${formatRupiah(item.price)}</div>
                </td>
                <td class="p-2 text-center" style="width: 25%">
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="updateQty(${index}, -1)">-</button>
                        <span class="fw-bold small">${item.qty}</span>
                        <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="updateQty(${index}, 1)">+</button>
                    </div>
                </td>
                <td class="p-2 text-end fw-bold small" style="width: 20%">
                    Rp${formatRupiah(subtotal)}
                </td>
            </tr>
        `;
    });

    // Update Counter di kanan atas
    if (cartCount) {
        cartCount.innerText = `${totalItems} Item`;
    }

    // Update Total Belanja
    document.getElementById("total-display").innerText = "Rp" + formatRupiah(totalAmount);
    
    calculateChange();
}

// Tambahkan juga perbaikan pada input manual pembayaran
inputFormat.addEventListener("input", function (e) {
    // Ambil hanya angka saja
    let value = this.value.replace(/[^0-9]/g, "");
    
    // Update input hidden dengan angka murni
    inputReal.value = value;
    
    // Tampilkan ke user dengan format titik yang benar
    this.value = value ? formatRupiah(value) : "";
    
    calculateChange();
});

function calculateChange() {
    const paid = parseInt(inputReal.value) || 0;
    const change = paid - totalAmount;
    
    // Jika kembalian minus, tampilkan 0, jika positif tampilkan format rupiah
    document.getElementById("change_amount").innerText = 
        "Rp" + (change > 0 ? formatRupiah(change) : "0");
}

// --- 4. SUBMIT TRANSAKSI (PERBAIKAN ERROR CSRF) ---
async function submitTransaction() {
    if (cart.length === 0) return alert("Keranjang masih kosong!");
    if (parseInt(inputReal.value) < totalAmount)
        return alert("Uang bayar kurang!");

    const btn = document.getElementById("btn-submit");
    btn.disabled = true;
    btn.innerText = "MEMPROSES...";

    const payload = {
        cart: cart,
        payment_method: document.getElementById("payment_method").value,
        total_amount: totalAmount,
        paid_amount: parseInt(inputReal.value),
    };

    try {
        const response = await fetch(window.posConfig.storeUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": window.posConfig.csrfToken, 
            },
            body: JSON.stringify(payload),
        });

        const result = await response.json();
        if (result.success) {
            alert("Transaksi Berhasil!");
            window.location.reload();
        } else {
            alert("Gagal: " + result.message);
            btn.disabled = false;
            btn.innerText = "PROSES TRANSAKSI";
        }
    } catch (error) {
        console.error(error);
        alert("Terjadi kesalahan koneksi ke server.");
        btn.disabled = false;
        btn.innerText = "PROSES TRANSAKSI";
    }
}
