<?php
namespace App\Core;

class Controller
{
  protected function view(string $path, array $data = [], string $layout = 'main'): void
  {
    // Biến dùng cho view/layout
    $viewFile = __DIR__ . '/../views/' . $path . '.php';
    $layoutFile = __DIR__ . '/../views/layouts/' . $layout . '.php';

    if (!file_exists($viewFile)) {
      throw new \RuntimeException("View not found: $path");
    }
    if (!file_exists($layoutFile)) {
      throw new \RuntimeException("Layout not found: $layout");
    }

    // Đưa data ra biến (title, products,...)
    extract($data, EXTR_SKIP);

    // Render layout (layout sẽ require $viewFile)
    require $layoutFile;
  }
}
