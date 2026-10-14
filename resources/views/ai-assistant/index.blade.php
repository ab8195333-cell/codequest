@extends('layouts.app')

@section('content')
    <!-- مكتبات تنسيق الماركداون وتظليل الأكواد و Axios -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>

    <style>
        /* توضيح الـ Placeholder */
        #userInput::placeholder {
            color: #8b949e !important;
            opacity: 1;
        }

        /* تنسيق كتلة الكود مع الشريط العلوي */
        .message pre {
            background: #0d1117 !important;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 12px 16px !important;
            padding-top: 45px !important;
            /* مسافة فارغة في الأعلى للشريط */
            position: relative;
            margin: 12px 0;
            direction: ltr;
            text-align: left;
        }

        /* الشريط العلوي لكتلة الكود */
        .code-header {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 36px;
            background: #161b22;
            border-bottom: 1px solid #30363d;
            border-top-left-radius: 7px;
            border-top-right-radius: 7px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 12px;
            direction: ltr;
            /* لضمان ظهور اسم اللغة وزر النسخ بشكل مرتّب */
        }

        /* معرف اللغة داخل الشريط العلوي */
        .lang-badge {
            color: #8b949e;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            font-family: monospace;
        }

        /* زر النسخ داخل الشريط العلوي */
        .copy-code-btn {
            background: #21262d;
            border: 1px solid #30363d;
            color: #c9d1d9;
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .copy-code-btn:hover {
            background: #30363d;
            color: #ffffff;
        }

        /* أنيميشن الانتظار */
        .typing-dot {
            width: 8px;
            height: 8px;
            background: #8b949e;
            border-radius: 50%;
            animation: typing 1.4s infinite ease-in-out;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {

            0%,
            100% {
                transform: translateY(0);
                opacity: 0.4;
            }

            50% {
                transform: translateY(-4px);
                opacity: 1;
            }
        }

        /* وضع التوسيع الكلي للمدخل (Expanded Mode) */
        .input-box-expanded {
            position: absolute !important;
            top: 15px;
            bottom: 15px;
            left: 15px;
            right: 15px;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            background-color: #0d1117 !important;
        }

        .input-box-expanded #userInput {
            max-height: none !important;
            flex-grow: 1;
            height: 100% !important;
        }
    </style>

    <div class="container-fluid px-3 px-md-4 py-2">
        <div class="card bg-dark text-light border-secondary shadow-lg mx-auto position-relative"
            style="max-width: 1100px; height: calc(100vh - 110px); border-color: #30363d !important; background-color: #161b22 !important;">
            <!-- منطقة الرسائل -->
            <div class="card-body overflow-auto d-flex flex-column gap-3 p-3" id="chatMessages">
                <div class="d-flex justify-content-start align-items-center p-3 border-bottom border-secondary">
                    <div class="p-3 text-center text-light bg-opacity-10 bg-light rounded border border-secondary text-wrap mx-auto"
                        style="max-width: 90%; border-color: #30363d !important; font-size: 14px;">
                        أهلاً بك في بيئة المساعد الذكي لـ CodeQuest! أرسل الكود أو السؤال وسيتم إرجاع الحل منسقاً بالكامل.
                    </div>

                </div>
            </div>

            <!-- صندوق الإدخال -->
            <div class="card-footer border-top border-secondary p-3"
                style="border-color: #30363d !important; background-color: #161b22 !important;">
                <form id="chatForm" onsubmit="handleSend(event)">
                    <div id="inputBoxContainer"
                        class="p-2 rounded-4 border border-secondary position-relative transition-all"
                        style="background-color: #0d1117; border-color: #30363d !important;">

                        <!-- زر التكبير/التصغير في الزاوية علوياً -->
                        <div class="d-flex justify-content-end px-1 pt-1">
                            <button type="button" class="btn btn-sm text-secondary p-0 shadow-none" id="expandToggleBtn"
                                onclick="toggleExpandInput()" title="تكبير / تصغير">
                                <i class="bi bi-arrows-angle-expand fs-6" id="expandIcon"></i>
                            </button>
                        </div>

                        <!-- textarea التلقائي والتكبير -->
                        <textarea id="userInput" class="form-control bg-transparent text-light border-0 shadow-none px-2 py-1"
                            placeholder="اكتب رسالتك أو الكود هنا..." rows="1" style="resize: none; max-height: 200px; overflow-y: auto;"
                            oninput="autoResize(this)"></textarea>

                        <!-- زر الإرسال والشريط السفلي -->


                    </div>

                    <div class="d-flex justify-content-start align-items-center gap-2 mt-2">
                        <div class="text-muted small"></div>
                        <button class="btn btn-danger font-weight-bold px-4 rounded-3" type="submit" id="sendBtn"
                            style="background-color: #f7665e; border-color: #f7665e;">
                            إرسال
                        </button>
                </form>
                <div class="text-end mt-2">
                    <button class="btn btn-sm btn-outline-danger font-weight-bold px-4 rounded-3  " onclick="clearChat()">
                        <i class="bi bi-trash"></i> مسح المحادثة
                    </button>
                </div>
            </div>
        </div>

    </div>
    </div>

    <script>
        // مصفوفة حفظ الذاكرة محلياً في المتصفح للجلسة الحالية
        let chatHistory = [];


        // ضبط التمدد التلقائي
        function autoResize(textarea) {
            const container = document.getElementById('inputBoxContainer');
            if (container.classList.contains('input-box-expanded')) return;
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        // خاصية التكبير والتصغير
        function toggleExpandInput() {
            const container = document.getElementById('inputBoxContainer');
            const icon = document.getElementById('expandIcon');
            const textarea = document.getElementById('userInput');

            container.classList.toggle('input-box-expanded');

            if (container.classList.contains('input-box-expanded')) {
                icon.className = 'bi bi-arrows-angle-contract fs-6';
            } else {
                icon.className = 'bi bi-arrows-angle-expand fs-6';
                autoResize(textarea);
            }
        }

        // الاختصار لعاد الضغط على Enter (إرسال) و Shift+Enter (سطر جديد)
        document.getElementById('userInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                handleSend(e);
            }
        });

        // إرسال البيانات عبر Axios
        function handleSend(e) {
            if (e) e.preventDefault();

            const inputArea = document.getElementById('userInput');
            const sendBtn = document.getElementById('sendBtn');
            const userText = inputArea.value.trim();

            if (!userText) return;

            // إذا كان وضع التكبير مفعل نلغيه عند الإرسال
            const container = document.getElementById('inputBoxContainer');
            if (container.classList.contains('input-box-expanded')) {
                toggleExpandInput();
            }

            appendUserMessage(userText);
            inputArea.value = '';
            inputArea.style.height = 'auto';

            sendBtn.disabled = true;
            const typingId = appendTypingIndicator();

            // إرسال السؤال مع الـ System Prompt ومصفوفة الذاكرة المحفوظة في المتصفح
            axios.post('{{ route('ai.assistant.analyze') }}', {
                    code: userText,
                    history: chatHistory
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    removeTypingIndicator(typingId);
                    sendBtn.disabled = false;

                    const data = response.data;
                    if (data.success) {
                        let responseText = '';
                        if (typeof data.data === 'string') {
                            responseText = data.data;
                        } else if (data.data.explanation || data.data.fixed_code) {
                            responseText = (data.data.explanation || '') + "\n\n" + (data.data.fixed_code ? "```php\n" +
                            data.data.fixed_code + "\n```" : '');
                        } else {
                            responseText = JSON.stringify(data.data, null, 2);
                        }

                        // تحديث مصفوفة الذاكرة المحلية (إضافة السؤال والإجابة)
                        chatHistory.push({
                            role: 'user',
                            content: userText
                        });
                        chatHistory.push({
                            role: 'assistant',
                            content: responseText
                        });

                        appendBotResponse(responseText);
                    } else {
                        appendBotMessage('حدث خطأ أثناء معالجة الطلب: ' + (data.message || 'خطأ غير معروف'));
                    }
                })
                .catch(error => {
                    removeTypingIndicator(typingId);
                    sendBtn.disabled = false;
                    appendBotMessage('فشل الاتصال بالخادم. يرجى التأكد من تشغيل الـ Webhook/n8n.');
                });
        }

        function appendUserMessage(text) {
            const messagesContainer = document.getElementById('chatMessages');
            const msgDiv = document.createElement('div');
            msgDiv.className = 'p-3 rounded-3 text-white align-self-start border border-primary';
            msgDiv.style.cssText =
                'background-color: #1f6feb; max-width: 80%; border-bottom-right-radius: 2px !important; word-break: break-word;';
            msgDiv.innerText = text;
            messagesContainer.appendChild(msgDiv);
            scrollToBottom();
        }

        function appendBotMessage(text) {
            const messagesContainer = document.getElementById('chatMessages');
            const msgDiv = document.createElement('div');
            msgDiv.className = 'p-3 rounded-3 align-self-end text-light border border-secondary';
            msgDiv.style.cssText =
                'background-color: #21262d; max-width: 90%; border-color: #30363d !important; border-bottom-left-radius: 2px !important; word-break: break-word;';
            msgDiv.innerText = text;
            messagesContainer.appendChild(msgDiv);
            scrollToBottom();
        }

        function appendBotResponse(markdownText) {
            const messagesContainer = document.getElementById('chatMessages');
            const msgDiv = document.createElement('div');
            msgDiv.className = 'p-3 rounded-3 align-self-end text-light border border-secondary message';
            msgDiv.style.cssText =
                'background-color: #21262d; width: 90%; border-color: #30363d !important; border-bottom-left-radius: 2px !important; word-break: break-word; opacity: 0; transition: opacity 0.3s;';

            messagesContainer.appendChild(msgDiv);

            msgDiv.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            let currentIndex = 0;
            const speed = 15;
            msgDiv.style.opacity = '1';

            function typeNextChunk() {
                if (currentIndex <= markdownText.length) {
                    const currentText = markdownText.slice(0, currentIndex);
                    msgDiv.innerHTML = marked.parse(currentText);
                    formatCodeBlocks(msgDiv);
                    Prism.highlightAllUnder(msgDiv);
                    currentIndex += 3;
                    setTimeout(typeNextChunk, speed);
                }
            }

            typeNextChunk();
        }

        function formatCodeBlocks(container) {
            container.querySelectorAll('pre').forEach((pre) => {
                if (pre.querySelector('.code-header')) return;

                const codeEl = pre.querySelector('code');
                let langName = 'CODE';
                if (codeEl) {
                    const match = codeEl.className.match(/language-(\w+)/);
                    if (match) langName = match[1];
                }

                const headerDiv = document.createElement('div');
                headerDiv.className = 'code-header';

                const langBadge = document.createElement('span');
                langBadge.className = 'lang-badge';
                langBadge.innerText = langName;

                const copyBtn = document.createElement('button');
                copyBtn.className = 'copy-code-btn';
                copyBtn.innerText = 'نسخ الكود';
                copyBtn.onclick = () => {
                    const code = codeEl ? codeEl.innerText : pre.innerText;
                    navigator.clipboard.writeText(code).then(() => {
                        copyBtn.innerText = 'تم النسخ!';
                        setTimeout(() => copyBtn.innerText = 'نسخ الكود', 2000);
                    });
                };

                headerDiv.appendChild(langBadge);
                headerDiv.appendChild(copyBtn);
                pre.appendChild(headerDiv);
            });
        }

        function appendTypingIndicator() {
            const messagesContainer = document.getElementById('chatMessages');
            const typingDiv = document.createElement('div');
            const id = 'typing_' + Date.now();
            typingDiv.id = id;
            typingDiv.className = 'p-2 px-3 rounded-3 align-self-end border border-secondary';
            typingDiv.style.cssText = 'background-color: #21262d; border-color: #30363d !important;';
            typingDiv.innerHTML = `
            <div class="d-flex gap-1 align-items-center py-1">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `;
            messagesContainer.appendChild(typingDiv);
            scrollToBottom();
            return id;
        }

        function removeTypingIndicator(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        function scrollToBottom() {
            const messagesContainer = document.getElementById('chatMessages');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    </script>
    <script>
        function clearChat() {
            if (confirm('هل تريد مسح سجل المحادثة الحالي؟')) {
                chatHistory = [];
                document.getElementById('chatMessages').innerHTML = `
            <div class="p-3 text-center text-light bg-opacity-10 bg-light rounded border border-secondary text-wrap mx-auto"
                style="max-width: 90%; border-color: #30363d !important; font-size: 14px;">
                تم بدء محادثة جديدة! أرسل سؤالك أو الكود البرمجي.
            </div>
        `;
            }
        }
    </script>
@endsection
