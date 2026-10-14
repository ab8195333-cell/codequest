@extends('layouts.app')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    <style>
        .btn-white {
            background-color: azure !important;
            color: #0d6efd !important;
        }

        .tagify {
            border-radius: 0.5rem;
            background-color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4" dir="rtl">
        <div class="row g-4">

            {{-- شريط التصفية والفرز بأسلوب Stack Overflow --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">

                        {{-- الكارد الداخلي المتضمّن للبحث وأزرار الفرز --}}
                        <div class="bg-light p-2 rounded-4 border d-flex align-items-center gap-2 flex-grow-1"
                            style="max-width: 800px;">

                            {{-- نموذج البحث النصي --}}
                            <div class="flex-grow-1">
                                <form action="{{ route('home') }}" method="GET" class="m-0">
                                    @csrf
                                    @if (request('sort'))
                                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                                    @endif
                                    @if (request('category'))
                                        <input type="hidden" name="category" value="{{ request('category') }}">
                                    @endif
                                    @if (request('tags'))
                                        <input type="hidden" name="tags" value="{{ $currentTags }}">
                                    @endif

                                    <div
                                        class="input-group input-group-lg bg-white rounded-3 overflow-hidden shadow-sm border-0">
                                        <span class="input-group-text bg-white border-0 text-muted pe-3 ps-0">
                                            <i class="bi bi-search fs-5"></i>
                                        </span>
                                        <input type="text" name="search"
                                            class="form-control bg-white border-0 py-2 fs-6"
                                            placeholder="ابحث عن سؤال أو تقنية..." value="{{ request('search') }}">
                                        <button class="btn btn-primary px-4 fw-bold fs-6" type="submit">
                                            بحث
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- أزرار الفرز السريعة --}}
                            <div class="flex-shrink-0">
                                <div class="btn-group rounded-pill p-1 bg-white border" role="group">
                                    <a href="{{ route('home', array_merge(request()->query(), ['sort' => 'latest', 'page' => 1])) }}"
                                        class="btn btn-sm rounded-pill py-1 px-3 text-nowrap {{ request('sort', 'latest') == 'latest' ? 'btn-light text-primary fw-bold shadow-sm' : 'text-secondary border-0' }}">
                                        الأحدث
                                    </a>
                                    <a href="{{ route('home', array_merge(request()->query(), ['sort' => 'popular', 'page' => 1])) }}"
                                        class="btn btn-sm rounded-pill py-1 px-3 text-nowrap {{ request('sort') == 'popular' ? 'btn-light text-primary fw-bold shadow-sm' : 'text-secondary border-0' }}">
                                        الأكثر مشاهدة
                                    </a>
                                    <a href="{{ route('home', array_merge(request()->query(), ['sort' => 'unanswered', 'page' => 1])) }}"
                                        class="btn btn-sm rounded-pill py-1 px-3 text-nowrap {{ request('sort') == 'unanswered' ? 'btn-light text-primary fw-bold shadow-sm' : 'text-secondary border-0' }}">
                                        بدون إجابات
                                    </a>
                                </div>
                            </div>

                        </div>

                        {{-- زر الفلترة المتقدمة --}}
                        <div class="flex-shrink-0">
                            <button class="btn btn-outline-primary rounded-pill px-3 py-2 text-nowrap fs-6" type="button"
                                data-bs-toggle="collapse" data-bs-target="#advancedFilter">
                                الفلترة والمتقدم <i class="bi bi-funnel me-1"></i>
                            </button>
                        </div>

                    </div>

                    {{-- صندوق الفلترة المتقدمة --}}
                    <div class="collapse {{ request()->hasAny(['category', 'tags', 'search']) ? 'show' : '' }} mt-3 pt-3 border-top"
                        id="advancedFilter">
                        <form action="{{ route('home') }}" method="GET">
                            @csrf
                            @if (request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif
                            @if (request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            <div class="row g-3">
                                {{-- التصفية حسب التصنيف --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">التصنيف</label>
                                    <select name="category" class="form-select form-select-sm rounded-3 py-2">
                                        <option value="">جميع التصنيفات</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->name }}"
                                                {{ request('category') == $category->name ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- التصفية حسب الوسوم المتعددة --}}
                                <div class="col-md-6 mb-3">
                                    <label for="tagsInput" class="form-label fw-bold text-secondary small">الوسوم</label>
                                    <input name="tags" id="tagsInput" class="form-control" value="{{ $currentTags }}">
                                    <div class="form-text">اختر حتى 5 وسوم لتصفية الأسئلة.</div>
                                </div>
                            </div>

                            {{-- أزرار التطبيق والإلغاء --}}
                            <div class="d-flex justify-content-end gap-2 mt-3 pt-2">
                                <a href="{{ route('home') }}" class="btn btn-light btn-sm rounded-pill px-3">إلغاء الفلاتر</a>
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">تطبيق الفلتر</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            {{-- قائمة عرض الأسئلة --}}
            @forelse ($questions as $question)
                <div class="card border-0 shadow-sm rounded-4 mb-3 transition-hover">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-3">

                            <a href="{{ route('profile',$question->user->id) }}"> <img src="{{ $question->user->avatar ? asset('storage/' . $question->user->avatar) : 'https://api.dicebear.com/7.x/identicon/svg?seed=' . $question->user->name }}"
                                alt="{{ $question->user->name }}" width="48" height="48"
                                class="rounded-circle object-fit-cover border flex-shrink-0"></a>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark fs-6">{{ $question->user->name }}</span>
                                    <small class="text-muted">
                                        <i class="bi bi-clock ms-1"></i>{{ $question->created_at->diffForHumans() }}
                                    </small>
                                </div>

                                <h5 class="fw-bold mb-2">
                                    <a href="{{ route('questions.show', $question->slug ?? $question) }}"
                                        class="text-decoration-none text-dark hover-primary">
                                        {{ $question->title }}
                                    </a>
                                </h5>

                                <p class="text-secondary small mb-3 text-truncate-2">
                                    {{ Str::limit(strip_tags($question->description), 150) }}
                                </p>

                                <div
                                    class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-top pt-3">
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        @if ($question->category)
                                            <a href="{{ route('home', ['category' => $question->category->name]) }}"
                                                class="badge bg-light text-dark border text-decoration-none ms-1">
                                                {{ $question->category->name }}
                                            </a>
                                        @endif
                                        @foreach ($question->tags as $tag)
                                            <a href="{{ route('home', array_merge(request()->query(), ['tags' => json_encode([['id' => $tag->tag_id, 'value' => $tag->name]]), 'page' => 1])) }}"
                                                class="badge bg-primary-subtle text-primary rounded-pill text-decoration-none">
                                                #{{ $tag->name }}
                                            </a>
                                        @endforeach
                                    </div>

                                    <div class="d-flex align-items-center gap-3 text-muted small me-auto">
                                        <span><i class="bi bi-chat-left-text ms-1"></i>{{ $question->answers_count }} إجابة</span>
                                        <span><i class="bi bi-eye ms-1"></i>{{ $question->views_count }} مشاهدة</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                    <div class="card-body">
                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                        <h5 class="fw-bold text-secondary">لا توجد أسئلة حالياً</h5>
                        <p class="text-muted small">كن أول من يطرح سؤالاً في منصة كود كويست (CodeQuest)!</p>
                        <a href="{{ route('ask') }}" class="btn btn-primary rounded-pill px-4">طرح سؤال جديد</a>
                    </div>
                </div>

            @endforelse

            {{-- الترقيم --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $questions->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script>
        var input = document.querySelector('#tagsInput');

        var tagify = new Tagify(input, {
            maxTags: 5,
            placeholder: "ابحث عن وسوم...",
            tagTextProp: 'value',
            valueField: 'id',
            whitelist: @json($tags->map(fn($tag) => ['id' => $tag->id ?? $tag->tag_id, 'value' => $tag->name])),
            dropdown: {
                maxItems: 10,
                enabled: 1,
                closeOnSelect: false
            }
        });
    </script>
@endpush