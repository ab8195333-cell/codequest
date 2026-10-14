@extends('layouts.app')

@push('css')
<style>
/* 1. الفضي اللامع (Silver Shiny) */
.badge-silver-shiny {
    background: linear-gradient(135deg, #e6e8fa 0%, #b0b5bc 25%, #ffffff 50%, #8d95a1 75%, #c0c0c0 100%);
    color: #1a1a1a !important;
    border: 1px solid #d1d5db;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15), inset 0 1px 1px rgba(255, 255, 255, 0.8);
    font-weight: 600;
}

/* 2. الذهبي اللامع (Gold Shiny) */
.badge-gold-shiny {
    background: linear-gradient(135deg, #ffe259 0%, #ffa751 25%, #ffffff 50%, #f4c430 75%, #e6ac00 100%);
    color: #2b1f00 !important;
    border: 1px solid #e0a900;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15), inset 0 1px 1px rgba(255, 255, 255, 0.8);
    font-weight: 600;
}

/* 3. البرونزي اللامع (Bronze Shiny) */
.badge-bronze-shiny {
    background: linear-gradient(135deg, #f7c59f 0%, #cd7f32 25%, #ffffff 50%, #a05a2c 75%, #8c5007 100%);
    color: black !important;
    border: 1px solid #8c5007;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15), inset 0 1px 1px rgba(255, 255, 255, 0.6);
    font-weight: 600;
}
</style>
@endpush

@section('content')
    <div class="container my-4" dir="rtl">
        <!-- 1. Header: صورة الملف، الاسم، والإحصائيات السريعة -->
        <div class="row align-items-center mb-4">
            <div class="col-auto">
                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://api.dicebear.com/7.x/identicon/svg?seed=' . $user->name }}"
                    alt="{{ $user->name }}" class="rounded" width="96" height="96">
            </div>
            <div class="col">
                <h2 class="fw-bold mb-1">{{ $user->name }}</h2>
                <div class="text-muted small d-flex flex-wrap gap-3 mb-2">
                    <span><i class="fas fa-cake-candles ms-1"></i> عضو منذ {{ $user->created_at->diffForHumans(null, true) }}</span>
                    <span><i class="fas fa-clock ms-1"></i> آخر ظهور {{ $user->updated_at->diffForHumans() }}</span>
                </div>
                <span class="badge {{ $user->badge['bg_class'] ?? 'bg-secondary' }}">
                    <i class="{{ $user->badge['icon'] ?? 'fas fa-user' }} ms-1"></i> {{ $user->badge['name'] ?? 'عضو' }}
                </span>
            </div>

            @if($isOwner)
                <div class="col-auto d-flex gap-2">
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-pen ms-1"></i> تعديل الملف الشخصي
                    </a>
                </div>
            @endif
        </div>

        <!-- 2. Navigation Tabs -->
        <ul class="nav nav-pills mb-4 border-bottom pb-2">
            <li class="nav-item">
                <a class="nav-link active bg-warning text-dark rounded-pill px-3 py-1 fw-semibold"
                    href="{{ route('profile', $user->id) }}">الملف الشخصي</a>
            </li>
            @if($isOwner)
                <li class="nav-item ms-2">
                    <a class="nav-link text-secondary px-3 py-1" href="{{ route('profile.edit') }}">تعديل البيانات</a>
                </li>
            @endif
        </ul>

        <!-- 3. Main Content Area -->
        <div class="row g-4">

            <!-- العمود الأيمن (الإحصائيات والمسمى الوظيفي) -->
            <div class="col-lg-3">
                <h5 class="fw-bold mb-2">الإحصائيات</h5>
                <div class="card p-3 mb-4 bg-light-subtle border">
                    <div class="row text-center g-3">
                        <div class="col-6 text-start">
                            <div class="fw-bold fs-5">{{ $user->reputation_points ?? 1 }}</div>
                            <div class="text-muted small">السمعة</div>
                        </div>
                        <div class="col-6 text-start">
                            <div class="fw-bold fs-5">{{ $totalUserReach }}</div>
                            <div class="text-muted small">المشاهدات</div>
                        </div>
                        <div class="col-6 text-start">
                            <div class="fw-bold fs-5">{{ $user->answers_count ?? 0 }}</div>
                            <div class="text-muted small">الإجابات</div>
                        </div>
                        <div class="col-6 text-start">
                            <div class="fw-bold fs-5">{{ $user->questions_count ?? 0 }}</div>
                            <div class="text-muted small">الأسئلة</div>
                        </div>
                    </div>
                </div>

                <div class="card p-2 bg-light-subtle border">
                    <div class="d-flex justify-content-between align-items-center text-primary fw-semibold">
                        المسمى الوظيفي: {{ $user->title ?? 'غير محدد' }}
                    </div>
                </div>
            </div>

            <!-- العمود الأيسر (نبذة، الشارات، الأسئلة، الإجابات) -->
            <div class="col-lg-9">

                <!-- About Section -->
                <h5 class="fw-bold mb-2">نبذة عني</h5>
                @if (!$user->about_me)
                    <div class="card p-4 text-center text-muted bg-light border-0 mb-4">
                        @if($isOwner)
                            قسم النبذة الشخصية فارغ حالياً. هل ترغب في إضافة نبذة؟
                            <a href="{{ route('profile.edit') }}" class="text-decoration-none mt-1">تعديل الملف الشخصي</a>
                        @else
                            لم أكتب نبذة شخصية بعد.
                        @endif
                    </div>
                @else
                    <div class="card p-4 bg-light-subtle border mb-4">
                        <p class="mb-0">{!! $user->about_me !!}</p>
                    </div>
                @endif

                <!-- Badges Section -->
                <h5 class="fw-bold mb-2">الشارات (Badges)</h5>
                <div class="row g-3 mb-4">
                    <!-- Gold Badge -->
                    <div class="col-md-4">
                        <div class="card p-3 text-center h-100 border">
                            <div class="text-warning fs-3 mb-2"><i class="fas fa-award"></i></div>
                            @if(($user->reputation_points ?? 0) >= 500)
                                <div class="fw-bold text-dark">المستوى الذهبي</div>
                                <span class="badge badge-gold-shiny text-dark mt-1">● خبير</span>
                            @else
                                <p class="text-muted small mb-0">تُفتح عند الوصول إلى 500 نقطة سمعة.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Silver Badge -->
                    <div class="col-md-4">
                        <div class="card p-3 text-center h-100 border">
                            <div class="text-secondary fs-3 mb-2"><i class="fas fa-award"></i></div>
                            @if(($user->reputation_points ?? 0) >= 200)
                                <div class="fw-bold text-dark">المستوى الفضي</div>
                                <span class="badge badge-silver-shiny text-white mt-1">● متقدم</span>
                            @else
                                <p class="text-muted small mb-0">تُفتح عند الوصول إلى 200 نقطة سمعة.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Bronze Badge -->
                    <div class="col-md-4">
                        <div class="card p-3 text-center h-100 border">
                            <div class="text-danger fs-3 mb-2"><i class="fas fa-award"></i></div>
                            @if(($user->reputation_points ?? 0) >= 15)
                                <div class="fw-bold text-dark">المستوى البرونزي</div>
                                <span class="badge text-white mt-1 badge-bronze-shiny">● مساهم</span>
                            @else
                                <p class="text-muted small mb-0">تُفتح عند الوصول إلى 15 نقطة سمعة.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 1. قسم الأسئلة -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold mb-0">الأسئلة</h5>
                    <span class="badge bg-secondary rounded-pill">
                        {{ $user->questions_count ?? $user->questions->count() }}
                    </span>
                </div>

                @if (isset($user->questions) && $user->questions->count() > 0)
                    <div class="list-group mb-4">
                        @foreach ($user->questions as $question)
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('questions.show', $question) }}" class="text-decoration-none fw-semibold">
                                        {{ $question->title }}
                                    </a>
                                    <div class="text-muted extra-small mt-1">
                                        طُرِح السؤال {{ $question->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-light text-dark border">
                                        {{ $question->answers_count ?? 0 }} إجابة
                                    </span>
                                    <span class="badge bg-light text-dark border">
                                        {{ $question->up_votes_count - $question->down_votes_count }} صوت
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card p-4 text-center bg-light border-0 mb-4">
                        @if($isOwner)
                            <p class="fw-semibold mb-1">لم تقم بطرح أي أسئلة حتى الآن</p>
                            <p class="text-muted small mb-0">
                                لديك استفسار؟ <a href="{{ route('ask') }}" class="text-decoration-none">اطرح سؤالاً جديداً</a> للمساعدة من المجتمع.
                            </p>
                        @else
                            <p class="text-muted mb-0">لم يقم هذا المستخدم بطرح أي أسئلة حتى الآن.</p>
                        @endif
                    </div>
                @endif

                <!-- 2. قسم الإجابات -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold mb-0">الإجابات</h5>
                    <span class="badge bg-secondary rounded-pill">
                        {{ $user->answers_count ?? $user->answers->count() }}
                    </span>
                </div>

                @if (isset($user->answers) && $user->answers->count() > 0)
                    <div class="list-group mb-4">
                        @foreach ($user->answers as $answer)
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small ms-2">على سؤال:</span>
                                    @if ($answer->question)
                                        <a href="{{ route('questions.show', $answer->question) }}" class="text-decoration-none fw-semibold">
                                            {{ $answer->question->title }}
                                        </a>
                                    @else
                                        <span class="text-muted fst-italic">[ سؤال محذوف ]</span>
                                    @endif

                                    <div class="text-muted extra-small mt-1">
                                        تمت الإجابة {{ $answer->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark border">
                                    {{ $answer->up_votes_count - $answer->down_votes_count }} صوت
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card p-4 text-center bg-light border-0 mb-4">
                        @if($isOwner)
                            <p class="fw-semibold mb-1">لم تقم بالإجابة على أي سؤال حتى الآن</p>
                            <p class="text-muted small mb-0">
                                ساعد الآخرين من خلال <a href="{{ route('home') }}" class="text-decoration-none">الإجابة على الأسئلة المفتوحة</a>.
                            </p>
                        @else
                            <p class="text-muted mb-0">لم يقم هذا المستخدم بالإجابة على أي سؤال حتى الآن.</p>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection