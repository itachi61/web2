<div class="flex flex-col md:flex-row gap-8 justify-center py-8">
    <!-- ĐĂNG NHẬP -->
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-indigo-600">Đăng nhập</h2>
        <form action="actions/process.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tên đăng nhập</label>
                <input type="text" name="username" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-300 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Mật khẩu</label>
                <input type="password" name="password" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-300 outline-none">
            </div>
            <button type="submit" name="login" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 transition">Đăng nhập</button>
        </form>
    </div>

    <!-- ĐĂNG KÝ -->
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-green-600">Đăng ký mới</h2>
        <form action="actions/process.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Họ tên đầy đủ</label>
                <input type="text" name="fullname" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-green-300 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tên đăng nhập</label>
                <input type="text" name="username" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-green-300 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Mật khẩu</label>
                <input type="password" name="password" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-green-300 outline-none">
            </div>
            <button type="submit" name="register" class="w-full bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 transition">Đăng ký</button>
        </form>
    </div>
</div>