<div class="course-chat" data-course-chat data-course-id="{{ $course->id }}" data-messages-url="{{ route('courses.chat.index', $course) }}" data-send-url="{{ route('courses.chat.store', $course) }}" data-current-user-id="{{ $user->id }}" data-user-role="{{ $user->role ?? 'student' }}" data-is-teacher="{{ (int)$course->teacher_id === (int)$user->id ? '1' : '0' }}">
    <div class="course-chat__header">
        <div>
            <h5><i class="bi bi-chat-heart-fill"></i> Course conversation</h5>
            <p>Chat live with your instructor and classmates.</p>
        </div>
        <span class="course-chat__online"><span class="course-chat__dot"></span><strong data-online-count>0</strong> online</span>
    </div>
    <div class="course-chat__pinned-bar" data-pinned-bar style="display: none;">
        <div class="course-chat__pinned-content">
            <i class="bi bi-pin-angle-fill course-chat__pinned-icon"></i>
            <span class="course-chat__pinned-label">Pinned Message:</span>
            <span class="course-chat__pinned-text" data-pinned-text></span>
        </div>
        <button type="button" class="course-chat__pinned-jump-btn" data-pinned-jump title="Jump to message">
            <i class="bi bi-arrow-down-circle"></i> Jump
        </button>
    </div>
    <div class="course-chat__body">
        <aside class="course-chat__members">
            <div class="course-chat__members-title">Online now</div>
            <div class="course-chat__members-list" data-online-members></div>
        </aside>
        <div class="course-chat__conversation">
            <div class="course-chat__status" data-chat-status>Connecting to live chat…</div>
            <div class="course-chat__messages" data-chat-messages aria-live="polite"></div>
            <form class="course-chat__form" data-chat-form>
                <textarea data-chat-input rows="1" maxlength="2000" placeholder="Write a message…" aria-label="Message"></textarea>
                <button type="submit" data-chat-submit aria-label="Send message"><i class="bi bi-send-fill"></i></button>
            </form>
        </div>
    </div>
</div>

