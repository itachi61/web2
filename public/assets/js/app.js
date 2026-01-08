// ===== SMOOTH PAGE TRANSITIONS =====
document.addEventListener('DOMContentLoaded', () => {
    // Add fade-in animation on page load
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.3s ease-in';
        document.body.style.opacity = '1';
    }, 10);

    // Smooth transitions for internal links
    const links = document.querySelectorAll('a[href^="/techsmart/public"]');
    links.forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (href && !href.includes('#')) {
                e.preventDefault();
                document.body.style.opacity = '0';
                setTimeout(() => {
                    window.location.href = href;
                }, 300);
            }
        });
    });
});

// ===== TOAST NOTIFICATION SYSTEM =====
class Toast {
    static show(message, type = 'success') {
        // Remove existing toasts
        const existingToast = document.querySelector('.toast');
        if (existingToast) existingToast.remove();

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
      <span class="toast-icon">${type === 'success' ? '✓' : '⚠'}</span>
      <span class="toast-message">${message}</span>
    `;

        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.add('toast-show'), 10);

        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.classList.remove('toast-show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    static success(message) {
        this.show(message, 'success');
    }

    static error(message) {
        this.show(message, 'error');
    }
}

// ===== ADD TO CART FUNCTIONALITY =====
function addToCart(productId, productName, stock) {
    // Check stock availability
    if (stock === 0) {
        Toast.error('Sản phẩm hiện đã hết hàng!');
        return;
    }

    // Simulate adding to cart (in real app, this would be an API call)
    Toast.success(`Đã thêm "${productName}" vào giỏ hàng!`);

    // Update cart count in header (if exists)
    updateCartCount();
}

// ===== UPDATE CART COUNT =====
function updateCartCount() {
    const cartBadge = document.querySelector('.cart-badge');
    if (cartBadge) {
        const currentCount = parseInt(cartBadge.textContent) || 0;
        cartBadge.textContent = currentCount + 1;
    }
}

// ===== FILTER FUNCTIONALITY =====
function setupFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const productCards = document.querySelectorAll('.product-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active state
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filter = btn.textContent.trim();

            // Filter products
            productCards.forEach(card => {
                const category = card.dataset.category;
                if (filter === 'Tất cả' || category === filter) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeIn 0.3s ease-in';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

// ===== SEARCH FUNCTIONALITY =====
function setupSearch() {
    const searchInput = document.querySelector('.search-input');
    const searchBtn = document.querySelector('.search-bar .btn');
    const productCards = document.querySelectorAll('.product-card');

    if (!searchInput) return;

    const performSearch = () => {
        const query = searchInput.value.toLowerCase().trim();
        let foundCount = 0;

        productCards.forEach(card => {
            const productName = card.querySelector('h3').textContent.toLowerCase();
            if (productName.includes(query) || query === '') {
                card.style.display = 'block';
                foundCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (foundCount === 0 && query !== '') {
            Toast.error('Không tìm thấy sản phẩm nào!');
        }
    };

    searchBtn?.addEventListener('click', performSearch);
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') performSearch();
    });
}

// ===== QUANTITY CONTROLS =====
function setupQuantityControls() {
    const qtyBtns = document.querySelectorAll('.qty-btn');

    qtyBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('.qty-input');
            const currentValue = parseInt(input.value) || 1;
            const max = parseInt(input.max) || 999;

            if (btn.textContent === '+' && currentValue < max) {
                input.value = currentValue + 1;
            } else if (btn.textContent === '-' && currentValue > 1) {
                input.value = currentValue - 1;
            } else if (btn.textContent === '+' && currentValue >= max) {
                Toast.error('Đã đạt số lượng tối đa!');
            }
        });
    });
}

// ===== INITIALIZE ON PAGE LOAD =====
document.addEventListener('DOMContentLoaded', () => {
    setupFilters();
    setupSearch();
    setupQuantityControls();
});
