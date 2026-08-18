<?php
declare(strict_types=1);
session_start();

/*
 * EduMeet - Trang đặt lịch tư vấn/hẹn gặp giảng viên
 * Bài cá nhân môn Lập trình Web - PHP chạy trên XAMPP.
 */

$lecturers = [
    ["id" => 1, "name" => "TS. Nguyễn Minh Tâm", "email" => "nguyenminhtam@edumeet.vn", "specialty" => "Đồ án / Khóa luận"],
    ["id" => 2, "name" => "ThS. Trần Thị Mai", "email" => "tranthimai@edumeet.vn", "specialty" => "Học phần"],
    ["id" => 3, "name" => "TS. Lê Hoàng Nam", "email" => "lehoangnam@edumeet.vn", "specialty" => "Định hướng nghề nghiệp"],
    ["id" => 4, "name" => "TS. Phạm Quốc Bình", "email" => "phamquocbinh@edumeet.vn", "specialty" => "Chuyên đề"]
];

$timeSlots = [
    "08:00 - 09:00", "09:00 - 10:00", "10:00 - 11:00",
    "13:00 - 14:00", "14:00 - 15:00", "15:00 - 16:00"
];

$subjects = [
    "Đồ án / Khóa luận",
    "Tư vấn học phần",
    "Tư vấn định hướng nghề nghiệp",
    "Tư vấn chuyên đề"
];

$errors = [];
$old = [
    "name" => "", "student_id" => "", "email" => "", "phone" => "",
    "lecturer" => "", "date" => "", "time" => "", "subject" => "", "detail" => ""
];

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

function normalizeText(string $value): string {
    $value = trim($value);
    $value = preg_replace('/[ \t]+/u', ' ', $value) ?? $value;
    return $value;
}

function kiemTraEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function layTenGiangVien(array $lecturers, int $id): string {
    foreach ($lecturers as $lecturer) {
        if ((int)$lecturer["id"] === $id) {
            return $lecturer["name"];
        }
    }
    return "";
}

function lecturerExists(array $lecturers, int $id): bool {
    foreach ($lecturers as $lecturer) {
        if ((int)$lecturer["id"] === $id) {
            return true;
        }
    }
    return false;
}

function validDate(string $date): bool {
    $d = DateTime::createFromFormat("Y-m-d", $date);
    return $d !== false && $d->format("Y-m-d") === $date;
}

