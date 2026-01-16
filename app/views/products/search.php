<h3 class="mb-4">Kết quả tìm kiếm cho: "<?= $data['keyword'] ?>"</h3>

<?php if (empty($data['products'])): ?>
    <div class="alert alert-warning">Không tìm thấy sản phẩm nào phù hợp.</div>
<?php else: ?>
    <div class="row">
        <?php foreach($data['products'] as $product): ?>
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                <div class="card product-card h-100 shadow-sm border-0">
                    <div class="position-relative text-center p-3">
                        <a href="<?= BASE_URL ?>product/detail/<?= $product['id'] ?>">
                            <img src="<?= BASE_URL . 'img/' . $product['image'] ?>" 
                                 class="card-img-top img-fluid" style="height: 180px; object-fit: contain;"
                                 onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                        </a>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title"><a href="<?= BASE_URL ?>product/detail/<?= $product['id'] ?>" class="text-decoration-none text-dark"><?= $product['name'] ?></a></h6>
                        <span class="text-danger fw-bold"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>