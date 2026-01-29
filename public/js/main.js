// public/js/main.js

document.addEventListener("DOMContentLoaded", function() {

    // 1. Tự động ẩn thông báo (Alerts) sau 3 giây
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                // Sử dụng Bootstrap fade out nếu có class 'fade'
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500); // Xóa khỏi DOM sau khi mờ dần
            });
        }, 3000);
    }

    // 2. Xác nhận khi xóa (Sản phẩm/Giỏ hàng)
    // Áp dụng cho bất kỳ thẻ <a> hoặc <button> nào có class 'btn-delete'
    const deleteBtns = document.querySelectorAll('.btn-delete');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Bạn có chắc chắn muốn xóa mục này không?')) {
                e.preventDefault(); // Ngăn chặn hành động nếu user bấm Cancel
            }
        });
    });

    // 3. Validate form Số lượng (Không cho nhập số âm)
    const quantityInputs = document.querySelectorAll('input[type="number"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
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
        imgInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    }
});