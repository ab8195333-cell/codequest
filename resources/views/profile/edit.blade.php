@extends('layouts.app')

@section('content')
<div class="container py-4" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h2 class="fw-bold mb-1">تعديل الملف الشخصي</h2>
            <p class="text-muted small mb-0">المعلومات العامة والبيانات التي ستظهر للآخرين</p>
        </div>
        <a href="{{ route('profile') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right ms-1"></i> العودة للملف الشخصي
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill ms-2"></i> {{ session('success') }}
            <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-2">المعلومات العامة</h5>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">صورة الملف الشخصي</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative">
                                    <img id="avatarPreview"
                                         src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://api.dicebear.com/7.x/identicon/svg?seed=' . auth()->user()->name }}"
                                         alt="الصورة الشخصية"
                                         class="rounded-3 border object-fit-cover"
                                         width="90" height="90">
                                </div>
                                <div>
                                    <label for="avatarInput" class="btn btn-sm btn-primary mb-2">
                                        <i class="bi bi-upload ms-1"></i> تغيير الصورة
                                    </label>
                                    <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*" onchange="previewImage(event)">
                                    <div class="form-text text-muted small">JPG أو PNG أو GIF (الحجم الأقصى: 2 ميجابايت)</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">اسم العرض (الاسم الشخصي)</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                   value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                   value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="location" class="form-label fw-semibold">الموقع / المدينة</label>
                                <input type="text" class="form-control" id="location" name="location"
                                       value="{{ old('location', auth()->user()->location ?? '') }}" placeholder="مثال: عدن، اليمن">
                            </div>
                            <div class="col-md-6">
                                <label for="title" class="form-label fw-semibold">المسمى الوظيفي</label>
                                <input type="text" class="form-control" id="title" name="title"
                                       value="{{ old('title', auth()->user()->title ?? '') }}" placeholder="مثال: مطور ويب / Full-Stack Developer">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="about_me" class="form-label fw-semibold">نبذة عني</label>
                            <textarea class="form-control font-monospace @error('about_me') is-invalid @enderror"
                                      id="about_me" name="about_me" rows="6"
                                      placeholder="اكتب نبذة عنك هنا... يدعم تنسيق Markdown">{{ old('about_me', auth()->user()->about_me ?? '') }}</textarea>
                            <div class="form-text text-muted small">يمكنك كتابة النصوص وتنسيقها بصيغة Markdown.</div>
                        </div>

                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-4">
                    <button type="reset" class="btn btn-light border">إلغاء</button>
                    <button type="submit" class="btn btn-success px-4">حفظ التغييرات</button>
                </div>
            </form>

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3 border-bottom pb-2 text-danger">
                        <i class="bi bi-shield-lock ms-1"></i> أمان الحساب
                    </h5>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 bg-light p-3 rounded-3 border">
                        <div>
                            <h6 class="fw-bold mb-1">تغيير كلمة المرور</h6>
                            <p class="text-muted small mb-0">للحفاظ على أمان حسابك، سيتم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.</p>
                        </div>
                        <form action="{{ route('password.email') }}" method="POST">
                            @csrf
                            <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                            <button type="submit" class="btn btn-outline-danger btn-sm text-nowrap">
                                <i class="bi bi-envelope-at ms-1"></i> إرسال رابط التغيير
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 20px;">
                <div class="card-header bg-light border-bottom">
                    <h6 class="fw-bold mb-0 text-muted"><i class="bi bi-eye ms-1"></i> المعاينة المباشرة</h6>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <img id="liveAvatar"
                             src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://api.dicebear.com/7.x/identicon/svg?seed='.auth()->user()->name }}"
                             class="rounded-circle border mb-2 object-fit-cover"
                             width="80" height="80">
                        <h5 id="liveName" class="fw-bold mb-0">{{ auth()->user()->name }}</h5>
                        <span id="liveTitle" class="badge bg-secondary-subtle text-secondary mt-1">
                            {{ auth()->user()->title ?? 'لا يوجد مسمى وظيفي' }}
                        </span>
                    </div>
                    <hr>
                    <div class="small text-muted mb-2">
                        <i class="bi bi-geo-alt ms-1"></i> <span id="liveLocation">{{ auth()->user()->location ?? 'غير محدد' }}</span>
                    </div>
                    <div class="mt-3">
                        <h6 class="fw-semibold text-dark small mb-1">النبذة الشخصية:</h6>
                        <div id="liveAbout" class="p-3 bg-light rounded text-break small text-secondary" style="min-height: 80px;">
                            {{ auth()->user()->about_me ?? 'لم يتم إضافة نبذة بعد...' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // معاينة الصورة مباشرة عند اختيارها
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('avatarPreview');
            const liveAvatar = document.getElementById('liveAvatar');
            output.src = reader.result;
            liveAvatar.src = reader.result;
        };
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // المعاينة اللحظية للنصوص أثناء الكتابة
    document.getElementById('name').addEventListener('input', e => {
        document.getElementById('liveName').innerText = e.target.value || 'اسم المستخدم';
    });

    document.getElementById('title').addEventListener('input', e => {
        document.getElementById('liveTitle').innerText = e.target.value || 'لا يوجد مسمى وظيفي';
    });

    document.getElementById('location').addEventListener('input', e => {
        document.getElementById('liveLocation').innerText = e.target.value || 'غير محدد';
    });

    document.getElementById('about_me').addEventListener('input', e => {
        document.getElementById('liveAbout').innerText = e.target.value || 'لم يتم إضافة نبذة بعد...';
    });
</script>
@endpush