<style>
.course-chat{background:#fff;border:1px solid #e5e7eb;border-radius:18px;overflow:hidden;box-shadow:0 8px 30px rgba(15,23,42,.06);position:relative}
.course-chat__header{display:flex;justify-content:space-between;gap:16px;align-items:center;padding:20px 22px;border-bottom:1px solid #eef2f7;background:linear-gradient(135deg,#f8fbff,#f5f3ff)}
.course-chat__header h5{margin:0;color:#1e293b;font-size:1rem;font-weight:700}.course-chat__header h5 i{color:#6366f1;margin-right:7px}.course-chat__header p{margin:5px 0 0;color:#64748b;font-size:.82rem}
.course-chat__online{display:inline-flex;align-items:center;gap:7px;white-space:nowrap;font-size:.78rem;color:#475569;background:#fff;padding:7px 10px;border:1px solid #e2e8f0;border-radius:999px}.course-chat__dot{width:8px;height:8px;background:#22c55e;border-radius:50%;box-shadow:0 0 0 3px #dcfce7}

.course-chat__pinned-bar{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:9px 18px;background:linear-gradient(135deg,#fffbeb,#fef3c7);border-bottom:1px solid #fde68a;font-size:.81rem;color:#92400e}
.course-chat__pinned-content{display:flex;align-items:center;gap:6px;min-width:0;overflow:hidden}
.course-chat__pinned-icon{color:#d97706;font-size:.95rem;flex-shrink:0}
.course-chat__pinned-label{font-weight:700;white-space:nowrap;flex-shrink:0}
.course-chat__pinned-text{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#78350f;font-weight:500}
.course-chat__pinned-jump-btn{background:#fef3c7;border:1px solid #fde68a;color:#b45309;font-size:.74rem;font-weight:700;padding:3px 10px;border-radius:999px;cursor:pointer;display:inline-flex;align-items:center;gap:4px;transition:all .15s ease;flex-shrink:0}
.course-chat__pinned-jump-btn:hover{background:#fde68a;color:#78350f}

.course-chat__body{display:flex;min-height:430px}.course-chat__members{width:200px;flex:none;padding:16px 12px;border-right:1px solid #eef2f7;background:#fafcff}.course-chat__members-title{font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;font-weight:700;padding:0 7px 10px}.course-chat__members-list{display:flex;flex-direction:column;gap:5px}.course-chat__member{display:flex;align-items:center;gap:8px;padding:7px;border-radius:999px;min-width:0}.course-chat__member-avatar{width:28px;height:28px;border-radius:50%;object-fit:cover;background:#e0e7ff;flex:none}.course-chat__member-avatar--letter{display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;color:#fff;background:linear-gradient(135deg,#6366f1,#818cf8)}.course-chat__member-info{display:flex;flex-direction:column;gap:2px;min-width:0}.course-chat__member-name-row{display:flex;align-items:center;gap:5px;flex-wrap:wrap}.course-chat__member-name{font-size:.78rem;color:#334155;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.course-chat__badge{display:inline-flex;align-items:center;font-size:.6rem;font-weight:700;padding:1px 6px;border-radius:999px;white-space:nowrap;line-height:1.5}.course-chat__badge--teacher{background:#dcfce7;color:#16a34a}.course-chat__badge--online{background:#dcfce7;color:#16a34a;display:inline-flex;align-items:center;gap:3px}.course-chat__badge--online::before{content:'';width:6px;height:6px;background:#22c55e;border-radius:50%}

.course-chat__conversation{display:flex;flex:1;min-width:0;flex-direction:column}.course-chat__status{min-height:32px;padding:9px 17px;color:#64748b;font-size:.76rem;border-bottom:1px solid #f1f5f9}.course-chat__messages{flex:1;height:330px;overflow-y:auto;padding:18px;display:flex;flex-direction:column;gap:14px;background:#fff}.course-chat__empty{margin:auto;color:#94a3b8;text-align:center;font-size:.85rem}

.course-chat__message{display:flex;gap:8px;max-width:85%;align-items:flex-start;position:relative;group:1}
.course-chat__message--mine{align-self:flex-end;flex-direction:row-reverse}
.course-chat__message-wrapper{position:relative;display:flex;flex-direction:column;min-width:0}

.course-chat__avatar{width:32px;height:32px;border-radius:50%;object-fit:cover;background:#e0e7ff;flex:none}.course-chat__avatar--letter{display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;color:#fff;background:linear-gradient(135deg,#6366f1,#818cf8)}

.course-chat__bubble-container{position:relative;display:inline-block}
.course-chat__bubble{padding:9px 12px;background:#f1f5f9;border-radius:4px 13px 13px;color:#334155;font-size:.84rem;line-height:1.45;word-break:break-word;position:relative;transition:all .15s ease}
.course-chat__message--mine .course-chat__bubble{background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;border-radius:13px 4px 13px 13px}
.course-chat__bubble--pinned{border:1.5px solid #f59e0b !important;box-shadow:0 0 0 3px rgba(245,158,11,.15) !important}

.course-chat__pinned-indicator{display:inline-flex;align-items:center;gap:3px;font-size:.68rem;font-weight:700;color:#d97706;background:#fef3c7;padding:2px 7px;border-radius:999px;margin-left:6px}
.course-chat__message--mine .course-chat__pinned-indicator{background:rgba(254,243,199,.25);color:#fef08a}

.course-chat__edited-tag{font-size:.68rem;opacity:.75;font-style:italic;margin-left:4px}

.course-chat__meta{display:flex;gap:7px;align-items:center;margin-bottom:3px;font-size:.69rem;color:#94a3b8}.course-chat__message--mine .course-chat__meta{justify-content:flex-end}.course-chat__name{font-weight:700;color:#64748b}.course-chat__message--mine .course-chat__name{color:#818cf8}

/* Action Toolbar on Message Hover */
.course-chat__actions{position:absolute;top:-14px;right:0;display:none;align-items:center;gap:2px;background:#fff;border:1px solid #e2e8f0;box-shadow:0 4px 12px rgba(15,23,42,.1);border-radius:999px;padding:2px 5px;z-index:10}
.course-chat__message--mine .course-chat__actions{right:auto;left:0}
.course-chat__message:hover .course-chat__actions{display:flex}
.course-chat__action-btn{background:none;border:none;color:#64748b;font-size:.78rem;padding:3px 6px;border-radius:999px;cursor:pointer;transition:all .15s ease;display:flex;align-items:center;justify-content:center}
.course-chat__action-btn:hover{color:#4f46e5;background:#f1f5f9}
.course-chat__action-btn--delete:hover{color:#ef4444;background:#fef2f2}
.course-chat__action-btn--pin.is-active{color:#d97706;background:#fef3c7}

/* Edit Form */
.course-chat__edit-box{display:flex;flex-direction:column;gap:6px;width:100%;min-width:220px;margin-top:2px}
.course-chat__edit-input{width:100%;border:1px solid #818cf8;border-radius:9px;padding:7px 10px;font-size:.84rem;outline:none;background:#fff;color:#1e293b}
.course-chat__edit-actions{display:flex;gap:6px;justify-content:flex-end}
.course-chat__edit-btn{padding:3px 10px;font-size:.74rem;font-weight:600;border-radius:6px;border:none;cursor:pointer}
.course-chat__edit-btn--save{background:#4f46e5;color:#fff}.course-chat__edit-btn--save:hover{background:#4338ca}
.course-chat__edit-btn--cancel{background:#e2e8f0;color:#475569}.course-chat__edit-btn--cancel:hover{background:#cbd5e1}

.course-chat__form{display:flex;gap:10px;padding:12px 15px;border-top:1px solid #eef2f7;background:#fafcff}.course-chat__form textarea{resize:none;min-height:42px;max-height:108px;flex:1;border:1px solid #dbe3ef;border-radius:11px;padding:10px 12px;font-size:.85rem;outline:none}.course-chat__form textarea:focus{border-color:#818cf8;box-shadow:0 0 0 3px #eef2ff}.course-chat__form button{width:42px;border:0;border-radius:11px;background:#4f46e5;color:#fff;transition:.2s}.course-chat__form button:disabled{opacity:.55}.course-chat__form button:hover:not(:disabled){background:#4338ca}
@media(max-width:700px){.course-chat__body{min-height:390px}.course-chat__members{display:none}.course-chat__header{padding:16px}.course-chat__messages{height:300px}.course-chat__header p{display:none}}
</style>
