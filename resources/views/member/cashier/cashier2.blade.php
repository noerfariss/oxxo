<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .product-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .product-card:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body class="p-3">

    <div class="row">
        <div class="col-md-8">
            <h4>Kios Oxxo Laundry</h4>
            <p>Jl. Merdekan raya no. 666 Surabaya</p>

            <div class="row g-3">
                {{-- Contoh Produk --}}
                <div class="col-md-3">
                    <div class="product-card"
                         data-name="Suit bawah"
                         data-prices='[{"label":"DC/LD","harga":53000},{"label":"PO","harga":48000}]'>
                        <strong>APPAREL</strong>
                        <p>Suit bawah</p>
                        <p>DC/LD: Rp 53.000 <br> PO: Rp 48.000</p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="product-card"
                         data-name="Jacket"
                         data-prices='[{"label":"DC/LD","harga":71000},{"label":"PO","harga":66000}]'>
                        <strong>APPAREL</strong>
                        <p>Jacket</p>
                        <p>DC/LD: Rp 71.000 <br> PO: Rp 66.000</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 bg-primary text-white p-3">
            <h5>Total: <span id="total">Rp 0</span></h5>
            <hr>
            <h6>Detail transaksi</h6>
            <ul id="cart" class="list-unstyled"></ul>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="priceOptions"></div>
                    <div class="mt-3">
                        <label for="note" class="form-label">Catatan</label>
                        <textarea id="note" class="form-control" placeholder="Misalnya: ada noda..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="addToCart" class="btn btn-primary">Tambahkan</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedProduct = null;
        let cart = [];

        // klik produk
        $(".product-card").on("click", function () {
            selectedProduct = {
                name: $(this).data("name"),
                prices: $(this).data("prices")
            };

            $("#modalTitle").text(selectedProduct.name);
            $("#priceOptions").empty();

            // generate radio harga
            selectedProduct.prices.forEach((p, i) => {
                $("#priceOptions").append(`
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="priceOption" id="price${i}" value="${p.harga}" ${i === 0 ? 'checked' : ''}>
                        <label class="form-check-label" for="price${i}">${p.label} - Rp ${p.harga.toLocaleString()}</label>
                    </div>
                `);
            });

            $("#note").val("");
            new bootstrap.Modal(document.getElementById('productModal')).show();
        });

        // tambahkan ke cart
        $("#addToCart").on("click", function () {
            const price = $("input[name='priceOption']:checked").val();
            const note = $("#note").val();

            cart.push({
                name: selectedProduct.name,
                price: parseInt(price),
                note: note
            });

            updateCart();
            bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
        });

        function updateCart() {
            $("#cart").empty();
            let total = 0;

            cart.forEach((item, idx) => {
                total += item.price;
                $("#cart").append(`<li>${item.name} - Rp ${item.price.toLocaleString()} <br><small>${item.note}</small></li><hr>`);
            });

            $("#total").text("Rp " + total.toLocaleString());
        }
    </script>
</body>
</html>
