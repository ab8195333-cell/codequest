@extends('layouts.app')

@push('css')
    <style>
        .hover-shadow {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4" dir="rtl">
        <div class="row g-4">

            {{-- رأس الصفحة --}}
            <div class="mb-2 text-center text-md-start">
                <h2 class="fw-bold text-primary mb-1">
                    <i class="bi bi-tags-fill me-2"></i> التصنيفات والوسوم
                </h2>
                <p class="text-muted small">استكشف المواضيع والوسوم المتاحة وابحث عنها بسرعة للوصول للأسئلة المرتبطة بها.</p>
            </div>

            {{-- شريط البحث البسيط (search) --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body p-3">
                    <form action="{{ route('tags&categories') }}" method="GET" class="m-0">
                        <div class="input-group input-group-lg bg-white rounded-3 overflow-hidden shadow-sm border">
                            <span class="input-group-text bg-white border-0 text-muted pe-3 ps-0">
                                <i class="bi bi-search fs-5"></i>
                            </span>
                            <input type="text" name="search" class="form-control bg-white border-0 py-2 fs-6"
                                placeholder="ابحث عن تصنيف أو وسم بالاسم أو الوصف..." value="{{ request('search') }}">
                            <button class="btn btn-primary px-4 fw-bold fs-6" type="submit">
                                بحث
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- قسم التصنيفات الرئيسية --}}
            <div class="mb-4">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-folder2-open text-primary me-1"></i> التصنيفات الرئيسية
                </h5>
                <div class="row g-3">
                    @forelse ($categories as $category)
                        <div class="col-12 col-md-6 col-lg-4">
                            <a href="{{ route('home', ['category' => $category->name]) }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm rounded-4 hover-shadow p-3 transition">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="fs-2 text-primary flex-shrink-0 bg-primary-subtle px-3 py-2 rounded-3">
                                            <i class="bi bi-folder-fill"></i>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <h6 class="fw-bold text-dark mb-0 text-truncate fs-6">{{ $category->name }}
                                                </h6>
                                                <span
                                                    class="badge bg-light text-secondary border rounded-pill flex-shrink-0 ms-1">{{ $category->questions_count ?? 0 }}
                                                    سؤال</span>
                                            </div>
                                            <p class="text-muted small mb-0 line-clamp-2">
                                                {{ $category->description ?? 'لا يوجد وصف محدد لهذا التصنيف حالياً.' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                                <p class="text-muted small mb-0">لا توجد تصنيفات مطابقة للبحث.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <hr class="my-3">

            {{-- قسم عرض الوسوم (Tags) --}}
            <div>
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-hash text-primary me-1"></i> الوسوم (Tags)
                </h5>

                <div class="row g-3">
                    @forelse ($tags as $tag)
                        <div class="col-12 col-md-6 col-lg-4">
                            <a href="{{ route('home', ['tags' => json_encode([['id' => $tag->id ?? $tag->tag_id, 'value' => $tag->name]])]) }}"
                                class="text-decoration-none">
                                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 hover-shadow transition">
                                    <div class="d-flex align-items-start justify-content-between mb-2">
                                        <span
                                            class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 rounded-pill px-3 py-1.5">
                                            #{{ $tag->name }}
                                        </span>
                                        <span
                                            class="text-muted small fs-7 border px-2 py-0.5 rounded-pill bg-light">{{ $tag->questions_count ?? 0 }}
                                            سؤال</span>
                                    </div>
                                    <p class="text-muted small mb-0 line-clamp-2">
                                        {{ $tag->description ?? 'وسم مستخدم لتصنيف الأسئلة التقنية ذات الصلة.' }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                                <i class="bi bi-tags fs-2 text-muted mb-2"></i>
                                <h6 class="fw-bold text-secondary">لا توجد وسوم مطابقة للبحث</h6>
                                <p class="text-muted small mb-0">جرب البحث بكلمات أخرى.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- الترقيم (Pagination) --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $tags->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>
@endsection
