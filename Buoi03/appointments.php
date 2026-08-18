<?php
declare(strict_types=1);
session_start();

$appointments = $_SESSION["appointments"] ?? [];

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

function formatDateVN(string $date): string {
    $d = DateTime::createFromFormat("Y-m-d", $date);
    return $d ? $d->format("d/m/Y") : $date;
}

$allowedFilters = ["Tất cả", "Chờ xác nhận", "Đã xác nhận", "Đã hoàn thành", "Đã hủy"];
$filter = (string)($_GET["status"] ?? "Tất cả");
if (!in_array($filter, $allowedFilters, true)) {
    $filter = "Tất cả";
}

$filtered = [];
foreach ($appointments as $item) {
    if (is_array($item) && ($filter === "Tất cả" || ($item["status"] ?? "") === $filter)) {
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
        <a href="index.php">Đặt lịch</a><a href="index.php#lecturer">Giảng viên</a>
        <a class="active" href="appointments.php">Lịch của tôi</a><a href="appointments.php">Thông báo</a>
    </nav>
    <div class="profile"><div class="avatar">♙</div><div><b>Nguyễn Văn A</b><small>SV123456</small></div></div>
</header>

<section class="my-title">
    <h1>Lịch của tôi</h1>
    <p>Quản lý và theo dõi các lịch hẹn tư vấn của bạn</p>
    <div class="tabs">
        <?php foreach ($allowedFilters as $tab): ?>
            <a class="<?= $filter === $tab ? 'selected' : '' ?>" href="?status=<?= urlencode($tab) ?>"><?= e($tab) ?></a>
        <?php endforeach; ?>
    </div>
</section>

<main class="my-layout">
    <aside class="list-panel">
        <div class="search">
            <span>⌕</span>
            <input id="searchInput" type="search" maxlength="80" placeholder="Tìm kiếm lịch hẹn..." aria-label="Tìm kiếm lịch hẹn">
        </div>

        <div id="appointmentList">
        <?php if (!$filtered): ?>
            <div class="empty">Chưa có lịch hẹn phù hợp.<br><a href="index.php">Đặt lịch ngay</a></div>
        <?php endif; ?>

        <?php foreach ($filtered as $index => $item): ?>
            <button type="button" class="appointment-mini <?= $index === 0 ? 'chosen' : '' ?>" data-index="<?= $index ?>">
                <div class="date-box">
                    <b><?= e(date("d", strtotime((string)$item["date"]))) ?></b>
                    <span>Thg <?= e(date("n", strtotime((string)$item["date"]))) ?></span>
                    <strong><?= e(date("Y", strtotime((string)$item["date"]))) ?></strong>
                </div>
                <div class="mini-content">
                    <b><?= e((string)$item["subject"]) ?></b>
                    <span><?= e((string)$item["lecturer"]) ?></span>
                    <span>◷ <?= e((string)$item["time"]) ?></span>
                    <em><?= e((string)$item["status"]) ?></em>
                </div>
            </button>
        <?php endforeach; ?>
        </div>
    </aside>

    <section class="detail-panel" id="detailPanel">
        <?php if ($filtered): $item = $filtered[0]; ?>
            <div class="detail-head">
                <div class="calendar-icon">▣</div>
                <div><h2><?= e((string)$item["subject"]) ?></h2><p>Mã lịch hẹn: <?= e((string)$item["id"]) ?></p><p>Đặt vào: <?= e((string)$item["created_at"]) ?></p></div>
                <span class="detail-status"><?= e((string)$item["status"]) ?></span>
            </div>

            <h2 class="section-label">THÔNG TIN LỊCH HẸN</h2>
            <div class="detail-grid">
                <div><b>Mã lịch hẹn</b><span><?= e((string)$item["id"]) ?></span></div>
                <div><b>Họ và tên</b><span><?= e((string)$item["name"]) ?></span></div>
                <div><b>Mã số sinh viên</b><span><?= e((string)$item["student_id"]) ?></span></div>
                <div><b>Email</b><span><?= e((string)$item["email"]) ?></span></div>
                <div><b>Số điện thoại</b><span><?= e((string)$item["phone"]) ?></span></div>
                <div><b>Giảng viên</b><span class="blue"><?= e((string)$item["lecturer"]) ?></span></div>
                <div><b>Nội dung tư vấn</b><span><?= e((string)$item["subject"]) ?></span></div>
                <div><b>Hình thức gặp gỡ</b><span><?= e((string)$item["meeting_type"]) ?></span></div>
                <div><b>Ngày hẹn</b><span><?= e(formatDateVN((string)$item["date"])) ?></span></div>
                <div><b>Khung giờ</b><span><?= e((string)$item["time"]) ?></span></div>
                <div class="wide"><b>Nội dung chi tiết</b><span><?= e((string)($item["detail"] ?: "Không có")) ?></span></div>
            </div>

            <h2 class="section-label status-title">TRẠNG THÁI LỊCH HẸN</h2>
            <div class="timeline">
                <div class="step done"><i>✓</i><span>Đã gửi yêu cầu</span><small><?= e((string)$item["created_at"]) ?></small></div>
                <div class="step current-step"><i>✓</i><span>Chờ xác nhận</span><small>Hiện tại</small></div>
                <div class="step"><i>✓</i><span>Đã xác nhận</span></div>
                <div class="step"><i>✓</i><span>Hoàn thành</span></div>
            </div>

            <div class="detail-actions">
                <button type="button" class="danger" onclick="alert('Chức năng hủy lịch sẽ được xử lý ở phần tiếp theo.')">Hủy lịch hẹn</button>
                <button type="button" class="edit" onclick="location.href='index.php'">Sửa lịch hẹn</button>
                <button type="button" class="chat" onclick="alert('Chức năng nhắn tin sẽ được bổ sung sau.')">Nhắn tin với giảng viên</button>
            </div>
        <?php else: ?>
            <div class="empty large-empty">Chưa có dữ liệu để hiển thị.</div>
        <?php endif; ?>
    </section>
</main>

<div class="note">ⓘ <b>Lưu ý:</b> Bạn sẽ nhận được thông báo khi lịch hẹn được giảng viên xác nhận hoặc có thay đổi.</div>
<footer class="footer"><span>© EduMeet. All rights reserved.</span><span>Chính sách bảo mật</span><span>Hỗ trợ</span></footer>
</div>

<script>
(() => {
    const search = document.getElementById('searchInput');
    const cards = [...document.querySelectorAll('.appointment-mini')];

    search?.addEventListener('input', () => {
        const keyword = search.value.trim().toLocaleLowerCase('vi');
        cards.forEach(card => {
            const text = card.textContent.toLocaleLowerCase('vi');
            card.hidden = keyword !== '' && !text.includes(keyword);
        });
    });
})();
</script>
</body>
</html>
