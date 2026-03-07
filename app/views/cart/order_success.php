<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm border-0 rounded-4 p-5">
                <div class="mb-4">
                    <i class="fa-solid fa-circle-check text-success" style="font-size: 80px;"></i>
                </div>
                <h2 class="fw-bold text-success mb-3">Đặt hàng thành công!</h2>
                <p class="text-muted mb-2">Cảm ơn bạn đã mua hàng tại TechSmart.</p>
                <p class="mb-4">Mã đơn hàng của bạn: <span class="badge bg-primary fs-5 px-3 py-2">#<?= $data['orderId'] ?></span></p>
                
                <div class="alert alert-info text-start">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    Chúng tôi sẽ liên hệ xác nhận đơn hàng trong thời gian sớm nhất. 
                    Bạn có thể theo dõi trạng thái đơn hàng trong mục "Đơn hàng của tôi".
                </div>

                <div class="d-flex gap-3 justify-content-center mt-3">
                    <a href="<?= BASE_URL ?>" class="btn btn-primary btn-lg px-4">
                        <i class="fa-solid fa-house me-2"></i>Về trang chủ
                    </a>
                    <a href="<?= BASE_URL ?>" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="fa-solid fa-bag-shopping me-2"></i>Tiếp tục mua
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
