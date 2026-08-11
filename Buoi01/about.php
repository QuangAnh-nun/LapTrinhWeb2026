<?php
$name = "Đỗ Quang Anh";
$student_id = "224001763";
$class = "CNTTD2024B";

$hobbies = [
    "Lập trình và tìm hiểu công nghệ",
    "Thiết kế và xây dựng website",
    "Chơi game",
    "Nghe nhạc"
];

$projects = [
    [
        "name" => "Tây Bắc Tour",
        "description" => "Website giới thiệu và hỗ trợ tìm kiếm các địa điểm du lịch, tour và khách sạn khu vực Tây Bắc.",
        "technology" => "HTML, CSS, JavaScript"
    ],
    [
        "name" => "Quản lý siêu thị mini",
        "description" => "Ứng dụng quản lý sản phẩm, giỏ hàng, số lượng tồn kho và tính tiền cho cửa hàng.",
        "technology" => "C++"
    ],
    [
        "name" => "Quản lý sự kiện & vé",
        "description" => "Ứng dụng hỗ trợ quản lý sự kiện, vé và tra cứu thông tin người tham gia.",
        "technology" => "Java Swing"
    ]
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Giới thiệu - Đỗ Quang Anh</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #333;
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 40px auto;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: white;
            padding: 45px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .header h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 18px;
        }

        /* Các section */
        .section {
            background: white;
            margin-top: 25px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.07);
        }

        .section h2 {
            color: #2563eb;
            margin-bottom: 20px;
            border-left: 5px solid #2563eb;
            padding-left: 12px;
        }

        /* Thông tin cá nhân */
        .info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .info-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
        }

        .info-item strong {
            color: #2563eb;
        }

        /* Sở thích */
        .hobbies {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .hobby {
            background: #eff6ff;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #dbeafe;
        }

        /* Dự án */
        .project {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: 0.3s;
        }

        .project:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.08);
        }

        .project h3 {
            color: #1d4ed8;
            margin-bottom: 8px;
        }

        .technology {
            display: inline-block;
            margin-top: 12px;
            padding: 6px 12px;
            background: #dbeafe;
            color: #1d4ed8;
            border-radius: 20px;
            font-size: 14px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 25px;
            padding: 20px;
            color: #666;
        }

        /* Responsive */
        @media (max-width: 700px) {
            .info,
            .hobbies {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- Phần giới thiệu -->
    <div class="header">
        <h1>Xin chào! Tôi là Đỗ Quang Anh</h1>
        <p>Sinh viên ngành Công nghệ Thông tin</p>
    </div>


    <!-- Thông tin cá nhân -->
    <div class="section">

        <h2>Thông tin cá nhân</h2>

        <div class="info">

            <div class="info-item">
                <strong>Họ và tên:</strong>
                <?= $name ?>
            </div>

            <div class="info-item">
                <strong>Mã sinh viên:</strong>
                <?= $student_id ?>
            </div>

            <div class="info-item">
                <strong>Lớp:</strong>
                <?= $class ?>
            </div>

            <div class="info-item">
                <strong>Ngành:</strong>
                Công nghệ Thông tin
            </div>

        </div>

    </div>


    <!-- Sở thích -->
    <div class="section">

        <h2>Sở thích</h2>

        <div class="hobbies">

            <?php foreach ($hobbies as $hobby): ?>

                <div class="hobby">
                    🎯 <?= $hobby ?>
                </div>

            <?php endforeach; ?>

        </div>

    </div>


    <!-- Dự án -->
    <div class="section">

        <h2>Các dự án đã thực hiện</h2>

        <?php foreach ($projects as $project): ?>

            <div class="project">

                <h3><?= $project["name"] ?></h3>

                <p>
                    <?= $project["description"] ?>
                </p>

                <span class="technology">
                    <?= $project["technology"] ?>
                </span>

            </div>

        <?php endforeach; ?>

    </div>


    <!-- Mục tiêu -->
    <div class="section">

        <h2>Mục tiêu học tập</h2>

        <p>
            Tôi đang theo học ngành Công nghệ Thông tin và mong muốn
            nâng cao kiến thức về lập trình, cơ sở dữ liệu và phát triển
            ứng dụng Web. Trong thời gian tới, tôi muốn có thể tự xây dựng
            những website hoàn chỉnh và áp dụng kiến thức đã học vào các
            dự án thực tế.
        </p>

    </div>


    <div class="footer">
        <p>© 2026 Đỗ Quang Anh - CNTTD2024B</p>
    </div>

</div>

</body>
</html>
