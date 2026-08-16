<?php
session_start();

$appointments = $_SESSION["appointments"] ?? [];

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}
function formatDateVN(string $date): string {
    $parts = explode("-", $date);
    return count($parts) === 3 ? $parts[2] . "/" . $parts[1] . "/" . $parts[0] : $date;
}

// Bộ lọc trạng thái bằng GET
$filter = $_GET["status"] ?? "Tất cả";
$filtered = [];

foreach ($appointments as $item) {
    if ($filter === "Tất cả" || $item["status"] === $filter) {
        $filtered[] = $item;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduMeet - Lịch của tôi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-shell">
    <header class="header compact">
        <a class="brand" href="index.php"><span class="brand-cap">◒</span> EduMeet</a>
        <nav class="nav">
            <a href="index.php">Trang chủ</a><a href="index.php">Đặt lịch</a><a href="index.php#lecturer">Giảng viên</a>
            <a class="active" href="appointments.php">Lịch của tôi</a><a href="appointments.php">Thông báo</a>
        </nav>
        <div class="profile"><div class="avatar">♙</div><div><b>Nguyễn Văn A</b><small>SV123456</small></div><span>⌄</span></div>
    </header>

    <section class="my-title">
        <h1>Lịch của tôi</h1>
        <p>Quản lý và theo dõi các lịch hẹn tư vấn của bạn</p>
        <div class="tabs">
            <?php
            $tabs = ["Tất cả", "Chờ xác nhận", "Đã xác nhận", "Đã hoàn thành", "Đã hủy"];
            foreach ($tabs as $tab):
            ?>
                <a class="<?= $filter === $tab ? 'selected' : '' ?>" href="?status=<?= urlencode($tab) ?>"><?= e($tab) ?></a>
            <?php endforeach; ?>
        </div>
    </section>

    <main class="my-layout">
        <aside class="list-panel">
            <div class="search"><span>⌕</span><input id="searchInput" type="text" placeholder="Tìm kiếm lịch hẹn..."><button>⚲</button></div>

            <?php if (!$filtered): ?>
                <div class="empty">Chưa có lịch hẹn phù hợp.<br><a href="index.php">Đặt lịch ngay</a></div>
            <?php endif; ?>

            <?php foreach ($filtered as $index => $item): ?>
                <div class="appointment-mini <?= $index === 0 ? 'chosen' : '' ?>" onclick="showAppointment(<?= $index ?>)">
                    <div class="date-box">
                        <b><?= e(date("d", strtotime($item["date"]))) ?></b>
                        <span>Thg <?= e(date("n", strtotime($item["date"]))) ?></span>
                        <strong><?= e(date("Y", strtotime($item["date"]))) ?></strong>
                    </div>
                    <div class="mini-content">
                        <b><?= e($item["subject"]) ?></b>
                        <span><?= e($item["lecturer"]) ?></span>
                        <span>◷ <?= e($item["time"]) ?></span>
                        <em><?= e($item["status"]) ?></em>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="pagination"><button>‹</button><button class="current">1</button><button>2</button><button>…</button><button>›</button></div>
        </aside>

        <section class="detail-panel" id="detailPanel">
            <?php if ($filtered): $item = $filtered[0]; ?>
                <div class="detail-head">
                    <div class="calendar-icon">▣</div>
                    <div><h2><?= e($item["subject"]) ?></h2><p>Mã lịch hẹn: <?= e($item["id"]) ?></p><p>Đặt vào: <?= e($item["created_at"]) ?></p></div>
                    <span class="detail-status"><?= e($item["status"]) ?></span>
                </div>

                <h2 class="section-label">THÔNG TIN LỊCH HẸN</h2>
                <div class="detail-grid">
                    <div><b>Mã lịch hẹn</b><span><?= e($item["id"]) ?></span></div>
                    <div><b>Họ và tên</b><span><?= e($item["name"]) ?></span></div>
                    <div><b>Mã số sinh viên</b><span><?= e($item["student_id"]) ?></span></div>
                    <div><b>Email</b><span><?= e($item["email"]) ?></span></div>
                    <div><b>Số điện thoại</b><span><?= e($item["phone"]) ?></span></div>
                    <div><b>Giảng viên</b><span class="blue"><?= e($item["lecturer"]) ?></span></div>
                    <div><b>Nội dung tư vấn</b><span><?= e($item["subject"]) ?></span></div>
                    <div><b>Hình thức gặp gỡ</b><span><?= e($item["meeting_type"]) ?></span></div>
                    <div><b>Ngày hẹn</b><span><?= e(formatDateVN($item["date"])) ?></span></div>
                    <div><b>Khung giờ</b><span><?= e($item["time"]) ?></span></div>
                    <div class="wide"><b>Nội dung chi tiết</b><span><?= e($item["detail"] ?: "Không có") ?></span></div>
                </div>

                <h2 class="section-label status-title">TRẠNG THÁI LỊCH HẸN</h2>
                <div class="timeline">
                    <div class="step done"><i>✓</i><span>Đã gửi yêu cầu</span><small><?= e($item["created_at"]) ?></small></div>
                    <div class="step current-step"><i>✓</i><span>Chờ xác nhận</span><small>Hiện tại</small></div>
                    <div class="step"><i>✓</i><span>Đã xác nhận</span></div>
                    <div class="step"><i>✓</i><span>Hoàn thành</span></div>
                </div>

                <div class="detail-actions">
                    <button class="danger">♙ Hủy lịch hẹn</button>
                    <button class="edit">✎ Sửa lịch hẹn</button>
                    <button class="chat">▣ Nhắn tin với giảng viên</button>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <div class="note">ⓘ <b>Lưu ý:</b> Bạn sẽ nhận được email thông báo khi lịch hẹn được giảng viên xác nhận hoặc có thay đổi.</div>

    <footer class="footer">
        <span>⚙ Cài đặt</span><span>© EduMeet. All rights reserved.</span><span>Chính sách bảo mật</span><span>Điều khoản dịch vụ</span><span>Hỗ trợ</span><span>Liên hệ</span>
    </footer>
</div>

<script>
function showAppointment(index) {
    const cards = document.querySelectorAll('.appointment-mini');
    cards.forEach(c => c.classList.remove('chosen'));
    if (cards[index]) cards[index].classList.add('chosen');
}
document.getElementById('searchInput')?.addEventListener('input', function() {
    const keyword = this.value.toLowerCase();
    document.querySelectorAll('.appointment-mini').forEach(card => {
        card.style.display = card.innerText.toLowerCase().includes(keyword) ? '' : 'none';
    });
});
</script>
</body>
</html>
