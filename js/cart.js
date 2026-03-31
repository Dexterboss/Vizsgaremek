// ===== KOSÁR JAVASCRIPT (FETCH API ALAPÚ) =====

const API_URL = "../api/";

document.addEventListener('DOMContentLoaded', function() {
    const cartContainer = document.getElementById('cart-container');

    // ===== KOSÁR BETÖLTÉSE =====
    function loadCart() {
        fetch(API_URL + 'cart-get.php')
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    cartContainer.innerHTML = `<p class="error">${data.error}</p>`;
                    return;
                }

                // Kosár elem megjelenítése
                if (data.count === 0) {
                    cartContainer.innerHTML = `
                        <div class="empty-cart">A kosár üres.</div>
                        <a href="webshop.php" class="back-to-shop">Vissza a webshopba</a>
                    `;
                } else {
                    let cartHTML = `
                        <div class="cart-header">
                            <a href="webshop.php" class="back-to-shop">Vissza a webshopba</a>
                        </div>
                        <div class="cart-items">`;
                    
                    data.items.forEach(item => {
                        cartHTML += `
                            <div class="cart-item">
                                <img src="../images/products/${item.image || 'placeholder.jpg'}" alt="${item.name}">
                                <div class="item-details">
                                    <h3>${item.name}</h3>
                                    <p>${item.price.toLocaleString()} Ft darabja</p>
                                    <div class="quantity-control">
                                        <button class="qty-btn decrease-qty" data-cart-id="${item.cart_id}">-</button>
                                        <span class="quantity">${item.quantity}</span>
                                        <button class="qty-btn increase-qty" data-cart-id="${item.cart_id}">+</button>
                                    </div>
                                </div>
                                <div class="item-price">
                                    <p>${(item.price * item.quantity).toLocaleString()} Ft</p>
                                    <button class="remove-btn" data-cart-id="${item.cart_id}">Eltávolítás</button>
                                </div>
                            </div>
                        `;
                    });

                    cartHTML += '</div>';
                    cartHTML += `
                        <div class="cart-summary">
                            <h2>Végösszesen: ${data.total.toLocaleString()} Ft</h2>
                            <button id="checkout-btn" class="checkout-btn">Megrendelés</button>
                        </div>
                    `;

                    cartContainer.innerHTML = cartHTML;

                    // Eseménykezelők
                    document.querySelectorAll('.increase-qty').forEach(btn => {
                        btn.addEventListener('click', function() {
                            updateQuantity(this.dataset.cartId, 'increase');
                        });
                    });

                    document.querySelectorAll('.decrease-qty').forEach(btn => {
                        btn.addEventListener('click', function() {
                            updateQuantity(this.dataset.cartId, 'decrease');
                        });
                    });

                    document.querySelectorAll('.remove-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            removeFromCart(this.dataset.cartId);
                        });
                    });

                    document.getElementById('checkout-btn').addEventListener('click', showCheckoutForm);
                }
            })
            .catch(err => {
                cartContainer.innerHTML = `<p class="error">Hiba: ${err}</p>`;
            });
    }

    // ===== MENNYISÉG FRISSÍTÉS =====
    function updateQuantity(cartId, action) {
        const formData = new FormData();
        formData.append('cart_id', cartId);
        formData.append('action', action);

        fetch(API_URL + 'cart-update.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadCart();
            } else {
                console.error(data.error || 'Hiba történt');
            }
        })
        .catch(err => console.error('Hiba: ' + err));
    }

    // ===== KOSÁRBÓL ELTÁVOLÍTÁS =====
    function removeFromCart(cartId) {
        const formData = new FormData();
        formData.append('cart_id', cartId);

        fetch(API_URL + 'cart-remove.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadCart();
            } else {
                console.error(data.error || 'Hiba történt');
            }
        })
        .catch(err => console.error('Hiba: ' + err));
    }

    // ===== CHECKOUT FORM MEGJELENÍTÉSE =====
    function showCheckoutForm() {
        // Ha már megvan a form, nem kell duplikálni
        if (document.getElementById('checkout-section')) {
            return;
        }

        const checkoutHTML = `
            <section id="checkout-section" class="checkout-section">
                <div class="checkout-header-inline">
                    <h2><i class='bx bx-package'></i> Rendelés leadása</h2>
                    <button type="button" id="close-checkout" class="close-checkout">Mégse</button>
                </div>
                <form id="checkout-form" class="checkout-form">
                    <div class="form-section">
                        <h3><i class='bx bx-user'></i> Személyes adatok</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name"><i class='bx bx-user-circle'></i> Teljes név *</label>
                                <input type="text" id="name" name="shipping_name" placeholder="pl. Kovács János" required>
                            </div>
                            <div class="form-group">
                                <label for="email"><i class='bx bx-envelope'></i> Email cím *</label>
                                <input type="email" id="email" name="shipping_email" placeholder="email@example.com" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone"><i class='bx bx-phone'></i> Telefonszám *</label>
                                <input type="tel" id="phone" name="shipping_phone" placeholder="+36 30 123 4567" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-section">
                        <h3><i class='bx bx-map-pin'></i> Szállítási cím</h3>
                        <div class="form-group">
                            <label for="address"><i class='bx bx-home'></i> Teljes cím *</label>
                            <textarea id="address" name="shipping_address" placeholder="Irányítószám, Város, Utca, Házszám" required></textarea>
                        </div>
                    </div>
                    <div class="form-section">
                        <h3><i class='bx bx-credit-card'></i> Fizetési mód</h3>
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cash" checked>
                                <span class="payment-label">
                                    <i class='bx bx-money'></i> Utánvét (készpénz vagy kártya)
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="form-section">
                        <h3><i class='bx bx-note'></i> Megjegyzés (opcionális)</h3>
                        <div class="form-group">
                            <textarea id="notes" name="notes" placeholder="Pl. ajtócsengő neve, speciális kézbesítési utasítások"></textarea>
                        </div>
                    </div>
                    <div id="checkout-message" class="checkout-message"></div>
                    <div class="checkout-summary">
                        <p><strong>Szállítási költség:</strong> Ingyenes</p>
                        <p><strong>Fizetendő összeg:</strong> <span id="total-amount">0 Ft</span></p>
                    </div>
                    <div class="form-buttons">
                        <button type="button" class="btn-cancel" id="cancel-checkout">Mégse</button>
                        <button type="submit" class="btn-submit">
                            <i class='bx bx-check'></i> Rendelés megerősítése
                        </button>
                    </div>
                </form>
            </section>
        `;

        cartContainer.insertAdjacentHTML('beforeend', checkoutHTML);

        // Összeg frissítése
        fetch(API_URL + 'cart-get.php')
            .then(res => res.json())
            .then(data => {
                if (data.total) {
                    document.getElementById('total-amount').textContent = data.total.toLocaleString() + ' Ft';
                }
            });

        document.getElementById('checkout-form').addEventListener('submit', submitOrder);
        document.getElementById('close-checkout').addEventListener('click', hideCheckoutForm);
        document.getElementById('cancel-checkout').addEventListener('click', hideCheckoutForm);
    }

    function hideCheckoutForm() {
        const section = document.getElementById('checkout-section');
        if (section) {
            section.remove();
        }
    }

    function setCheckoutMessage(type, message) {
        const msgElement = document.getElementById('checkout-message');
        if (!msgElement) return;

        msgElement.textContent = message;
        msgElement.className = 'checkout-message ' + (type === 'success' ? 'success' : 'error');
        msgElement.style.display = 'block';
    }

    // ===== RENDELÉS LEADÁSA =====
    function submitOrder(e) {
        e.preventDefault();
        
        const form = document.getElementById('checkout-form');
        const submitBtn = form.querySelector('.btn-submit');
        
        // Egyszerű validáció
        const name = form.shipping_name.value.trim();
        const email = form.shipping_email.value.trim();
        const phone = form.shipping_phone.value.trim();
        const address = form.shipping_address.value.trim();
        
        if (!name || !email || !phone || !address) {
            setCheckoutMessage('error', 'Kérjük, töltse ki az összes kötelező mezőt!');
            return;
        }
    
        
        // Gomb letiltása
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class=\'bx bx-loader-alt bx-spin\'></i> Feldolgozás...';
        
        const formData = new FormData(form);
        formData.append('action', 'confirm_order');

        fetch(API_URL + 'checkout.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                cartContainer.innerHTML = `
                    <div class="success-message">
                        <h2><i class='bx bx-check-circle'></i> Rendelés sikeresen leadva!</h2>
                        <p>Rendelés azonosító: <strong>${data.order_id}</strong></p>
                        <p>Köszönjük a vásárlást! </p>
                        <a href="webshop.php" class="btn-back">
                            <i class='bx bx-arrow-back'></i> Vissza a webshopba
                        </a>
                    </div>
                `;
            } else {
                setCheckoutMessage('error', data.error || 'Hiba történt a rendelés leadásakor');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class=\'bx bx-check\'></i> Rendelés megerősítése';
            }
        })
        .catch(err => {
            console.error('Hiba: ' + err);
            setCheckoutMessage('error', 'Hálózati hiba történt. Próbálja újra!');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class=\'bx bx-check\'></i> Rendelés megerősítése';
        });
    }

    // Kezdeti betöltés
    loadCart();
});
