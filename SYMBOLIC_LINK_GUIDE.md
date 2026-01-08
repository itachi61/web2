# Hướng dẫn tạo Symbolic Link cho TechSmart

## Bước 1: Mở Command Prompt với quyền Administrator
1. Nhấn `Windows + X`
2. Chọn "Terminal (Admin)" hoặc "Command Prompt (Admin)"

## Bước 2: Chạy lệnh tạo symbolic link

```cmd
mklink /D "C:\xampp\htdocs\techsmart" "C:\Users\ACER\Desktop\techsmart"
```

**Giải thích:**
- `/D` - Tạo directory symbolic link
- `"C:\xampp\htdocs\techsmart"` - Link trong htdocs (Apache sẽ đọc từ đây)
- `"C:\Users\ACER\Desktop\techsmart"` - Project thật ở Desktop

## Bước 3: Kiểm tra

Sau khi chạy lệnh, bạn sẽ thấy:
```
symbolic link created for C:\xampp\htdocs\techsmart <<===>> C:\Users\ACER\Desktop\techsmart
```

## Bước 4: Truy cập website

Bây giờ bạn có thể truy cập:
- `http://localhost/techsmart/public/`
- `http://localhost/techsmart/public/debug.php`

## ✅ Lợi ích:

1. **Project vẫn ở Desktop** - Dễ quản lý, dễ backup
2. **Apache vẫn truy cập được** - Qua symbolic link
3. **Dễ push lên GitHub** - Vì project ở Desktop, không lẫn với các project khác trong htdocs
4. **Không cần copy qua copy lại** - Mọi thay đổi đều tự động sync

## 🚨 Lưu ý:

- Phải chạy Command Prompt **với quyền Administrator**
- Nếu muốn xóa link: `rmdir "C:\xampp\htdocs\techsmart"` (chỉ xóa link, không xóa project thật)

## 🔄 Nếu lỗi "You do not have sufficient privilege"

Nghĩa là bạn chưa mở Command Prompt với quyền Admin. Hãy:
1. Tìm "cmd" trong Start Menu
2. Click phải → "Run as administrator"
3. Chạy lại lệnh mklink

---

## 📝 Alternative: Nếu không muốn dùng symbolic link

Bạn có thể config Virtual Host trong Apache, nhưng phức tạp hơn. Symbolic link là cách đơn giản nhất!
