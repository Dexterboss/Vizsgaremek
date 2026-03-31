// ===== WEBSHOP JAVASCRIPT (FETCH API ALAPÚ) =====

const API_URL = "../api/";

document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const productGrid = document.querySelector('.products-grid');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');

    // ===== ÁR CSÚSZKA ELEMEK =====
    const minRange = document.getElementById('minPrice');
    const maxRange = document.getElementById('maxPrice');
    const minInput = document.getElementById('minInput');
    const maxInput = document.getElementById('maxInput');
    const priceDisplay = document.querySelector('.price-display');

    // Ellenőrzés: van-e filter form
    if (!filterForm) {
        console.warn('Filter form not found!');
        return;
    }

    // ===== ÁR KIJELZÉS FRISSÍTÉS =====
    function updatePriceDisplay() {
        if (!priceDisplay) return;
        const minVal = parseInt(minRange.value);
        const maxVal = parseInt(maxRange.value);
        priceDisplay.innerHTML = `<span>${minVal.toLocaleString()} Ft</span> - <span>${maxVal.toLocaleString()} Ft</span>`;
    }

    // ===== CSÚSZKA Z-INDEX FRISSÍTÉS (mindkét csúszka elérhetősége) =====
    function updateSliderZIndex() {
        if (!minRange || !maxRange) return;
        const minVal = parseInt(minRange.value);
        const maxVal = parseInt(maxRange.value);
        // Ha az értékek közel vannak, az aktív csúszkát tegyük felülre
        if (maxVal - minVal <= 5) {
            minRange.style.zIndex = 6;
            maxRange.style.zIndex = 7;
        } else {
            minRange.style.zIndex = 7;
            maxRange.style.zIndex = 6;
        }
    }

    // ===== CSÚSZKA SZINKRONIZÁCIÓ =====
    if (minRange && maxRange && minInput && maxInput && priceDisplay) {
        const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

        // Min csúszka Change event
        minRange.addEventListener('input', () => {
            if (parseInt(minRange.value) >= parseInt(maxRange.value)) {
                minRange.value = parseInt(maxRange.value) - 1;
            }
            minInput.value = minRange.value;
            updatePriceDisplay();
            updateSliderZIndex();
        });

        // Max csúszka Change event
        maxRange.addEventListener('input', () => {
            if (parseInt(maxRange.value) <= parseInt(minRange.value)) {
                maxRange.value = parseInt(minRange.value) + 1;
            }
            maxInput.value = maxRange.value;
            updatePriceDisplay();
            updateSliderZIndex();
        });

        // Min input Change event
        minInput.addEventListener('input', () => {
            let newMin = parseInt(minInput.value);
            if (isNaN(newMin)) newMin = parseInt(minRange.min);
            newMin = clamp(newMin, parseInt(minRange.min), parseInt(maxRange.value) - 1);
            minInput.value = newMin;
            minRange.value = newMin;
            updatePriceDisplay();
            updateSliderZIndex();
        });

        // Max input Change event
        maxInput.addEventListener('input', () => {
            let newMax = parseInt(maxInput.value);
            if (isNaN(newMax)) newMax = parseInt(maxRange.max);
            newMax = clamp(newMax, parseInt(minRange.value) + 1, parseInt(maxRange.max));
            maxInput.value = newMax;
            maxRange.value = newMax;
            updatePriceDisplay();
            updateSliderZIndex();
        });

        // Kezdeti értékek beállítása
        minInput.value = minRange.value;
        maxInput.value = maxRange.value;
        updatePriceDisplay();
        updateSliderZIndex();
    }

    // ===== SZŰRŐK TÖRLÉSE =====
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            filterForm.reset();
            if (minInput && minRange) {
                minInput.value = minRange.min;
                minRange.value = minRange.min;
            }
            if (maxInput && maxRange) {
                maxInput.value = maxRange.max;
                maxRange.value = maxRange.max;
            }
            updatePriceDisplay();
            updateSliderZIndex();
            loadProducts();
        });
    }

    // ===== TERMÉKEK BETÖLTÉSE API-ból =====
    function loadProducts() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        
        fetch(API_URL + 'products.php?' + params.toString())
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    productGrid.innerHTML = `<p class="error">${data.error}</p>`;
                    return;
                }

                // Márkák és kategóriák feltöltése az első betöltéskor
                if (data.brands && data.brands.length > 0) {
                    updateFilterOptions('brands-container', data.brands, 'selectedBrands[]');
                }
                if (data.categories && data.categories.length > 0) {
                    updateFilterOptions('categories-container', data.categories, 'selectedCategories[]');
                }

                // Termékek megjelenítése
                if (data.products.length === 0) {
                    productGrid.innerHTML = '<p class="no-products">Nincs ilyen termék a kiválasztott szűrők alapján.</p>';
                } else {
                    productGrid.innerHTML = data.products.map(product => `
                        <div class="product-card">
                            <div class="product-image">
                                <img src="../images/products/${product.image || 'placeholder.jpg'}" alt="${product.name}">
                            </div>
                            <div class="product-info">
                                <div class="info-top">
                                    <h3>${product.name}</h3>
                                    <p class="product-description">${product.description.substring(0, 100)}...</p>
                                </div>
                                <div class="info-bottom">
                                    <div class="product-meta">
                                        <span class="brand">${product.brand_name}</span>
                                        <span class="category">${product.category_name}</span>
                                    </div>
                                    <div class="product-rating">
                                        ${Array.from({length: 5}, (_, i) => 
                                            `<i class='bx bxs-star' style="color: ${i + 1 <= product.rating ? 'var(--star)' : 'var(--grey)'}"></i>`
                                        ).join('')}
                                        <span>(${product.rating})</span>
                                    </div>
                                    <div class="product-price-stock">
                                        <span class="price">${product.price.toLocaleString()} Ft</span>
                                        <span class="stock ${product.stock > 0 ? 'in-stock' : 'out-of-stock'}">
                                            ${product.stock > 0 ? product.stock + 'db raktáron' : 'Nincs raktáron'}
                                        </span>
                                    </div>
                                    <button class="add-to-cart-btn" ${product.stock <= 0 ? 'disabled' : ''} data-product-id="${product.id}">
                                        <i class='bx bx-cart-add'></i> Kosárba
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('');

                    // Kosárba gomb eseménykezelők
                    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            addToCart(this.dataset.productId);
                        });
                    });
                }

                // Kosár számláló frissítése
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cartCount;
                }
            })
            .catch(err => {
                productGrid.innerHTML = `<p class="error">Hiba történt: ${err}</p>`;
            });
    }

    // ===== SZŰRŐ OPCIÓK FRISSÍTÉSE (MÁRKÁK ÉS KATEGÓRIÁK) =====
    function updateFilterOptions(containerId, items, inputName) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = items.map(item => `
            <label class="checkbox-label">
                <input type="checkbox" name="${inputName}" value="${item.id}">
                ${item.name}
            </label>
        `).join('');
    }

    // ===== KOSÁRBA ADÁS =====
    function addToCart(productId) {
        const formData = new FormData();
        formData.append('product_id', productId);

        fetch(API_URL + 'cart-add.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadProducts(); // Kosár számláló frissítése
            } else {
                console.error(data.error || 'Hiba történt');
            }
        })
        .catch(err => console.error('Hiba: ' + err));
    }

    // ===== SZŰRÉS GOMBNYOMÁSRA =====
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        loadProducts();
    });

    // Kezdeti termékek betöltése
    loadProducts();

    // ===== MOBIL MENÜ TOGGLE =====
    const menuBtn = document.querySelector('.menu-btn');
    const menu = document.querySelector('.menu');

    if (menuBtn && menu) {
        menuBtn.addEventListener('click', function() {
            menu.classList.toggle('show');
            this.classList.toggle('active');
        });
    }
});