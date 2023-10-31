<!DOCTYPE html>
<!-- Coding By CodingNepal - youtube.com/codingnepal -->
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <title>Dynamic Calendar JavaScript | CodingNepal</title>
  <link rel="stylesheet" href="calendar.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Google Font Link for Icons -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
  <script src="<?php echo ROOT_DIR?>/assets/images/calendar.js" defer></script>
</head>

<body>
  <div class="wrapper">
    <header>
      <p class="current-date"></p>
      <div class="icons">
        <span id="prev" class="material-symbols-rounded"></span>
        <span id="next" class="material-symbols-rounded"></span>
      </div>
    </header>
    <div class="calendar">
      <ul class="weeks">
        <li>Sun</li>
        <li>Mon</li>
        <li>Tue</li>
        <li>Wed</li>
        <li>Thu</li>
        <li>Fri</li>
        <li>Sat</li>
      </ul>
      <ul class="days"></ul>
    </div>
  </div>

</body>

</html>