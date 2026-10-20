@extends('layouts.app')

@php
    $isEdit = isset($question) && $question->exists;
@endphp

@section('content')
    <div class="container my-5" style="max-width: 800px;" dir="rtl">
        <!-- Header -->
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">
                {{ $isEdit ? 'تعديل السؤال' : 'طرح سؤال جديد' }}
            </h2>
            <p class="text-muted">
                {{ $isEdit ? 'تحديث تفاصيل سؤالك والوسوم الخاصة به' : 'شارك مشكلتك البرمجية مع مجتمع كود كويست (CodeQuest) واحصل على إجابات دقيقة' }}
            </p>
        </div>

        <!-- Form Card -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 p-md-5">
                <form action="{{ $isEdit ? route('question.update', $question->id) : route('ask.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    <!-- Question Title -->
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control rounded-3" id="titleInput" name="title"
                            placeholder="عنوان السؤال" required maxlength="100"
                            value="{{ old('title', $question->title ?? '') }}">
                        <label for="titleInput">عنوان السؤال</label>
                    </div>

                    <!-- Main Category -->
                    <div class="form-floating mb-4">
                        <select class="form-select rounded-3" id="categorySelect" name="cat_id" required>
                            <option value="" disabled
                                {{ !old('cat_id', $question->category_id ?? null) ? 'selected' : '' }}>-- اختر التصنيف
                                الرئيسي --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('cat_id', $question->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="categorySelect">التصنيف الرئيسي</label>
                    </div>

                    <!-- Tags Input -->
                    <div class="mb-4">
                        <label for="tags" class="form-label fw-bold">الوسوم (Tags) *</label>
                        <input name="tags[]" id="tagsInput" class="form-control" value="">
                        <div class="form-text">أضف حتى 5 وسوم لتوصيف سؤالك بشكل أفضل.</div>
                    </div>

                    <!-- Editor Container -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">تفاصيل السؤال</label>
                        <div id="question-editor" style="height: 250px;" dir="ltr"></div>
                        <input type="hidden" name="description" id="questionInput"
                            value="{{ old('description', $question->description ?? '') }}">
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start mt-4">
                        <button type="submit"
                            class="btn btn-{{ $isEdit ? 'warning' : 'primary' }} btn-lg px-5 rounded-3 fw-bold">
                            {{ $isEdit ? 'تحديث السؤال' : 'نشر السؤال' }}
                        </button>
                    </div>
                </form>

                @if ($isEdit)
                    <div class="mt-2">
                        <button type="button" class="btn btn-danger btn-lg px-5 rounded-3 fw-bold" data-bs-toggle='modal'
                            data-bs-target="#delete">حذف السؤال</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($isEdit)
        {{-- 🔴 موديل تحقق قبل الحذف  --}}
        <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="deleteLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-danger" id="deleteLabel">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" title="إغلاق"></button>
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> هل أنت متأكد من حذف هذا السؤال؟
                        </h5>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-2">سيتم حذف هذا السؤال نهائياً .</p>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>

                        <form action="{{ route('question.destroy', $question->id) }}" method="POST"
                            class="ms-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm px-3">حذف السؤال</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

    <style>
        .tagify {
            border-radius: 0.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <!-- استدعاء مكتبة Quill قبل ملف quill-uploader.js -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script src="{{ asset('js/quill-uploader.js') }}"></script>

    <script>
        // 1. Tagify Setup
        var input = document.querySelector('#tagsInput');
        var selectedTags = @json($isEdit ? $question->tags->map(fn($tag) => ['id' => $tag->tag_id, 'value' => $tag->name]) : []);

        var tagify = new Tagify(input, {
            maxTags: 5,
            placeholder: "اكتب للبحث عن الوسوم...",
            tagTextProp: 'value',
            valueField: 'id',
            whitelist: @json($tags->map(fn($tag) => ['id' => $tag->tag_id, 'value' => $tag->name])),
            dropdown: {
                maxItems: 10,
                enabled: 1,
                closeOnSelect: false
            }
        });

        if (selectedTags.length > 0) {
            tagify.addTags(selectedTags);
        }

        // 2. Quill Setup & Form Submission Fix
        document.addEventListener("DOMContentLoaded", function() {
            var editor = initQuillEditor(
                'question-editor',
                'questionInput',
                "{{ route('upload.editor.image') }}"
            );

            var existingContent = {!! json_encode(old('description', $question->description ?? '')) !!};

            if (existingContent && editor && !editor.root.innerHTML.trim()) {
                editor.root.innerHTML = existingContent;
            }

            // 🔴 التعديل الجوهري: مزامنة محتوى المحرر مع الحقل المخفي قبل إرسال الفورم
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    var questionInput = document.querySelector('#questionInput');
                    if (editor && questionInput) {
                        // وضع محتوى المحرر (HTML) داخل الحقل المخفي ليرسله الكنترولر بدقة
                        questionInput.value = editor.root.innerHTML;
                    }
                });
            }
        });
    </script>
@endpush