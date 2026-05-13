let cart        = [];
let totalAmount = 0;

document.getElementById('product-search').addEventListener('input', function () {
    const keyword = this.value.toLowerCase().trim();
    const items   = document.querySelectorAll('.product-item');
    const empty   = document.getElementById('search-empty');
    let visible   = 0;

    items.forEach(item => {
        const match = item.dataset.name.includes(keyword);
        item.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    empty.style.display = (visible === 0 && keyword !== '') ? 'block' : 'none';
});
function addToCart(product) {
    const existing = cart.find(i => i.id === product.id);

    if (existing) {
        if (existing.qty < product.stock) {
            existing.qty++;
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Stok Terbatas',
                text: `Stok ${product.name} hanya ${product.stock} unit.`,
                confirmButtonColor: '#4e73df',
                timer: 2000,
                showConfirmButton: false,
            });
            return;
        }
    } else {
        cart.push({
            id:    product.id,
            name:  product.name,
            price: product.price,
            image: product.image,
            qty:   1,
            max:   product.stock,
        });
    }

    renderCart();
}

function updateQty(index, delta) {
    const newQty = cart[index].qty + delta;

    if (newQty <= 0) {
        cart.splice(index, 1);
    } else if (newQty > cart[index].max) {
        Swal.fire({
            icon: 'info',
            title: 'Batas Stok',
            text: `Maksimal ${cart[index].max} unit.`,
            confirmButtonColor: '#4e73df',
            timer: 1800,
            showConfirmButton: false,
        });
        return;
    } else {
        cart[index].qty = newQty;
    }

    renderCart();
}

function clearCart() {
    if (cart.length === 0) return;

    Swal.fire({
        title: 'Kosongkan Keranjang?',
        text: 'Semua produk di keranjang akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Kosongkan',
        cancelButtonText: 'Batal',
    }).then(result => {
        if (result.isConfirmed) {
            cart = [];
            document.getElementById('paid_amount_format').value = '';
            document.getElementById('paid_amount').value = '0';
            renderCart();
        }
    });
}

function renderCart() {
    const wrap       = document.getElementById('cart-table-body');
    const emptyState = document.getElementById('cart-empty-state');
    const badge      = document.getElementById('cart-count');

    wrap.innerHTML = '';
    totalAmount    = 0;
    let totalItems = 0;

    if (cart.length === 0) {
        emptyState.style.display = 'flex';
        badge.textContent = '0 Item';
        document.getElementById('total-display').textContent = 'Rp0';
        calculateChange();
        return;
    }

    emptyState.style.display = 'none';

    cart.forEach((item, index) => {
        const subtotal  = item.price * item.qty;
        totalAmount    += subtotal;
        totalItems     += item.qty;

        wrap.innerHTML += `
        <div class="cart-item">
            <div style="flex:1; min-width:0;">
                <div class="cart-item-name" title="${escHtml(item.name)}">${escHtml(item.name)}</div>
                <div class="cart-item-price">Rp${formatRupiah(item.price)}</div>
            </div>
            <div class="qty-ctrl">
                <button class="qty-btn minus" onclick="updateQty(${index}, -1)">
                    <i class="fas fa-minus" style="font-size:.55rem;"></i>
                </button>
                <span class="qty-num">${item.qty}</span>
                <button class="qty-btn plus" onclick="updateQty(${index}, 1)">
                    <i class="fas fa-plus" style="font-size:.55rem;"></i>
                </button>
            </div>
            <div class="cart-item-sub">Rp${formatRupiah(subtotal)}</div>
        </div>`;
    });

    badge.textContent = `${totalItems} Item`;
    document.getElementById('total-display').textContent = 'Rp' + formatRupiah(totalAmount);
    calculateChange();
}

function formatRupiah(angka) {
    if (!angka && angka !== 0) return '0';
    return new Intl.NumberFormat('id-ID').format(Math.floor(angka));
}

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

const inputFormat = document.getElementById('paid_amount_format');
const inputReal   = document.getElementById('paid_amount');

inputFormat.addEventListener('input', function () {
    const raw = this.value.replace(/[^0-9]/g, '');
    inputReal.value = raw || '0';
    this.value = raw ? formatRupiah(raw) : '';
    calculateChange();
});

function calculateChange() {
    const method = document.getElementById('payment_method').value;
    if (method === 'transfer') {
        document.getElementById('change_amount').textContent = 'Rp0';
        return;
    }
    const paid   = parseInt(inputReal.value) || 0;
    const change = paid - totalAmount;
    document.getElementById('change_amount').textContent =
        'Rp' + (change > 0 ? formatRupiah(change) : '0');
}

async function submitTransaction() {
    if (cart.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Keranjang Kosong',
            text: 'Tambahkan produk terlebih dahulu.',
            confirmButtonColor: '#060607',
        });
        return;
    }

    const method    = document.getElementById('payment_method').value;
    const paidValue = parseInt(inputReal.value) || 0;

    if (method === 'cash' && paidValue < totalAmount) {
        Swal.fire({
            icon: 'error',
            title: 'Uang Kurang!',
            text: `Kurang Rp${formatRupiah(totalAmount - paidValue)} dari total belanja.`,
            confirmButtonColor: '#4e73df',
        });
        return;
    }

    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> MEMPROSES...';

    const payload = {
        cart:           cart,
        payment_method: method,
        total_amount:   totalAmount,
        paid_amount:    method === 'transfer' ? totalAmount : paidValue,
    };

    try {
        const response = await fetch(window.posConfig.storeUrl, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': window.posConfig.csrfToken,
            },
            body: JSON.stringify(payload),
        });

        const result = await response.json();

        if (result.success) {
            if (result.is_transfer) {
                await Swal.fire({
                    icon:  'info',
                    title: 'Transaksi Tersimpan!',
                    html:  `No Invoice: <b>${result.invoice_number}</b><br>
                            <span class="text-warning">
                                <i class="fas fa-hourglass-half mr-1"></i>
                                Menunggu konfirmasi admin
                            </span>`,
                    confirmButtonColor: '#4e73df',
                    confirmButtonText:  'Lihat Struk',
                });
                window.location.href =
                    result.receipt_url + '?from=pos&invoice=' + result.invoice_number;
            } else {
                window.location.href =
                    result.receipt_url +
                    '?status=success&invoice=' + result.invoice_number + '&from=pos';
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Transaksi Gagal',
                text: result.message,
                confirmButtonColor: '#4e73df',
            });
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle mr-1"></i> PROSES TRANSAKSI';
        }

    } catch (err) {
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Koneksi Error',
            text: 'Terjadi kesalahan koneksi ke server.',
            confirmButtonColor: '#4e73df',
        });
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle mr-1"></i> PROSES TRANSAKSI';
    }
}