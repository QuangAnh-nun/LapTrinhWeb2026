<?php
session_start();

/*
 * EduMeet - Trang đặt lịch tư vấn/hẹn gặp giảng viên
 * Bài cá nhân môn Lập trình Web - PHP chạy trên XAMPP.
 */

// Dữ liệu mẫu được tổ chức bằng mảng
$lecturers = [
    ["id" => 1, "name" => "TS. Nguyễn Minh Tâm", "email" => "nguyenminhtam@edumeet.vn", "specialty" => "Đồ án / Khóa luận"],
    ["id" => 2, "name" => "ThS. Trần Thị Mai", "email" => "tranthimai@edumeet.vn", "specialty" => "Học phần"],
    ["id" => 3, "name" => "TS. Lê Hoàng Nam", "email" => "lehoangnam@edumeet.vn", "specialty" => "Định hướng nghề nghiệp"],
    ["id" => 4, "name" => "TS. Phạm Quốc Bình", "email" => "phamquocbinh@edumeet.vn", "specialty" => "Chuyên đề"]
];

$timeSlots = [
    "08:00 - 09:00",
    "09:00 - 10:00",
    "10:00 - 11:00",
    "13:00 - 14:00",
    "14:00 - 15:00",
    "15:00 - 16:00"
];

$subjects = [
    "Đồ án / Khóa luận",
    "Tư vấn học phần",
    "Tư vấn định hướng nghề nghiệp",
    "Tư vấn chuyên đề"
];

$errors = [];

// Hàm tự định nghĩa: kiểm tra email hợp lệ
function kiemTraEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Hàm tự định nghĩa: tìm tên giảng viên theo mã
function layTenGiangVien(array $lecturers, int $id): string {
    foreach ($lecturers as $lecturer) {
        if ((int)$lecturer["id"] === $id) {
            return $lecturer["name"];
        }
    }
    return "Chưa chọn";
}