/* Tạo CSRF token để chống việc gửi form giả mạo từ website khác. */
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $old["name"] = normalizeText((string)($_POST["name"] ?? ""));
    $old["student_id"] = strtoupper(normalizeText((string)($_POST["student_id"] ?? "")));
    $old["email"] = strtolower(normalizeText((string)($_POST["email"] ?? "")));
    $old["phone"] = preg_replace('/\D+/', '', (string)($_POST["phone"] ?? "")) ?? "";
    $old["lecturer"] = (string)($_POST["lecturer"] ?? "");
    $old["date"] = (string)($_POST["date"] ?? "");
    $old["time"] = (string)($_POST["time"] ?? "");
    $old["subject"] = normalizeText((string)($_POST["subject"] ?? ""));
    $old["detail"] = normalizeText((string)($_POST["detail"] ?? ""));

    /* 1. Kiểm tra CSRF trước khi xử lý dữ liệu. */
    $csrf = (string)($_POST["csrf_token"] ?? "");
    if (!hash_equals((string)$_SESSION["csrf_token"], $csrf)) {
        $errors[] = "Phiên gửi biểu mẫu không hợp lệ. Vui lòng tải lại trang và thử lại.";
    }

    /* 2. Validation phía server - không tin dữ liệu từ trình duyệt. */
    if ($old["name"] === "") {
        $errors[] = "Vui lòng nhập họ và tên.";
    } elseif (mb_strlen($old["name"]) < 2 || mb_strlen($old["name"]) > 80) {
        $errors[] = "Họ và tên phải từ 2 đến 80 ký tự.";
    }

    if (!preg_match('/^[A-Z0-9]{6,15}$/', $old["student_id"])) {
        $errors[] = "Mã số sinh viên chỉ gồm chữ và số, dài từ 6 đến 15 ký tự.";
    }

    if (!kiemTraEmail($old["email"]) || mb_strlen($old["email"]) > 120) {
        $errors[] = "Email không hợp lệ.";
    }

    if (!preg_match('/^(0|\+84)[0-9]{9,10}$/', $old["phone"])) {
        $errors[] = "Số điện thoại không hợp lệ.";
    }

    $lecturerId = filter_var($old["lecturer"], FILTER_VALIDATE_INT);
    if ($lecturerId === false || $lecturerId <= 0 || !lecturerExists($lecturers, (int)$lecturerId)) {
        $errors[] = "Giảng viên được chọn không hợp lệ.";
    }

    if (!validDate($old["date"])) {
        $errors[] = "Ngày hẹn không hợp lệ.";
    } elseif ($old["date"] < date("Y-m-d")) {
        $errors[] = "Ngày hẹn không được ở trong quá khứ.";
    }

    if (!in_array($old["time"], $timeSlots, true)) {
        $errors[] = "Khung giờ được chọn không hợp lệ.";
    }

    if (!in_array($old["subject"], $subjects, true)) {
        $errors[] = "Chủ đề tư vấn được chọn không hợp lệ.";
    }

    if (mb_strlen($old["detail"]) > 300) {
        $errors[] = "Nội dung chi tiết không được vượt quá 300 ký tự.";
    }

    if (count($errors) === 0) {
        $appointment = [
            "id" => "LH" . date("YmdHis") . random_int(10, 99),
            "name" => $old["name"],
            "student_id" => $old["student_id"],
            "email" => $old["email"],
            "phone" => $old["phone"],
            "lecturer_id" => (int)$lecturerId,
            "lecturer" => layTenGiangVien($lecturers, (int)$lecturerId),
            "date" => $old["date"],
            "time" => $old["time"],
            "subject" => $old["subject"],
            "detail" => $old["detail"],
            "meeting_type" => "Trực tiếp",
            "status" => "Chờ xác nhận",
            "created_at" => date("d/m/Y - H:i")
        ];

        $_SESSION["appointments"] ??= [];
        $_SESSION["appointments"][] = $appointment;
        $_SESSION["latest_appointment"] = $appointment;

        /* Đổi token sau khi submit thành công để hạn chế replay request. */
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

        header("Location: success.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EduMeet - Đặt lịch tư vấn và hẹn gặp giảng viên">
    <title>EduMeet - Đặt lịch</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-shell">
    <header class="header">
        <a class="brand" href="index.php"><span class="brand-cap">◒</span> EduMeet</a>
        <nav class="nav">
            <a class="active" href="index.php">Đặt lịch</a>
            <a href="index.php#lecturer">Giảng viên</a>
            <a href="appointments.php">Lịch của tôi</a>
            <a href="appointments.php">Thông báo</a>
        </nav>
        <div class="profile">
            <div class="avatar">♙</div>
            <div><b>Nguyễn Văn A</b><small>SV123456</small></div>
        </div>
    </header>

    <main class="layout">
        <aside class="sidebar-card">
            <div class="illustration">👨🏻‍💻　💬　👩🏻‍💼<br><span>　　💻　　🕘</span></div>
            <h3>Lợi ích khi đặt lịch</h3>
            <div class="benefit"><span>✓</span><div><b>Chủ động thời gian</b><p>Chọn khung giờ phù hợp với lịch của bạn.</p></div></div>
            <div class="benefit"><span>⌕</span><div><b>Tư vấn hiệu quả</b><p>Trao đổi trực tiếp với giảng viên về học tập, đồ án,...</p></div></div>
            <div class="benefit"><span>♧</span><div><b>Nhắc lịch tự động</b><p>Hệ thống sẽ gửi thông báo nhắc lịch trước buổi hẹn.</p></div></div>
            <div class="quote">“<br><strong>Đặt lịch dễ dàng<br>Kết nối tri thức</strong><br>”</div>
        </aside>

        <section class="form-card">
            <div class="form-title">
                <div class="title-icon">▣</div>
                <div><h2>ĐẶT LỊCH TƯ VẤN / HẸN GẶP GIẢNG VIÊN</h2><p>Vui lòng điền đầy đủ thông tin để đặt lịch hẹn</p></div>
            </div>

            <?php if ($errors): ?>
                <div class="alert error" role="alert">
                    <b>Vui lòng kiểm tra lại:</b>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php" id="appointmentForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e((string)$_SESSION["csrf_token"]) ?>">

                <div class="form-grid">
                    <label>Họ và tên <em>*</em>
                        <input type="text" name="name" id="name" value="<?= e($old["name"]) ?>"
                               placeholder="Nguyễn Văn A" minlength="2" maxlength="80"
                               autocomplete="name" required>
                        <small class="field-error" id="nameError"></small>
                    </label>

                    <label>Mã số sinh viên <em>*</em>
                        <input type="text" name="student_id" id="student_id" value="<?= e($old["student_id"]) ?>"
                               placeholder="SV123456" minlength="6" maxlength="15"
                               pattern="[A-Za-z0-9]{6,15}" required>
                        <small class="field-error" id="studentIdError"></small>
                    </label>

                    <label>Email <em>*</em>
                        <input type="email" name="email" id="email" value="<?= e($old["email"]) ?>"
                               placeholder="nguyenvana@gmail.com" maxlength="120"
                               autocomplete="email" required>
                        <small class="field-error" id="emailError"></small>
                    </label>

                    <label>Số điện thoại <em>*</em>
                        <input type="tel" name="phone" id="phone" value="<?= e($old["phone"]) ?>"
                               placeholder="0123456789" maxlength="11" inputmode="numeric"
                               autocomplete="tel" required>
                        <small class="field-error" id="phoneError"></small>
                    </label>

                    <label class="full">Chọn giảng viên <em>*</em>
                        <select name="lecturer" id="lecturer" required>
                            <option value="">--- Chọn giảng viên ---</option>
                            <?php foreach ($lecturers as $lecturer): ?>
                                <option value="<?= (int)$lecturer['id'] ?>" <?= $old["lecturer"] == (string)$lecturer['id'] ? 'selected' : '' ?>>
                                    <?= e($lecturer['name']) ?> — <?= e($lecturer['specialty']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>Ngày hẹn <em>*</em>
                        <input type="date" name="date" id="date" value="<?= e($old["date"]) ?>"
                               min="<?= e(date('Y-m-d')) ?>" required>
                        <small class="field-error" id="dateError"></small>
                    </label>

                    <label>Khung giờ <em>*</em>
                        <select name="time" id="time" required>
                            <option value="">--- Chọn khung giờ ---</option>
                            <?php foreach ($timeSlots as $slot): ?>
                                <option value="<?= e($slot) ?>" <?= $old["time"] === $slot ? 'selected' : '' ?>><?= e($slot) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="full">Tiêu đề <em>*</em>
                        <select name="subject" id="subject" required>
                            <option value="">--- Chọn chủ đề ---</option>
                            <?php foreach ($subjects as $item): ?>
                                <option value="<?= e($item) ?>" <?= $old["subject"] === $item ? 'selected' : '' ?>><?= e($item) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="full">Nội dung chi tiết (nếu có)
                        <textarea name="detail" id="detail" maxlength="300"
                                  placeholder="Bạn có thể nhập thêm thông tin chi tiết..."><?= e($old["detail"]) ?></textarea>
                        <small class="counter"><span id="detailCount"><?= mb_strlen($old["detail"]) ?></span>/300</small>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn btn-yellow">Nhập lại</button>
                    <button type="submit" class="btn btn-primary">▣ &nbsp; Đặt lịch ngay</button>
                </div>
            </form>
        </section>
    </main>

    <div class="note">ⓘ <b>Lưu ý:</b> Dữ liệu được kiểm tra ở cả trình duyệt và máy chủ. Vui lòng kiểm tra kỹ thông tin trước khi đặt lịch.</div>

    <footer class="footer">
        <span>© EduMeet. All rights reserved.</span>
        <span>Chính sách bảo mật</span><span>Điều khoản dịch vụ</span><span>Hỗ trợ</span>
    </footer>
</div>

<script>
(() => {
    const form = document.getElementById('appointmentForm');
    const detail = document.getElementById('detail');
    const counter = document.getElementById('detailCount');

    const setError = (id, message) => {
        const el = document.getElementById(id);
        if (el) el.textContent = message;
    };

    const clearErrors = () => {
        document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
        document.querySelectorAll('.input-invalid').forEach(el => el.classList.remove('input-invalid'));
    };

    const mark = (input, errorId, message) => {
        input.classList.add('input-invalid');
        setError(errorId, message);
    };

    const updateCounter = () => {
        if (detail && counter) counter.textContent = detail.value.length;
    };

    detail?.addEventListener('input', updateCounter);
    updateCounter();

    document.getElementById('student_id')?.addEventListener('input', e => {
        e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });

    document.getElementById('phone')?.addEventListener('input', e => {
        e.target.value = e.target.value.replace(/[^\d+]/g, '').slice(0, 11);
    });

    form?.addEventListener('submit', event => {
        clearErrors();
        let valid = true;

        const name = document.getElementById('name');
        const studentId = document.getElementById('student_id');
        const email = document.getElementById('email');
        const phone = document.getElementById('phone');
        const lecturer = document.getElementById('lecturer');
        const date = document.getElementById('date');
        const time = document.getElementById('time');
        const subject = document.getElementById('subject');

        if (name.value.trim().length < 2 || name.value.trim().length > 80) {
            mark(name, 'nameError', 'Họ và tên phải từ 2 đến 80 ký tự.');
            valid = false;
        }

        if (!/^[A-Za-z0-9]{6,15}$/.test(studentId.value.trim())) {
            mark(studentId, 'studentIdError', 'Mã sinh viên chỉ gồm chữ và số, 6-15 ký tự.');
            valid = false;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim()) || email.value.trim().length > 120) {
            mark(email, 'emailError', 'Email không hợp lệ.');
            valid = false;
        }

        if (!/^(0|\+84)\d{9,10}$/.test(phone.value.trim())) {
            mark(phone, 'phoneError', 'Số điện thoại không hợp lệ.');
            valid = false;
        }

        const today = new Date().toISOString().split('T')[0];
        if (!date.value || date.value < today) {
            mark(date, 'dateError', 'Vui lòng chọn ngày hôm nay hoặc ngày trong tương lai.');
            valid = false;
        }

        if (detail.value.length > 300) {
            valid = false;
        }

        if (!valid) {
            event.preventDefault();
            document.querySelector('.input-invalid')?.focus();
        }
    });
})();
</script>
</body>
</html>
