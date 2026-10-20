@extends('layouts.app')

@section('content')
    @php
        $tpyeedit = null;
    @endphp
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                {{-- بطاقة السؤال الرئيسية --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">

                        <div class="d-flex align-items-start gap-3">
                            {{-- تصويت السؤال --}}
                            <div class="d-flex flex-column align-items-center vote-section"
                                data-id="{{ $question->id }}" data-type="question">
                                @php
                                    $userVote = auth()->check()
                                        ? $question->votes->where('user_id', auth()->id())->first()?->vote_type
                                        : null;
                                @endphp
                                <button
                                    class="btn btn-sm btn-outline-secondary vote-btn vote-up {{ $userVote === 'up' ? 'active btn-success text-white' : '' }}"
                                    data-vote="up" title="تصويت إيجابي">
                                    <i class="bi bi-caret-up-fill fs-5"></i>
                                </button>
                                <span class="fw-bold my-1 votes-count fs-5">{{ $question->net_votes }}</span>
                                <button
                                    class="btn btn-sm btn-outline-secondary vote-btn vote-down {{ $userVote === 'down' ? 'active btn-danger text-white' : '' }}"
                                    data-vote="down" title="تصويت سلبي">
                                    <i class="bi bi-caret-down-fill fs-5"></i>
                                </button>
                            </div>

                            <div class="flex-grow-1">
                                {{-- معلومات صاحب السؤال --}}
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="d-flex align-items-center justify-content-between">

                                        <div class="d-flex align-items-center">
                                            <a href="{{ route('profile', $question->user->id) }}"> <img
                                                    src="{{ $question->user->avatar ? asset('storage/' . $question->user->avatar) : 'https://api.dicebear.com/7.x/identicon/svg?seed=' . $question->user->name }}"
                                                    alt="{{ $question->user->name }}" width="55" height="55"
                                                    class="rounded-circle ms-3 object-fit-cover border"></a>
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $question->user->name }}</h6>
                                                <small class="text-muted">
                                                    {{ $question->user->title ?? 'عضو' }} •
                                                    <span
                                                        class="badge bg-secondary">{{ $question->user->reputation_points }}
                                                        نقطة</span>
                                                </small>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            @can('update', $question)
                                                <a href="{{ route('question.edit', $question->id) }}"
                                                    class="btn btn-warning btn-sm">تعديل السؤال</a>
                                            @endcan

                                            {{-- 🚩 زر الإبلاغ عن السؤال --}}
                                            <button class="btn btn-sm btn-outline-danger report-btn"
                                                data-id="{{ $question->id }}" data-type="question"
                                                title="الإبلاغ عن هذا السؤال">
                                                <i class="bi bi-flag-fill"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>

                                {{-- عنوان السؤال --}}
                                <h1 class="h3 fw-bold text-dark mb-3">{{ $question->title }}</h1>

                                {{-- البيانات الوصفية --}}
                                <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                                    <div><i class="bi bi-clock me-1"></i>تاريخ الطرح: <time
                                            datetime="{{ $question->created_at->toIso8601String() }}">{{ $question->created_at->diffForHumans() }}</time>
                                    </div>
                                    <div><i class="bi bi-eye me-1"></i>المشاهدات: {{ $question->views_count }}</div>
                                    <div><i class="bi bi-folder me-1"></i>التصنيف: <a href="#"
                                            class="badge bg-light text-dark border rounded-pill px-3 py-1 mb-1">{{ $question->category->name }}</a>
                                    </div>
                                </div>

                                <hr class="my-3">

                                {{-- تفاصيل السؤال --}}
                                <div class="fs-5 text-secondary mb-4 style-description">
                                    {!! $question->description !!}
                                </div>

                                {{-- الوسوم --}}
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-bold me-2 text-muted"><i class="bi bi-tags me-1"></i>الوسوم:</span>
                                    @forelse ($question->tags as $tag)
                                        <a href="#" class="btn btn-primary rounded-pill px-3 py-1 mb-1"
                                            style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .2rem; --bs-btn-font-size: .63rem;">
                                            {{ $tag->name }}
                                        </a>
                                    @empty
                                        <span class="text-muted small">لا توجد وسوم</span>
                                    @endforelse
                                </div>

                                {{-- قسم تعليقات السؤال --}}
                                <div class="mt-4 pt-3 border-top">
                                    <h6 class="fw-bold mb-3 text-muted"><i class="bi bi-chat-text me-1"></i>التعليقات على
                                        السؤال:</h6>

                                    @forelse ($question->comments as $comment)
                                        <div
                                            class="d-flex align-items-start justify-content-between mb-2 pb-2 border-bottom text-dark small">
                                            <div class="d-flex align-items-start flex-grow-1">
                                                <a href="{{ route('profile', $comment->user->id) }}"> <img
                                                        src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : 'https://api.dicebear.com/7.x/identicon/svg?seed=' . $comment->user->name }}"
                                                        alt="{{ $comment->user->name }}" width="28" height="28"
                                                        class="rounded-circle me-2 object-fit-cover mt-1"></a>
                                                <div class="ms-2">
                                                    <span class="fw-bold me-1">{{ $comment->user->name }}:</span>
                                                    <span>{{ $comment->content }}</span>
                                                    <span class="text-muted ms-2"
                                                        style="font-size: 0.75rem;">({{ $comment->created_at->diffForHumans() }})</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                @can('update', $comment)
                                                    {{-- ✏️ زر تعديل تعليق السؤال --}}
                                                    <button
                                                        class="btn btn-link text-primary p-0 ms-1 edit-btn opacity-75 opacity-100-hover"
                                                        data-id="{{ $comment->id ?? $comment->comment_id }}"
                                                        data-type="comment"
                                                        data-content="{{ $comment->content }}"
                                                        title="تعديل التعليق">
                                                        <i class="bi bi-pencil-square fs-7"></i>
                                                    </button>

                                                    {{-- 🗑️ زر حذف تعليق السؤال --}}
                                                    <button
                                                        class="btn btn-link text-danger p-0 ms-1 delete-btn opacity-75 opacity-100-hover"
                                                        data-id="{{ $comment->id ?? $comment->comment_id }}"
                                                        data-type="comment" title="حذف التعليق">
                                                        <i class="bi bi-trash fs-7"></i>
                                                    </button>
                                                @endcan

                                                {{-- 🚩 زر الإبلاغ عن تعليق السؤال --}}
                                                <button
                                                    class="btn btn-link text-muted p-0 ms-2 report-btn opacity-75 opacity-100-hover"
                                                    data-id="{{ $comment->id ?? $comment->comment_id }}"
                                                    data-type="comment" title="إبلاغ">
                                                    <i class="bi bi-flag fs-7"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted small mb-2">لا توجد تعليقات على هذا السؤال بعد.</p>
                                    @endforelse

                                    @auth
                                        <form
                                            action="{{ route('comments.store', ['type' => 'question', 'id' => $question->id]) }}"
                                            method="POST" class="mt-3">
                                            @csrf
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="content" class="form-control"
                                                    placeholder="اكتب تعليقاً على السؤال..." required>
                                                <button class="btn btn-outline-primary" type="submit">تعليق</button>
                                            </div>
                                        </form>
                                    @endauth
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                {{-- قسم الإجابات --}}
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="fw-bold mb-0">
                        {{ $question->answers->count() }} {{ $question->answers->count() == 1 ? 'إجابة' : 'إجابات' }}
                    </h4>
                </div>

                @forelse ($question->answers as $answer)
                    <div class="card shadow-sm border-0 mb-3 style-description">
                        <div class="card-body p-4">

                            <div class="d-flex align-items-start gap-3">
                                {{-- تصويت الإجابة --}}
                                <div class="d-flex flex-column align-items-center vote-section"
                                    data-id="{{ $answer->id ?? $answer->answer_id }}" data-type="answer">
                                    @php
                                        $userAnswerVote = auth()->check()
                                            ? $answer->votes->where('user_id', auth()->id())->first()?->vote_type
                                            : null;
                                    @endphp
                                    <button
                                        class="btn btn-sm btn-outline-secondary vote-btn vote-up {{ $userAnswerVote === 'up' ? 'active btn-success text-white' : '' }}"
                                        data-vote="up" title="تصويت إيجابي">
                                        <i class="bi bi-caret-up-fill fs-5"></i>
                                    </button>
                                    <span class="fw-bold my-1 votes-count fs-5">{{ $answer->net_votes }}</span>
                                    <button
                                        class="btn btn-sm btn-outline-secondary vote-btn vote-down {{ $userAnswerVote === 'down' ? 'active btn-danger text-white' : '' }}"
                                        data-vote="down" title="تصويت سلبي">
                                        <i class="bi bi-caret-down-fill fs-5"></i>
                                    </button>
                                </div>

                                <div class="flex-grow-1">
                                    @if ($answer->is_accepted)
                                        <div class="alert alert-success d-flex align-items-center justify-content-between mb-3 py-2 px-3"
                                            role="alert">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-check-circle-fill fs-5"></i>
                                                <span class="fw-bold">إجابة مقبولة (الحل المعتمد)</span>
                                            </div>

                                            @auth
                                                @if (auth()->id() === $question->user_id)
                                                    <form action="{{ route('answer.accept', $answer->id ?? $answer->answer_id) }}"
                                                        method="POST" class="d-inline ajax-form">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="is_accepted" value="0">
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger py-0 px-2">إلغاء
                                                            القبول</button>
                                                    </form>
                                                @endif
                                            @endauth
                                        </div>
                                    @elseif (!$question->answers->where('is_accepted', true)->count())
                                        @auth
                                            @if (auth()->id() === $question->user_id)
                                                <form action="{{ route('answer.accept', $answer->id ?? $answer->answer_id) }}"
                                                    method="POST" class="mb-3 ajax-form">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="is_accepted" value="1">
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-success d-flex align-items-center gap-1">
                                                        <i class="bi bi-check2-circle"></i> تحديد كإجابة مقبولة
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    @endif

                                    <p class="card-text text-dark fs-6 mb-3">{!! $answer->answer_text !!}</p>

                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>تمت الإجابة:
                                            <time datetime="{{ $answer->created_at->toIso8601String() }}">
                                                {{ $answer->created_at->diffForHumans() }}
                                            </time>
                                        </small>

                                        <div class="d-flex align-items-center gap-3">
                                            @if (isset($answer->user))
                                                <div class="d-flex align-items-center">
                                                    <a href="{{ route('profile', $answer->user->id) }}"><img
                                                            src="{{ $answer->user->avatar ? asset('storage/' . $answer->user->avatar) : 'https://api.dicebear.com/7.x/identicon/svg?seed=' . $answer->user->name }}"
                                                            width="30" height="30"
                                                            class="rounded-circle me-2 object-fit-cover"></a>
                                                    <small
                                                        class="fw-semibold text-secondary">{{ $answer->user->name }}</small>
                                                </div>
                                            @endif

                                            @can('update', $answer)
                                                {{-- ✏️ زر تعديل الإجابة --}}
                                                <button class="btn btn-sm btn-link text-primary p-0 edit-btn"
                                                    data-id="{{ $answer->id ?? $answer->answer_id }}" data-type="answer"
                                                    data-content="{!! e($answer->answer_text) !!}" title="تعديل الإجابة">
                                                    <i class="bi bi-pencil-square fs-6"></i>
                                                </button>

                                                {{-- 🗑️ زر حذف الإجابة --}}
                                                <button class="btn btn-sm btn-link text-danger p-0 delete-btn"
                                                    data-id="{{ $answer->id ?? $answer->answer_id }}" data-type="answer"
                                                    title="حذف الإجابة">
                                                    <i class="bi bi-trash fs-6"></i>
                                                </button>
                                            @endcan

                                            {{-- 🚩 زر الإبلاغ عن هذه الإجابة --}}
                                            <button class="btn btn-sm btn-link text-muted p-0 report-btn"
                                                data-id="{{ $answer->id ?? $answer->answer_id }}" data-type="answer"
                                                title="الإبلاغ عن هذه الإجابة">
                                                <i class="bi bi-flag"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- قسم تعليقات الإجابة --}}
                                    <div class="mt-3 pt-3 border-top bg-light p-3 rounded-3">
                                        <h6 class="fw-bold mb-2 text-muted small"><i
                                                class="bi bi-chat-left-text me-1"></i>التعليقات على الإجابة:</h6>

                                        @forelse ($answer->comments as $comment)
                                            <div
                                                class="d-flex align-items-start justify-content-between mb-2 pb-1 border-bottom text-dark small">
                                                <div class="d-flex align-items-start">
                                                    @if (isset($comment->user))
                                                        <a href="{{ route('profile', $comment->user->id) }}"> <img
                                                                src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : 'https://api.dicebear.com/7.x/identicon/svg?seed=' . $comment->user->name }}"
                                                                alt="{{ $comment->user->name }}" width="24"
                                                                height="24"
                                                                class="rounded-circle me-2 object-fit-cover mt-1"></a>
                                                    @endif
                                                    <div class="ms-2">
                                                        <strong class="me-1"
                                                            style="font-size: 0.85rem;">{{ $comment->user->name ?? 'مستخدم' }}:</strong>
                                                        <span
                                                            style="font-size: 0.85rem;">{{ $comment->content ?? $comment->comment_text }}</span>
                                                        <span class="text-muted ms-2"
                                                            style="font-size: 0.7rem;">({{ $comment->created_at->diffForHumans() }})</span>
                                                    </div>
                                                </div>
                                                {{-- 🚩 زر الإبلاغ عن تعليق الإجابة --}}
                                                <div class="d-flex align-items-center gap-1">
                                                    @can('update', $comment)
                                                        {{-- ✏️ زر تعديل تعليق الإجابة --}}
                                                        <button
                                                            class="btn btn-link text-primary p-0 ms-1 edit-btn opacity-75 opacity-100-hover"
                                                            data-id="{{ $comment->id ?? $comment->comment_id }}"
                                                            data-type="comment"
                                                            data-content="{{ $comment->content }}"
                                                            title="تعديل التعليق">
                                                            <i class="bi bi-pencil-square fs-7"></i>
                                                        </button>

                                                        {{-- 🗑️ زر حذف تعليق الإجابة --}}
                                                        <button
                                                            class="btn btn-link text-danger p-0 ms-1 delete-btn opacity-75 opacity-100-hover"
                                                            data-id="{{ $comment->id ?? $comment->comment_id }}"
                                                            data-type="comment" title="حذف التعليق">
                                                            <i class="bi bi-trash fs-7"></i>
                                                        </button>
                                                    @endcan

                                                    {{-- 🚩 زر الإبلاغ عن تعليق الإجابة --}}
                                                    <button
                                                        class="btn btn-link text-muted p-0 ms-2 report-btn opacity-75 opacity-100-hover"
                                                        data-id="{{ $comment->id ?? $comment->comment_id }}"
                                                        data-type="comment" title="إبلاغ">
                                                        <i class="bi bi-flag fs-7"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-muted small mb-2" style="font-size: 0.8rem;">لا توجد تعليقات
                                                على هذه الإجابة.</p>
                                        @endforelse

                                        @auth
                                            <form
                                                action="{{ route('comments.store', ['type' => 'answer', 'id' => $answer->id ?? $answer->answer_id]) }}"
                                                method="POST" class="mt-2">
                                                @csrf
                                                <div class="input-group input-group-sm">
                                                    <input type="text" name="content" class="form-control"
                                                        placeholder="اكتب تعليقاً على هذه الإجابة..." required>
                                                    <button class="btn btn-outline-secondary" type="submit">إرسال</button>
                                                </div>
                                            </form>
                                        @endauth
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="alert alert-light text-center border py-4" role="alert">
                        <i class="bi bi-chat-left-dots fs-3 text-muted d-block mb-2"></i>
                        لا توجد إجابات حتى الآن. كن أول من يجيب!
                    </div>
                @endforelse

                @auth
                    <div class="card shadow-sm border-0 mb-3 mt-4">
                        <div class="card-body p-4">
                            <p class="card-text text-dark fs-5 mb-3 fw-bold">قم بإدخال إجابتك</p>

                            <form action="{{ route('questions.answer') }}" method="post">
                                @csrf
                                <div class="mb-3">
                                    <div id="answer-editor" style="height: 250px;" dir="ltr"></div>
                                    <input type="hidden" name="answer" id="answerInput">
                                    <input type="hidden" name="question_id" value="{{ $question->id }}">
                                </div>
                                <button class="btn btn-primary px-4" type="submit">أجب</button>
                            </form>
                        </div>
                    </div>
                @endauth

            </div>
        </div>
    </div>

    {{-- 🟣 نافذة التعديل والحذف الموحدة (Edit/Delete Unified Modal) --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-primary" id="editModalLabel">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" title="إغلاق"></button>
                        <i class="bi bi-pencil-square me-1"></i> <span id="modalTitleText">تعديل</span>
                    </h5>
                </div>
                <form id="editForm">
                    @csrf
                    <input type="hidden" id="editTargetId" name="id">
                    <input type="hidden" id="editTargetType" name="type">

                    <div class="modal-body">
                        {{-- حقل Quill Editor (يظهر للإجابات فقط) --}}
                        <div id="quillContainer" class="mb-3 d-none">
                            <label class="form-label fw-semibold">محتوى الإجابة:</label>
                            <div id="edit-modal-quill-editor" style="height: 200px;" dir="ltr"></div>
                            <input type="hidden" id="editQuillContent" name="content_quill">
                        </div>

                        {{-- حقل Textarea العادي (يظهر للتعليقات فقط) --}}
                        <div id="plainTextContainer" class="mb-3 d-none">
                            <label for="editPlainContent" class="form-label fw-semibold">محتوى التعليق:</label>
                            <textarea class="form-control" id="editPlainContent" name="content_plain" rows="4"
                                placeholder="اكتب التعليق هنا..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer bg-light d-flex justify-content-between">
                        <button type="button" class="btn btn-danger btn-sm" id="modalDeleteBtn">
                            <i class="bi bi-trash me-1"></i> حذف
                        </button>

                        <div>
                            <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary btn-sm px-4" id="submitEditBtn">حفظ
                                التعديلات</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- 🔴 نافذة الإبلاغ الموحدة (Report Modal) --}}
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-danger" id="reportModalLabel">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" title="إغلاق"></button>
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> إرسال بلاغ
                    </h5>
                </div>
                <form id="reportForm">
                    <div class="modal-body">
                        <input type="hidden" id="reportTargetId" name="id">
                        <input type="hidden" id="reportTargetType" name="type">

                        <div class="mb-3">
                            <label for="reportReason" class="form-label fw-semibold">سبب الإبلاغ:</label>
                            <select class="form-select" id="reportReason" name="reason" required>
                                <option value="" selected disabled>اختر السبب...</option>
                                <option value="spam">محتوى مزعج / إعلاني (Spam)</option>
                                <option value="abuse">محتوى مسيء أو غير لائق</option>
                                <option value="inaccurate">معلومات مضللة أو غير صحيحة</option>
                                <option value="other">سبب آخر</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="reportDetails" class="form-label fw-semibold">تفاصيل إضافية (اختياري):</label>
                            <textarea class="form-control" id="reportDetails" name="details" rows="3"
                                placeholder="وضح سبب الإبلاغ بالتفصيل..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger btn-sm px-3" id="submitReportBtn">إرسال
                            البلاغ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="content modal-content text-center p-3">
                <div class="modal-body p-2">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-1 mb-2"></i>
                    <h5 class="fw-bold mb-2">تأكيد الحذف</h5>
                    <p class="text-muted small mb-0">هل أنت تأكد من رغبتك في الحذف؟ لا يمكن التراجع عن هذا الإجراء.</p>
                </div>
                <div class="d-flex justify-content-center gap-2 mt-3">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger btn-sm px-3">حذف</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">

    <style>
        .ql-code-block-container {
            background-color: #2d2d2d !important;
            border-radius: 8px;
            padding: 12px !important;
            margin: 15px 0;
            direction: ltr !important;
            text-align: left !important;
            font-family: 'Fira Code', Consolas, monospace !important;
        }

        .ql-code-block-container pre,
        .ql-code-block-container code {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            text-shadow: none !important;
        }

        .style-description img {
            max-width: 100% !important;
            height: auto !important;
            object-fit: contain;
            border-radius: 8px;
        }

        .opacity-100-hover:hover {
            opacity: 1 !important;
        }

        .fs-7 {
            font-size: 0.8rem;
        }

        .btn-loading {
            pointer-events: none;
            opacity: 0.65;
            position: relative;
        }

        .btn-loading .spinner-border-sm {
            display: inline-block !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/quill-uploader.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>

    <script>
        function escapeHtml(text) {
            return text.replace(/[&<>"']/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            } [m]));
        }

        document.addEventListener("DOMContentLoaded", () => {

            // 1. تهيئة Quill و Prism وإصلاح الأكواد البرمجية
            let mainQuill = null;
            if (typeof initQuillEditor === 'function' && document.getElementById('answer-editor')) {
                mainQuill = initQuillEditor('answer-editor', 'answerInput', "{{ route('upload.editor.image') }}");
            }

            document.querySelectorAll('.ql-code-block-container').forEach(container => {
                const lines = Array.from(container.querySelectorAll('.ql-code-block')).map(el => el.innerText);
                const rawText = lines.length ? lines.join('\n') : container.innerText;
                if (rawText.trim()) {
                    container.innerHTML = `<pre><code class="language-clike">${escapeHtml(rawText)}</code></pre>`;
                }
            });

            if (typeof Prism !== 'undefined') Prism.highlightAll();


            // 2. معالجة إرسال نموذج الإجابة ونقل محتوى محرر Quill بحقل الإدخال المخفي
            const answerForm = document.querySelector('form[action*="answer"]');
            if (answerForm) {
                answerForm.addEventListener('submit', function (e) {
                    const answerInput = document.getElementById('answerInput');
                    const quillEditorElement = document.querySelector('#answer-editor .ql-editor');
                    
                    if (answerInput && quillEditorElement) {
                        answerInput.value = quillEditorElement.innerHTML;
                    }
                });
            }


            // 3. إعدادات Axios و Interceptors
            axios.interceptors.response.use(
                response => response,
                error => {
                    if (error.response && error.response.status === 429) {
                        let retryAfter = error.response.headers['retry-after'];
                        showJsonModal({
                            status: 429,
                            title: 'تنبيه: محاولات كثيرة',
                            retryAfter: retryAfter
                        });
                        return new Promise(() => {});
                    }
                    return Promise.reject(error);
                }
            );
            Object.assign(axios.defaults.headers.common, {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            });

            let disabledButtons = [];

            axios.interceptors.request.use(config => {
                const activeBtn = document.activeElement;
                if (activeBtn && (activeBtn.tagName === 'BUTTON' || activeBtn.classList.contains('btn-action'))) {
                    activeBtn.disabled = true;
                    activeBtn.classList.add('btn-loading');
                    disabledButtons.push(activeBtn);
                }
                return config;
            }, error => Promise.reject(error));

            axios.interceptors.response.use(response => {
                resetButtons();
                return response;
            }, error => {
                resetButtons();
                const errData = error.response?.data;
                let message = 'حدث خطأ غير متوقع أثناء الاتصال بالسيرفر.';

                if (error.response?.status === 422 && errData?.errors) {
                    message = Object.values(errData.errors).flat().join('<br>');
                } else if (errData?.message || errData?.error) {
                    message = errData.message || errData.error;
                }

                showJsonModal({
                    status: 'error',
                    title: 'خطأ في العملية',
                    message: message
                });

                return Promise.reject(error);
            });

            function resetButtons() {
                disabledButtons.forEach(btn => {
                    btn.disabled = false;
                    btn.classList.remove('btn-loading');
                });
                disabledButtons = [];
            }


            // 4. معالجة التصويت (Event Delegation)
            document.addEventListener('click', e => {
                const btn = e.target.closest('.vote-btn');
                if (!btn) return;

                @guest
                showJsonModal({
                    status: 'warning',
                    title: 'تنبيه',
                    message: 'يجب عليك تسجيل الدخول لتتمكن من التصويت.'
                });
                return;
                @endguest

                const section = btn.closest('.vote-section');
                const { id, type } = section.dataset;
                const vote_type = btn.dataset.vote;

                axios.post("{{ route('vote') }}", { id, type, vote_type })
                .then(({ data }) => {
                    if (!data.success) return;

                    section.querySelector('.votes-count').textContent = data.net_votes;

                    const upBtn = section.querySelector('.vote-up');
                    const downBtn = section.querySelector('.vote-down');

                    if (upBtn) {
                        upBtn.classList.toggle('active', data.user_vote === 'up');
                        upBtn.classList.toggle('btn-success', data.user_vote === 'up');
                        upBtn.classList.toggle('text-white', data.user_vote === 'up');
                    }

                    if (downBtn) {
                        downBtn.classList.toggle('active', data.user_vote === 'down');
                        downBtn.classList.toggle('btn-danger', data.user_vote === 'down');
                        downBtn.classList.toggle('text-white', data.user_vote === 'down');
                    }
                });
            });


            // 5. معالجة فتح وإرسال مودال الإبلاغ
            const reportModalEl = document.getElementById('reportModal');
            const reportModal = reportModalEl ? new bootstrap.Modal(reportModalEl) : null;
            const reportForm = document.getElementById('reportForm');

            document.addEventListener('click', e => {
                const btn = e.target.closest('.report-btn');
                if (!btn) return;

                @guest
                showJsonModal({
                    status: 'warning',
                    title: 'تنبيه',
                    message: 'يجب عليك تسجيل الدخول لتتمكن من الإبلاغ.'
                });
                return;
                @endguest

                document.getElementById('reportTargetId').value = btn.dataset.id;
                document.getElementById('reportTargetType').value = btn.dataset.type;
                reportForm.reset();

                if (reportModal) reportModal.show();
            });

            if (reportForm) {
                reportForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = {
                        id: document.getElementById('reportTargetId').value,
                        type: document.getElementById('reportTargetType').value,
                        reason: document.getElementById('reportReason').value,
                        details: document.getElementById('reportDetails').value
                    };

                    axios.post("{{ route('report.store') }}", formData)
                        .then(({ data }) => {
                            if (reportModal) reportModal.hide();
                            showJsonModal({
                                status: 'success',
                                title: 'تم الإرسال',
                                message: data.message || 'تم استلام بلاغك بنجاح.'
                            });
                            reportForm.reset();
                        });
                });
            }


            // 6. إدارة مودال التعديل والحذف الموحد
            let modalQuillInstance = null;
            const editModalEl = document.getElementById('editModal');
            const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
            const editForm = document.getElementById('editForm');

            if (document.getElementById('edit-modal-quill-editor')) {
                modalQuillInstance = new Quill('#edit-modal-quill-editor', {
                    theme: 'snow',
                    modules: {
                        syntax: false,
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'strike'],
                            [{ 'align': [] }],
                            [{ 'direction': 'rtl' }],
                            ['blockquote', 'code-block'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            ['link', 'image']
                        ]
                    }
                });
            }

            document.addEventListener('click', e => {
                const btn = e.target.closest('.edit-btn');
                if (!btn) return;

                const { id, type } = btn.dataset;
                document.getElementById('editTargetId').value = id;
                document.getElementById('editTargetType').value = type;

                const quillContainer = document.getElementById('quillContainer');
                const plainTextContainer = document.getElementById('plainTextContainer');
                const modalTitle = document.getElementById('modalTitleText');

                function decodeHTML(html) {
                    const txt = document.createElement('textarea');
                    txt.innerHTML = html;
                    return txt.value;
                }

                let rawContent = btn.dataset.content || '';
                rawContent = decodeHTML(rawContent);

                if (type === 'answer') {
                    modalTitle.textContent = 'تعديل الإجابة';
                    quillContainer.classList.remove('d-none');
                    plainTextContainer.classList.add('d-none');

                    if (modalQuillInstance) {
                        modalQuillInstance.setText('');
                        modalQuillInstance.clipboard.dangerouslyPasteHTML(0, rawContent);
                    }
                } else if (type === 'comment') {
                    modalTitle.textContent = 'تعديل التعليق';
                    plainTextContainer.classList.remove('d-none');
                    quillContainer.classList.add('d-none');

                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = rawContent;
                    document.getElementById('editPlainContent').value = tempDiv.textContent || tempDiv.innerText || '';
                }

                if (editModal) editModal.show();
            });

            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const id = document.getElementById('editTargetId').value;
                    const type = document.getElementById('editTargetType').value;

                    const content = type === 'answer' ?
                        (modalQuillInstance ? modalQuillInstance.root.innerHTML : '') :
                        document.getElementById('editPlainContent').value;

                    const baseUrl = "{{ url('/') }}";
                    const url = type === 'answer' ? `${baseUrl}/answers/${id}` : `${baseUrl}/comments/${id}`;

                    axios.put(url, { content: content, type: type })
                        .then(({ data }) => {
                            if (editModal) editModal.hide();
                            showJsonModal({
                                status: 'success',
                                title: 'تم التعديل',
                                message: data.message || 'تم تحديث المحتوى بنجاح.'
                            });
                            setTimeout(() => location.reload(), 1000);
                        });
                });
            }


            // 7. دالة وأزرار الحذف (باستخدام Bootstrap Modal)
            const deleteModalEl = document.getElementById('deleteConfirmModal');
            const deleteConfirmModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
            let pendingDeleteData = null;

            function executeDelete(id, type) {
                const baseUrl = "{{ url('/') }}";
                const url = type === 'answer' ? `${baseUrl}/answers/${id}` : `${baseUrl}/comments/${id}`;
                axios.delete(url)
                    .then(({ data }) => {
                        if (deleteConfirmModal) deleteConfirmModal.hide();
                        if (editModal) editModal.hide();
                        showJsonModal({
                            status: 'success',
                            title: 'تم الحذف',
                            message: data.message || 'تم الحذف بنجاح.'
                        });
                        setTimeout(() => location.reload(), 1000);
                    });
            }

            document.addEventListener('click', e => {
                const btn = e.target.closest('.delete-btn');
                if (!btn) return;

                pendingDeleteData = {
                    id: btn.dataset.id,
                    type: btn.dataset.type
                };
                if (deleteConfirmModal) deleteConfirmModal.show();
            });

            document.getElementById('modalDeleteBtn')?.addEventListener('click', () => {
                const id = document.getElementById('editTargetId').value;
                const type = document.getElementById('editTargetType').value;
                pendingDeleteData = { id, type };
                if (deleteConfirmModal) deleteConfirmModal.show();
            });

            document.getElementById('confirmDeleteBtn')?.addEventListener('click', () => {
                if (pendingDeleteData) {
                    executeDelete(pendingDeleteData.id, pendingDeleteData.type);
                    pendingDeleteData = null;
                }
            });


            // 8. تعطيل أزرار النماذج العادية لمنع التكرار
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (e.defaultPrevented) return;
                    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('btn-loading');
                    }
                });
            });

        });
    </script>
@endpush