// Nhận và xử lý dữ liệu khi người dùng gửi form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $studentId = trim($_POST["student_id"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $lecturerId = (int)($_POST["lecturer"] ?? 0);
    $date = trim($_POST["date"] ?? "");
    $time = trim($_POST["time"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $detail = trim($_POST["detail"] ?? "");

    // Điều kiện kiểm tra dữ liệu
    if ($name === "") $errors[] = "Vui lòng nhập họ và tên.";
    if ($studentId === "") $errors[] = "Vui lòng nhập mã số sinh viên.";
    if (!kiemTraEmail($email)) $errors[] = "Email không hợp lệ.";
    if ($phone === "") $errors[] = "Vui lòng nhập số điện thoại.";
    if ($lecturerId <= 0) $errors[] = "Vui lòng chọn giảng viên.";
    if ($date === "") $errors[] = "Vui lòng chọn ngày hẹn.";
    if ($time === "") $errors[] = "Vui lòng chọn khung giờ.";
    if ($subject === "") $errors[] = "Vui lòng nhập tiêu đề.";

    // Không cho chọn ngày trong quá khứ
    if ($date !== "" && $date < date("Y-m-d")) {
        $errors[] = "Ngày hẹn không được ở trong quá khứ.";
    }

    if (count($errors) === 0) {
        $appointment = [
            "id" => "LH" . date("His") . rand(10, 99),
            "name" => $name,
            "student_id" => $studentId,
            "email" => $email,
            "phone" => $phone,
            "lecturer_id" => $lecturerId,
            "lecturer" => layTenGiangVien($lecturers, $lecturerId),
            "date" => $date,
            "time" => $time,
            "subject" => $subject,
            "detail" => $detail,
            "meeting_type" => "Trực tiếp",
            "status" => "Chờ xác nhận",
            "created_at" => date("d/m/Y - H:i")
        ];

        // Lưu lịch hẹn vào session để có thể xem ở trang "Lịch của tôi"
        if (!isset($_SESSION["appointments"])) {
            $_SESSION["appointments"] = [];
        }
        $_SESSION["appointments"][] = $appointment;

        $_SESSION["latest_appointment"] = $appointment;
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
    <title>EduMeet - Đặt lịch</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-shell">
    <header class="header">
        <a class="brand" href="index.php"><span class="brand-cap">◒</span> EduMeet</a>
        <nav class="nav">
            <a href="index.php">Trang chủ</a>
            <a class="active" href="index.php">Đặt lịch</a>
            <a href="index.php#lecturer">Giảng viên</a>
            <a href="appointments.php">Lịch của tôi</a>
            <a href="appointments.php">Thông báo</a>
        </nav>
        <div class="profile">
            <div class="avatar">♙</div>
            <div><b>Nguyễn Văn A</b><small>SV123456</small></div>
            <span>⌄</span>
        </div>
    </header>

    <main class="layout">
        <aside class="sidebar-card">
            <div class="illustration">👨🏻‍💻　💬　👩🏻‍💼<br><span>　　💻　　🕘</span></div>
            <h3>Lợi ích khi đặt lịch</h3>
            <div class="benefit"><span>☑</span><div><b>Chủ động thời gian</b><p>Chọn khung giờ phù hợp với lịch của bạn.</p></div></div>
            <div class="benefit"><span>⌕</span><div><b>Tư vấn hiệu quả</b><p>Trao đổi trực tiếp với giảng viên về học tập, đồ án,...</p></div></div>
            <div class="benefit"><span>♧</span><div><b>Nhắc lịch tự động</b><p>Hệ thống sẽ gửi thông báo nhắc lịch trước buổi hẹn.</p></div></div>
            <div class="quote">“<br><strong>Đặt lịch dễ dàng<br> Kết nối tri thức</strong><br>”</div>
        </aside>

        <section class="form-card">
            <div class="form-title">
                <div class="title-icon">▣</div>
                <div><h2>ĐẶT LỊCH TƯ VẤN/HẸN GẶP GIẢNG VIÊN</h2><p>Vui lòng điền đầy đủ thông tin để đặt lịch hẹn</p></div>
            </div>

            <?php if ($errors): ?>
                <div class="alert error">
                    <b>Vui lòng kiểm tra lại:</b>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php">
                <div class="form-grid">
                    <label>Họ và tên <em>*</em>
                        <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Nguyễn Văn A" required>
                    </label>

                    <label>Mã số sinh viên <em>*</em>
                        <input type="text" name="student_id" value="<?= htmlspecialchars($_POST['student_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="SV123456" required>
                    </label>

                    <label>Email <em>*</em>
                        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="nguyenvana@gmail.com" required>
                    </label>

                    <label>Số điện thoại <em>*</em>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="0123 456 789" required>
                    </label>

                    <label class="full">Chọn giảng viên <em>*</em>
                        <select name="lecturer" required>
                            <option value="">--- Chọn giảng viên ---</option>
                            <?php foreach ($lecturers as $lecturer): ?>
                                <option value="<?= $lecturer['id'] ?>" <?= (($_POST['lecturer'] ?? '') == $lecturer['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($lecturer['name'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($lecturer['specialty'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>Ngày hẹn <em>*</em>
                        <input type="date" name="date" value="<?= htmlspecialchars($_POST['date'] ?? '', ENT_QUOTES, 'UTF-8') ?>" min="<?= date('Y-m-d') ?>" required>
                    </label>

                    <label>Khung giờ <em>*</em>
                        <select name="time" required>
                            <option value="">--- Chọn khung giờ ---</option>
                            <?php foreach ($timeSlots as $slot): ?>
                                <option value="<?= htmlspecialchars($slot, ENT_QUOTES, 'UTF-8') ?>" <?= (($_POST['time'] ?? '') === $slot) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($slot, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="full">Tiêu đề <em>*</em>
                        <select name="subject" required>
                            <option value="">--- Nhập tiêu đề ---</option>
                            <?php foreach ($subjects as $item): ?>
                                <option value="<?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?>" <?= (($_POST['subject'] ?? '') === $item) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="full">Nội dung chi tiết (nếu có)
                        <textarea name="detail" maxlength="300" placeholder="Bạn có thể nhập thêm thông tin chi tiết về nội dung cần tư vấn..."><?= htmlspecialchars($_POST['detail'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn btn-yellow">Nhập lại</button>
                    <button type="submit" class="btn btn-primary">▣ &nbsp; Đặt lịch ngay</button>
                </div>
            </form>
        </section>
    </main>

    <div class="note">ⓘ <b>Lưu ý:</b> Vui lòng kiểm tra kỹ thông tin trước khi đặt lịch. Bạn sẽ nhận được email xác nhận sau khi đặt lịch thành công.</div>

    <footer class="footer">
        <span>⚙ Cài đặt</span>
        <span>© EduMeet. All rights reserved.</span>
        <span>Chính sách bảo mật</span>
        <span>Điều khoản dịch vụ</span>
        <span>Hỗ trợ</span>
        <span>Liên hệ</span>
    </footer>
</div>
</body>
</html>
