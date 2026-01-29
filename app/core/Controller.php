<?php
class Controller {

    // Hàm gọi Model
    public function model($model) {
        // SỬA: Tạo đường dẫn tuyệt đối chính xác
        // dirname(__DIR__) trả về thư mục 'app'
        // Nối thẳng vào '/models/' (Bỏ chữ /core đi)
        $path = dirname(__DIR__) . '/models/' . $model . '.php';

        // Kiểm tra file model có tồn tại không trước khi gọi
        if (file_exists($path)) {
            require_once $path;
            
            // Khởi tạo object Model
            return new $model();
        } else {
            die("Model $model không tồn tại tại: " . $path);
        }
    }

    // Hàm gọi View (Giao diện)
    public function view($view, $data = []) {
        // SỬA: Tương tự, bỏ chữ /core đi
        // Đường dẫn đúng là: app/views/...
        $path = dirname(__DIR__) . '/views/' . $view . '.php';

        // Kiểm tra file view có tồn tại không
        if (file_exists($path)) {
            
            // Hàm extract sẽ biến mảng thành biến
            extract($data);

            require_once $path;
        } else {
            die("View $view không tồn tại tại: " . $path);
        }
    }
}
?>