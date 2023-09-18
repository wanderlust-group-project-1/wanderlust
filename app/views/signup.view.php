<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
</head>
<body>
    <h1>Signup</h1>
    <form action="<?=ROOT_DIR?>/signup" method="post">

    <?php if(isset($errors)): ?>
        <div>  <?= implode('<br>', $errors)?>  </div>
    <?php endif; ?>
    

        <label for="email">Email</label>
        <input type="text" name="email" id="email">
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
        <input type="submit" name="submit" value="Signup">

    </form>
    <a href="<?=ROOT_DIR?>/login" title="Login">Login</a>
    <br>
    <a href="<?=ROOT_DIR?>" title="Home">Home</a>
</body>
</html>




