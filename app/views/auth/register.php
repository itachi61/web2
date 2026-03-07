<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-success text-white text-center py-3">
                <h4 class="font-weight-light my-2">Đăng Ký Tài Khoản</h4>
            </div>
            <div class="card-body p-4">
                
                <?php if(isset($data['error'])): ?>
                    <div class="alert alert-danger"><?= $data['error'] ?></div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>auth/register" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input class="form-control py-2" type="text" name="fullname" placeholder="Nguyễn Văn A" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ Email <span class="text-danger">*</span></label>
                        <input class="form-control py-2" type="email" name="email" placeholder="name@example.com" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input class="form-control py-2" type="tel" name="phone" placeholder="0901234567" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ giao hàng mặc định <span class="text-danger">*</span></label>
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <select id="regProvince" class="form-select form-select-sm">
                                    <option value="">Tỉnh/Thành phố</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select id="regDistrict" class="form-select form-select-sm" disabled>
                                    <option value="">Quận/Huyện</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select id="regWard" class="form-select form-select-sm" disabled>
                                    <option value="">Phường/Xã</option>
                                </select>
                            </div>
                        </div>
                        <input type="text" id="regStreet" class="form-control py-2" placeholder="Số nhà, tên đường (VD: 123 Nguyễn Huệ)">
                        <input type="hidden" name="address" id="regAddrField" required>
                        <small class="text-muted"><i class="fa-solid fa-info-circle me-1"></i>Địa chỉ này sẽ được sử dụng làm mặc định khi đặt hàng</small>
                    </div>
                    <script>
                    (function(){
                        const API='https://provinces.open-api.vn/api/';
                        const p=document.getElementById('regProvince'), d=document.getElementById('regDistrict'), w=document.getElementById('regWard'), s=document.getElementById('regStreet'), f=document.getElementById('regAddrField');
                        function build(){const pts=[s.value,w.selectedOptions[0]?.text,d.selectedOptions[0]?.text,p.selectedOptions[0]?.text].filter(x=>x&&!x.includes('Tỉnh')&&!x.includes('Quận')&&!x.includes('Phường'));f.value=pts.join(', ');}
                        fetch(API+'?depth=1').then(r=>r.json()).then(data=>{data.sort((a,b)=>a.name.localeCompare(b.name));data.forEach(i=>{p.add(new Option(i.name,i.code));});});
                        p.onchange=function(){d.innerHTML='<option value="">Quận/Huyện</option>';w.innerHTML='<option value="">Phường/Xã</option>';d.disabled=true;w.disabled=true;if(!this.value)return;fetch(API+'p/'+this.value+'?depth=2').then(r=>r.json()).then(data=>{data.districts.forEach(i=>d.add(new Option(i.name,i.code)));d.disabled=false;});build();};
                        d.onchange=function(){w.innerHTML='<option value="">Phường/Xã</option>';w.disabled=true;if(!this.value)return;fetch(API+'d/'+this.value+'?depth=2').then(r=>r.json()).then(data=>{data.wards.forEach(i=>w.add(new Option(i.name,i.code)));w.disabled=false;});build();};
                        w.onchange=build; s.addEventListener('input',build);
                    })();
                    </script>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input class="form-control py-2" type="password" name="password" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nhập lại mật khẩu <span class="text-danger">*</span></label>
                            <input class="form-control py-2" type="password" name="confirm_password" required />
                        </div>
                    </div>
                    <div class="d-grid mt-4">
                        <button class="btn btn-success btn-lg" type="submit">Tạo tài khoản</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3">
                <div class="small">
                    <a href="<?= BASE_URL ?>auth/login">Đã có tài khoản? Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>