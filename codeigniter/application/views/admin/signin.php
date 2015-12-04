
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Please Sign In</h3>
            </div>
            <div class="panel-body">
                <form method="post">
                    <fieldset>
                        <div class="form-group">
                            <input class="form-control <?php if (count($errors) && array_key_exists('email', $errors)) echo 'error'; ?>" placeholder="E-mail" name="email" type="email" value="<?php echo $user['email']; ?>" autofocus>
                            <div class="error-message"><?php if (count($errors) && array_key_exists('email', $errors)) echo $errors['email']; ?></div>
                        </div>
                        <div class="form-group">
                            <input class="form-control <?php if (count($errors) && array_key_exists('password', $errors)) echo 'error'; ?>" placeholder="Password" name="password" type="password" value="">
                            <div class="error-message"><?php if (count($errors) && array_key_exists('password', $errors)) echo $errors['password']; ?></div>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input name="remember" type="checkbox" value="Remember Me">Remember Me
                            </label>
                        </div>
                        <!-- Change this to a button or input when using this as a form -->
                        <input type="submit" class="btn btn-lg btn-block" value="Login">
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
    