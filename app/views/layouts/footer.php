</div> 
<footer class="footer-custom pt-5 pb-3">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5><?= __('about_us', 'Về TechSmart') ?></h5>
                <p class="small opacity-75">
                    <?php if (getCurrentLang() === 'en'): ?>
                        Leading technology retail system. Committed to genuine products, lifetime warranty, 24/7 support.
                    <?php else: ?>
                        Hệ thống bán lẻ công nghệ uy tín hàng đầu. Cam kết sản phẩm chính hãng, bảo hành trọn đời, hỗ trợ 24/7.
                    <?php endif; ?>
                </p>
                <div class="mt-3">
                    <a href="#" class="me-3 fs-5"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="me-3 fs-5"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#" class="me-3 fs-5"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <h5><?= __('support', 'Hỗ trợ khách hàng') ?></h5>
                <ul class="list-unstyled small">
                    <?php if (getCurrentLang() === 'en'): ?>
                        <li class="mb-2"><a href="#">Shopping Guide</a></li>
                        <li class="mb-2"><a href="#">Warranty Policy</a></li>
                        <li class="mb-2"><a href="#">Shipping & Delivery</a></li>
                        <li class="mb-2"><a href="#">Payment Methods</a></li>
                    <?php else: ?>
                        <li class="mb-2"><a href="#">Hướng dẫn mua hàng</a></li>
                        <li class="mb-2"><a href="#">Chính sách bảo hành</a></li>
                        <li class="mb-2"><a href="#">Vận chuyển & Giao nhận</a></li>
                        <li class="mb-2"><a href="#">Phương thức thanh toán</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="col-md-4 mb-4">
                <h5><?= __('contact', 'Liên hệ') ?></h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="fa-solid fa-location-dot me-2"></i> Quận 1, TP. Hồ Chí Minh</li>
                    <li class="mb-2"><i class="fa-solid fa-phone me-2"></i> 1900 1234 567</li>
                    <li class="mb-2"><i class="fa-solid fa-envelope me-2"></i> support@techsmart.vn</li>
                </ul>
            </div>
        </div>
        
        <hr class="opacity-25">
        <div class="text-center small opacity-75">
            <p class="mb-0">&copy; <?= date('Y') ?> TechSmart. <?= __('copyright', 'All rights reserved.') ?></p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>js/theme.js?v=<?= time() ?>"></script>
<script src="<?= BASE_URL ?>js/main.js"></script>

<!-- Pass translations to JavaScript -->
<script>
    window.LANG = {
        dark_mode: '<?= __('dark_mode') ?>',
        light_mode: '<?= __('light_mode') ?>'
    };
</script>
</body>
</html>