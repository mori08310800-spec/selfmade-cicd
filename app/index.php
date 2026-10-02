<?php
require_once __DIR__ . '/config.php';

$result = $conn->query('SELECT * FROM tasks ORDER BY id DESC');
if (!$result) {
    http_response_code(500);
    exit('TASK_QUERY_FAILED');
}

$tasks = [];
$doneCount = 0;

while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
    if ($row['is_done']) {
        $doneCount++;
    }
}

$totalCount = count($tasks);
$remainingCount = $totalCount - $doneCount;
?>
<!doctype html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SkyNote — Mini Task Manager</title>
  <style>
    :root {
      --ink: #193b44;
      --muted: #668087;
      --teal: #0e807e;
      --teal-dark: #086360;
      --line: #dbe8e7;
      --white: #ffffff;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      color: var(--ink);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI",
                   "Hiragino Kaku Gothic ProN", "Noto Sans JP", sans-serif;
      background: #edf7f4;
    }

    a {
      color: inherit;
    }

    .sky {
      position: relative;
      min-height: 370px;
      overflow: hidden;
      background:
        radial-gradient(circle at 76% 20%, rgba(255, 244, 195, .95), transparent 24%),
        linear-gradient(155deg, #a7d9dd 0%, #d2ece5 54%, #f6f1df 100%);
    }

    .sky::after {
      content: "";
      position: absolute;
      left: -10%;
      right: -10%;
      bottom: -110px;
      height: 185px;
      border-radius: 50% 50% 0 0;
      background: #edf7f4;
    }

    .sky-inner,
    .main {
      width: min(920px, calc(100% - 40px));
      margin: 0 auto;
    }

    .sky-inner {
      position: relative;
      z-index: 2;
    }

    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-top: 34px;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 11px;
      font-size: 20px;
      font-weight: 800;
      letter-spacing: .08em;
    }

    .brand-mark {
      display: grid;
      width: 35px;
      height: 35px;
      place-items: center;
      border-radius: 12px;
      color: white;
      background: var(--teal);
      font-size: 19px;
    }

    .top-label {
      padding: 9px 14px;
      border: 1px solid rgba(25, 59, 68, .2);
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .14em;
    }

    .hero {
      padding-top: 73px;
    }

    .eyebrow {
      margin: 0 0 13px;
      color: var(--teal-dark);
      font-size: 12px;
      font-weight: 800;
      letter-spacing: .22em;
    }

    h1 {
      margin: 0;
      font-size: clamp(34px, 6vw, 56px);
      line-height: 1.3;
      letter-spacing: -.04em;
    }

    .hero p:last-child {
      margin-top: 17px;
      color: #44646a;
      font-size: 15px;
      line-height: 1.8;
    }

    /* それぞれの鳥が左から右へ飛ぶ */
    .bird {
      position: absolute;
      z-index: 1;
      top: var(--top);
      left: -130px;
      width: var(--size);
      height: auto;
      fill: none;
      stroke: rgba(27, 91, 99, .7);
      stroke-width: 5;
      stroke-linecap: round;
      animation: fly var(--duration) linear var(--delay) infinite;
    }

    @keyframes fly {
      0% {
        transform: translateX(0) translateY(0);
      }
      50% {
        transform: translateX(55vw) translateY(-18px);
      }
      100% {
        transform: translateX(120vw) translateY(0);
      }
    }

    .main {
      position: relative;
      z-index: 3;
      padding-bottom: 75px;
    }

    .panel {
      padding: 32px;
      border: 1px solid rgba(255, 255, 255, .9);
      border-radius: 25px;
      background: rgba(255, 255, 255, .9);
      box-shadow: 0 22px 70px rgba(29, 93, 93, .1);
    }

    .panel-heading {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 20px;
      margin-bottom: 25px;
    }

    .section-label {
      margin: 0 0 7px;
      color: var(--teal);
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .2em;
    }

    h2 {
      margin: 0;
      font-size: 25px;
      letter-spacing: -.03em;
    }

    .count {
      color: var(--muted);
      font-size: 13px;
      white-space: nowrap;
    }

    .count strong {
      color: var(--teal-dark);
      font-size: 22px;
    }

    .add-form {
      display: flex;
      gap: 10px;
      margin-bottom: 25px;
    }

    .add-form input {
      flex: 1;
      min-width: 0;
      padding: 16px 18px;
      border: 1px solid var(--line);
      border-radius: 13px;
      outline: none;
      background: #f8fbfa;
      color: var(--ink);
      font: inherit;
      transition: border-color .2s, box-shadow .2s;
    }

    .add-form input:focus {
      border-color: var(--teal);
      box-shadow: 0 0 0 4px rgba(14, 128, 126, .11);
    }

    .add-form button {
      padding: 0 23px;
      border: 0;
      border-radius: 13px;
      background: var(--teal);
      color: white;
      font: inherit;
      font-weight: 700;
      cursor: pointer;
      transition: background .2s, transform .2s;
    }

    .add-form button:hover {
      background: var(--teal-dark);
      transform: translateY(-2px);
    }

    .task-list {
      display: grid;
      gap: 10px;
    }

    .task {
      display: flex;
      align-items: center;
      gap: 14px;
      min-height: 68px;
      padding: 13px 16px;
      border: 1px solid var(--line);
      border-radius: 14px;
      background: white;
      transition: border-color .2s, transform .2s;
    }

    .task:hover {
      border-color: #9bcfca;
      transform: translateY(-2px);
    }

    .toggle {
      display: grid;
      flex: none;
      width: 28px;
      height: 28px;
      place-items: center;
      border: 2px solid #a8c9c5;
      border-radius: 50%;
      color: white;
      text-decoration: none;
      font-size: 16px;
      font-weight: 700;
    }

    .task.done .toggle {
      border-color: var(--teal);
      background: var(--teal);
    }

    .task-title {
      flex: 1;
      min-width: 0;
      overflow-wrap: anywhere;
      font-size: 14px;
      font-weight: 600;
      line-height: 1.6;
    }

    .task.done .task-title {
      color: #91a7a8;
      text-decoration: line-through;
    }

    .delete {
      flex: none;
      padding: 8px 10px;
      border-radius: 8px;
      color: #84999a;
      font-size: 12px;
      text-decoration: none;
    }

    .delete:hover {
      background: #fff0ee;
      color: #b14f46;
    }

    .empty {
      padding: 45px 20px;
      border: 1px dashed #b6d4d0;
      border-radius: 16px;
      background: #f8fcfa;
      text-align: center;
    }

    .empty-icon {
      margin-bottom: 10px;
      font-size: 30px;
    }

    .empty p {
      margin: 5px 0;
    }

    .empty p:last-child {
      color: var(--muted);
      font-size: 13px;
    }

    .footer {
      margin-top: 23px;
      color: var(--muted);
      font-size: 11px;
      text-align: center;
      letter-spacing: .08em;
    }

    @media (max-width: 600px) {
      .sky {
        min-height: 340px;
      }

      .topbar {
        padding-top: 22px;
      }

      .hero {
        padding-top: 76px;
      }

      .panel {
        padding: 22px 17px;
      }

      .panel-heading {
        align-items: flex-start;
      }

      .add-form button {
        padding: 0 15px;
      }

      .top-label {
        display: none;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .bird,
      .task,
      .add-form button {
        animation: none;
        transition: none;
      }

      .bird {
        display: none;
      }
    }
  </style>
</head>
<body>
  <header class="sky">
    <!-- SVGの曲線で描いた鳥。外部の画像ファイルは使わない -->
    <svg class="bird" style="--top: 22%; --size: 87px; --duration: 24s; --delay: -4s"
         viewBox="0 0 120 60" aria-hidden="true">
      <path d="M4 38 Q31 12 60 38 Q89 12 116 38"/>
    </svg>
    <svg class="bird" style="--top: 32%; --size: 51px; --duration: 31s; --delay: -18s"
         viewBox="0 0 120 60" aria-hidden="true">
      <path d="M4 38 Q31 12 60 38 Q89 12 116 38"/>
    </svg>
    <svg class="bird" style="--top: 48%; --size: 65px; --duration: 28s; --delay: -11s"
         viewBox="0 0 120 60" aria-hidden="true">
      <path d="M4 38 Q31 12 60 38 Q89 12 116 38"/>
    </svg>
    <svg class="bird" style="--top: 16%; --size: 45px; --duration: 35s; --delay: -26s"
         viewBox="0 0 120 60" aria-hidden="true">
      <path d="M4 38 Q31 12 60 38 Q89 12 116 38"/>
    </svg>

    <div class="sky-inner">
      <div class="topbar">
        <div class="brand">
          <span class="brand-mark">✦</span>
          <span>SkyNote</span>
        </div>
        <span class="top-label">MINI TASK MANAGER</span>
      </div>

      <div class="hero">
        <p class="eyebrow">MAKE SPACE FOR WHAT MATTERS</p>
        <h1>今日も<br>自分のペースで。</h1>
        <p>小さなタスクをひとつずつ。空を見上げる余裕をつくろう。</p>
      </div>
    </div>
  </header>

  <main class="main">
    <section class="panel" aria-labelledby="task-heading">
      <div class="panel-heading">
        <div>
          <p class="section-label">YOUR TASKS</p>
          <h2 id="task-heading">タスク一覧</h2>
        </div>
        <div class="count">残り <strong><?= $remainingCount ?></strong> / <?= $totalCount ?></div>
      </div>

      <form class="add-form" action="add.php" method="post">
        <input type="text" name="title" maxlength="255" required
               placeholder="新しいタスクを入力"
               aria-label="新しいタスク">
        <button type="submit">＋ 追加</button>
      </form>

      <?php if ($totalCount === 0): ?>
        <div class="empty">
          <div class="empty-icon">☁</div>
          <p>まだタスクはありません</p>
          <p>思いついたことを、上の欄から追加してみましょう。</p>
        </div>
      <?php else: ?>
        <div class="task-list">
          <?php foreach ($tasks as $row): ?>
            <?php
              $id = (int)$row['id'];
              $isDone = (bool)$row['is_done'];
              $safeTitle = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
            ?>
            <div class="task<?= $isDone ? ' done' : '' ?>">
              <a class="toggle" href="toggle.php?id=<?= $id ?>"
                 aria-label="<?= $isDone ? '未完了に戻す' : '完了にする' ?>">
                <?= $isDone ? '✓' : '' ?>
              </a>
              <span class="task-title"><?= $safeTitle ?></span>
              <a class="delete" href="delete.php?id=<?= $id ?>"
                 onclick="return confirm('このタスクを削除しますか？')"
                 aria-label="タスクを削除">削除</a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <div class="footer">SKYNOTE · ONE TASK AT A TIME</div>
  </main>
</body>
</html>
