<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form action="<?=ROOT_DIR?>/login" method="post">
        
    
    <?php if(isset($errors)): ?>
        <div>  <?= implode('<br>', $errors)?>  </div>
    <?php endif; ?>
    
        <label for="email">Email</label>
        <input type="text" name="email" id="username">
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
        <input type="submit" name="submit" value="Login">

    </form>
    <a href="<?=ROOT_DIR?>/signup" title="Signup">Signup</a>
    <a href="<?=ROOT_DIR?>" title="Home">Home</a>
</body>
</html>



