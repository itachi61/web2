// public/js/main.js

document.addEventListener("DOMContentLoaded", function () {

    // === DARK MODE TOGGLE ===
    const darkBtn = document.getElementById('darkModeToggle');
    const htmlEl = document.documentElement;

    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        htmlEl.setAttribute('data-bs-theme', 'dark');
        if (darkBtn) darkBtn.innerHTML = '<i class="fa-solid fa-sun"></i>';
    }

    if (darkBtn) {
        darkBtn.addEventListener('click', function () {
            const isDark = htmlEl.getAttribute('data-bs-theme') === 'dark';
            if (isDark) {
                htmlEl.removeAttribute('data-bs-theme');
                darkBtn.innerHTML = '<i class="fa-solid fa-moon"></i>';
                localStorage.setItem('theme', 'light');
            } else {
                htmlEl.setAttribute('data-bs-theme', 'dark');
                darkBtn.innerHTML = '<i class="fa-solid fa-sun"></i>';
                localStorage.setItem('theme', 'dark');
            }
        });
    }

    // === LANGUAGE TRANSLATION SYSTEM ===
    const translations = {
        // Navbar
        'Trang chủ': 'Home',
        'Laptop': 'Laptop',
        'Điện thoại': 'Phones',
        'Linh kiện': 'Components',
        'Linh Kiện': 'Components',
        'Sản phẩm kỹ thuật số': 'Digital Products',
        'Danh mục': 'Categories',
        'Xem tất cả': 'View All',
        'Bạn tìm gì...': 'Search...',
        'Đăng nhập': 'Login',
        'Đăng xuất': 'Logout',
        'Trang quản trị': 'Admin Panel',
        'Đơn hàng của tôi': 'My Orders',
        'Thông tin cá nhân': 'My Profile',

        // Banner Carousel
        'Công Nghệ Trong Tầm Tay': 'Technology At Your Fingertips',
        'Khám phá những sản phẩm mới nhất với mức giá ưu đãi chưa từng có': 'Discover the latest products at unprecedented prices',
        'Khám phá những sản phẩm mới nhất với mức giá ưu đãi chưa từng có.': 'Discover the latest products at unprecedented prices.',
        'Mua sắm ngay': 'Shop Now',
        'Flash Sale Cuối Tuần': 'Weekend Flash Sale',
        'Xem ngay': 'View Now',
        'Bảo Hành Chính Hãng': 'Authentic Warranty',
        'Cam kết 100% sản phẩm chính hãng, bảo hành toàn quốc, đổi trả 30 ngày': '100% authentic products, nationwide warranty, 30-day returns',
        'Khám phá': 'Explore',

        // Homepage
        'Sản phẩm nổi bật': 'Featured Products',
        'Thêm vào giỏ': 'Add to Cart',
        'Đã bán': 'Sold',

        // Category / Product
        'Tất cả sản phẩm': 'All Products',
        'Sắp xếp:': 'Sort:',
        'Mới nhất': 'Newest',
        'Giá: Thấp đến Cao': 'Price: Low to High',
        'Giá: Cao đến Thấp': 'Price: High to Low',
        'Tìm thấy': 'Found',
        'sản phẩm': 'products',
        'Không tìm thấy sản phẩm nào': 'No products found',
        'Quay lại trang chủ': 'Back to Home',

        // Product detail
        'Đánh giá sản phẩm': 'Product Reviews',
        'Gửi đánh giá': 'Submit Review',
        'Mô tả sản phẩm': 'Product Description',

        // Cart
        'GIỎ HÀNG CỦA BẠN': 'YOUR CART',
        'Giỏ hàng của bạn': 'Your Cart',
        'Sản phẩm': 'Product',
        'Đơn giá': 'Unit Price',
        'Số lượng': 'Quantity',
        'Thành tiền': 'Subtotal',
        'Xóa': 'Remove',
        'Cập nhật giỏ hàng': 'Update Cart',
        'Cộng giỏ hàng': 'Cart Total',
        'Tạm tính:': 'Subtotal:',
        'Phí vận chuyển:': 'Shipping:',
        'Miễn phí': 'Free',
        'Tổng cộng:': 'Total:',
        'Tiến hành thanh toán': 'Proceed to Checkout',
        'Đăng nhập để thanh toán': 'Login to Checkout',
        'Tiếp tục xem sản phẩm': 'Continue Shopping',
        'Giỏ hàng đang trống!': 'Your cart is empty!',
        'Bạn chưa thêm sản phẩm nào vào giỏ hàng.': 'You haven\'t added any products to your cart.',
        'Tiếp tục mua sắm': 'Continue Shopping',

        // Footer
        'VỀ TECHSMART': 'ABOUT TECHSMART',
        'Về TechSmart': 'About TechSmart',
        'HỖ TRỢ KHÁCH HÀNG': 'CUSTOMER SUPPORT',
        'Hỗ trợ khách hàng': 'Customer Support',
        'LIÊN HỆ': 'CONTACT',
        'Liên hệ': 'Contact',
        'Hướng dẫn mua hàng': 'Shopping Guide',
        'Chính sách bảo hành': 'Warranty Policy',
        'Vận chuyển & Giao nhận': 'Shipping & Delivery',
        'Phương thức thanh toán': 'Payment Methods',
        'Hệ thống bán lẻ công nghệ uy tín hàng đầu. Cam kết sản phẩm chính hãng, bảo hành trọn đời, hỗ trợ 24/7.': 'Leading technology retail system. 100% authentic products, lifetime warranty, 24/7 support.',

        // Checkout
        'Thanh toán': 'Checkout',
        'Thông tin giao hàng': 'Shipping Information',
        'Họ và tên người nhận': 'Recipient Name',
        'Số điện thoại': 'Phone Number',
        'Địa chỉ giao hàng': 'Shipping Address',
        'Ghi chú': 'Notes',
        'Phương thức thanh toán': 'Payment Methods',
        'Thanh toán khi nhận hàng (COD)': 'Cash on Delivery (COD)',
        'Chuyển khoản ngân hàng': 'Bank Transfer',
        'Đơn hàng của bạn': 'Your Order',
        'Xác nhận đặt hàng': 'Place Order',
        'Quay lại giỏ hàng': 'Back to Cart',

        // Auth
        'Đăng Ký Tài Khoản': 'Create Account',
        'Họ và tên': 'Full Name',
        'Địa chỉ Email': 'Email Address',
        'Mật khẩu': 'Password',
        'Nhập lại mật khẩu': 'Confirm Password',
        'Tạo tài khoản': 'Sign Up',
        'Đã có tài khoản? Đăng nhập': 'Already have an account? Login',
        'Địa chỉ giao hàng mặc định': 'Default Shipping Address',

        // Toast
        'Đã thêm vào giỏ hàng!': 'Added to cart!',
    };

    // Build reverse dictionary (EN -> VI)
    const reverseTranslations = {};
    for (const [vi, en] of Object.entries(translations)) {
        reverseTranslations[en] = vi;
    }

    function translatePage(lang) {
        const dict = lang === 'EN' ? translations : reverseTranslations;

        // Translate all elements with data-i18n attribute
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            if (dict[key]) {
                el.textContent = dict[key];
                el.setAttribute('data-i18n', dict[key]);
            }
        });

        // Auto-translate common text nodes
        const textWalker = function (root) {
            const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null, false);
            const textNodes = [];
            while (walker.nextNode()) textNodes.push(walker.currentNode);
            textNodes.forEach(node => {
                const trimmed = node.textContent.trim();
                if (trimmed && dict[trimmed]) {
                    node.textContent = node.textContent.replace(trimmed, dict[trimmed]);
                }
            });
        };

        // Translate nav links, buttons, headings, labels, spans, etc.
        document.querySelectorAll('.nav-link, .btn, h1, h2, h3, h4, h5, h6, label, th, td span, .dropdown-item, p, small, span, a, .breadcrumb-item').forEach(el => {
            // Skip elements with many children (complex elements)
            if (el.children.length > 2) return;

            // Handle elements with icon + text
            const textContent = el.textContent.trim();
            for (const [from, to] of Object.entries(dict)) {
                if (textContent === from || textContent.endsWith(from)) {
                    // Preserve icons (i tags)
                    const icon = el.querySelector('i, img');
                    if (icon) {
                        const iconHtml = icon.outerHTML;
                        el.innerHTML = iconHtml + ' ' + to;
                    } else {
                        el.textContent = to;
                    }
                    break;
                }
            }
        });

        // Translate placeholders
        document.querySelectorAll('input[placeholder]').forEach(input => {
            const ph = input.placeholder.trim();
            if (dict[ph]) input.placeholder = dict[ph];
        });

        // Translate select options
        document.querySelectorAll('select option').forEach(opt => {
            const text = opt.textContent.trim();
            if (dict[text]) opt.textContent = dict[text];
        });

        // Translate "Đã bán X" patterns
        document.querySelectorAll('small').forEach(el => {
            const text = el.textContent.trim();
            if (lang === 'EN') {
                const match = text.match(/Đã bán (\d+)/);
                if (match) el.innerHTML = el.innerHTML.replace('Đã bán ' + match[1], 'Sold ' + match[1]);
            } else {
                const match = text.match(/Sold (\d+)/);
                if (match) el.innerHTML = el.innerHTML.replace('Sold ' + match[1], 'Đã bán ' + match[1]);
            }
        });

        // Update page title
        const title = document.title;
        if (lang === 'EN' && title.includes('Trang chủ')) {
            document.title = title.replace('Trang chủ', 'Home');
        } else if (lang === 'VI' && title.includes('Home')) {
            document.title = title.replace('Home', 'Trang chủ');
        }
    }

    const savedLang = localStorage.getItem('lang') || 'VI';
    const langLabel = document.getElementById('currentLang');
    if (langLabel) langLabel.textContent = savedLang;

    // Apply saved language on load
    if (savedLang === 'EN') {
        translatePage('EN');
    }

    document.querySelectorAll('.lang-option').forEach(function (item) {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const lang = this.dataset.lang;
            localStorage.setItem('lang', lang);
            if (langLabel) langLabel.textContent = lang;
            translatePage(lang);
        });
    });

    // === AJAX ADD TO CART ===
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-add-cart');
        if (!btn) return;

        e.preventDefault();
        const productId = btn.dataset.productId;
        if (!productId) return;

        // Determine base URL from the link href
        const baseUrl = btn.getAttribute('data-base-url') || '/';

        fetch(baseUrl + 'cart/ajaxAdd/' + productId)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update cart badge
                    const badge = document.getElementById('cart-badge');
                    if (badge) {
                        badge.textContent = data.cartCount;
                        badge.style.display = 'inline-block';

                        // Bounce animation
                        badge.classList.add('cart-badge-bounce');
                        setTimeout(() => badge.classList.remove('cart-badge-bounce'), 400);
                    }

                    // Show toast notification
                    showCartToast(data.message);
                } else {
                    if (data.needLogin) {
                        window.location.href = baseUrl + 'auth/login';
                    } else {
                        showCartToast(data.message || 'Có lỗi xảy ra');
                    }
                }
            })
            .catch(err => {
                console.error('Cart AJAX error:', err);
                // Fallback: redirect
                window.location.href = baseUrl + 'cart/add/' + productId;
            });
    });

    // Toast notification function
    function showCartToast(message) {
        // Remove existing toast
        const existing = document.getElementById('cart-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'cart-toast';
        toast.innerHTML = '<i class="fa-solid fa-check-circle me-2"></i>' + message;
        toast.style.cssText = 'position:fixed;top:80px;right:20px;z-index:9999;background:#198754;color:#fff;padding:12px 20px;border-radius:8px;font-size:14px;font-weight:500;box-shadow:0 4px 12px rgba(0,0,0,0.2);animation:slideInRight 0.3s ease;display:flex;align-items:center;';
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }

    // === CART DELETE INLINE CONFIRM ===
    document.addEventListener('click', function (e) {
        // Click nút thùng rác → hiện confirm
        const initBtn = e.target.closest('.btn-cart-delete-init');
        if (initBtn) {
            const wrap = initBtn.closest('.cart-delete-wrap');
            initBtn.classList.add('d-none');
            wrap.querySelector('.cart-delete-confirm').classList.remove('d-none');
            return;
        }
        // Click nút hủy → ẩn confirm
        const cancelBtn = e.target.closest('.btn-cart-delete-cancel');
        if (cancelBtn) {
            const wrap = cancelBtn.closest('.cart-delete-wrap');
            wrap.querySelector('.cart-delete-confirm').classList.add('d-none');
            wrap.querySelector('.btn-cart-delete-init').classList.remove('d-none');
            return;
        }
    });

    // 1. Tự động ẩn thông báo (Alerts) sau 3 giây — chỉ ẩn alert-dismissible
    const alerts = document.querySelectorAll('.alert.alert-dismissible');
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);
    }

    // 2. Xác nhận khi xóa (Sản phẩm/Giỏ hàng)
    // Áp dụng cho bất kỳ thẻ <a> hoặc <button> nào có class 'btn-delete'
    const deleteBtns = document.querySelectorAll('.btn-delete');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('Bạn có chắc chắn muốn xóa mục này không?')) {
                e.preventDefault(); // Ngăn chặn hành động nếu user bấm Cancel
            }
        });
    });

    // 3. Validate form Số lượng (Không cho nhập số âm)
    const quantityInputs = document.querySelectorAll('input[type="number"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function () {
            if (this.value < 1) {
                alert('Số lượng phải ít nhất là 1');
                this.value = 1;
            }
        });
    });

    // 4. Preview ảnh trước khi upload (Dành cho trang Admin thêm sản phẩm)
    const imgInput = document.getElementById('imageUpload');
    const imgPreview = document.getElementById('imagePreview');

    if (imgInput && imgPreview) {
        imgInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // 5. SMOOTH PAGINATION - AJAX Page Transitions
    // TEMPORARILY DISABLED - Causing infinite loading
    /*
    const paginationControls = document.getElementById('pagination-controls');

    if (paginationControls) {
        paginationControls.addEventListener('click', function (e) {
            // Check if clicked element is a pagination link
            const link = e.target.closest('a.page-link');
            if (!link) return;

            // Prevent default navigation
            e.preventDefault();

            // Get page number from data attribute
            const page = link.getAttribute('data-page');
            if (!page) return;

            // Get current URL and update page parameter
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);

            // Fetch new page content via AJAX
            loadPageContent(url.toString());
        });
    }
    */

    function loadPageContent(url) {
        const productGrid = document.getElementById('product-grid');
        const paginationContainer = document.querySelector('nav[aria-label="Product pagination"]');

        if (!productGrid) return;

        // Add loading state
        productGrid.style.opacity = '0.5';
        productGrid.style.pointerEvents = 'none';

        // Fetch new content with cache disabled
        fetch(url, {
            method: 'GET',
            cache: 'no-store',  // Disable cache
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                // Parse the HTML response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Extract new product grid
                const newProductGrid = doc.getElementById('product-grid');
                const newPagination = doc.querySelector('nav[aria-label="Product pagination"]');

                if (newProductGrid) {
                    // Fade out animation
                    productGrid.style.transition = 'opacity 0.3s ease';
                    productGrid.style.opacity = '0';

                    setTimeout(() => {
                        // Replace content
                        productGrid.innerHTML = newProductGrid.innerHTML;

                        // Update pagination if exists
                        if (paginationContainer && newPagination) {
                            paginationContainer.innerHTML = newPagination.innerHTML;
                        }

                        // Fade in animation
                        productGrid.style.opacity = '1';
                        productGrid.style.pointerEvents = 'auto';

                        // Scroll to top of product grid smoothly
                        const productsListHeader = document.getElementById('products-list');
                        if (productsListHeader) {
                            productsListHeader.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        } else {
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }

                        // Update browser URL without reload
                        window.history.pushState({ page: url }, '', url);
                    }, 300);
                } else {
                    // If product grid not found, do a full page reload
                    console.warn('Product grid not found in response, reloading page');
                    window.location.href = url;
                }
            })
            .catch(error => {
                console.error('Error loading page:', error);
                // Restore state on error
                productGrid.style.opacity = '1';
                productGrid.style.pointerEvents = 'auto';
                alert('Không thể tải trang. Vui lòng thử lại.');
            });
    }

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function (e) {
        if (e.state && e.state.page) {
            loadPageContent(e.state.page);
        } else {
            location.reload();
        }
    });
});