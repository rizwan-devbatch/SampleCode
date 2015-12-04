<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class=""><a class="btn btn-primary" href="javascript:window.history.go(-1);">Go back</a></h1>
                </div>
            </div>    
            <div class="row">
                <div class="col-lg-12 page-header">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Create New User
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <form method="post">
                                        <div class="form-group <?php if (count($errors) && array_key_exists('name', $errors)) echo 'has-error'; ?>">
                                            <label for="name">Name</label>
                                            <input class="form-control" id="name" name="name" value="<?php echo $User['name']; ?>" placeholder="Name">
                                            <div class="error-message"><?php if (count($errors) && array_key_exists('name', $errors)) echo $errors['name']; ?></div>
                                        </div>
                                        <div class="form-group <?php if (count($errors) && array_key_exists('email', $errors)) echo 'has-error'; ?>">
                                            <label for="email">Email</label>
                                            <input class="form-control" id="email" name="email" value="<?php echo $User['email']; ?>" placeholder="Email">
                                            <div class="error-message"><?php if (count($errors) && array_key_exists('email', $errors)) echo $errors['email']; ?></div>
                                        </div>
                                        <div class="form-group <?php if (count($errors) && array_key_exists('password', $errors)) echo 'has-error'; ?>">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" value="<?php echo $User['password']; ?>" placeholder="Password">
                                            <div class="error-message"><?php if (count($errors) && array_key_exists('password', $errors)) echo $errors['password']; ?></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Select Role</label>
                                            <select name="role_id" id="role_id" class="form-control">
                                                <option value=""></option>
                                                <?php foreach ($Roles as $role) { ?>
                                                <option <?php if($User['role_id'] == $role['id']) echo 'selected'?> value="<?php echo $role['id']?>"><?php echo $role['name']?></option>
                                                <?php } ?>
                                            </select>
                                        </div>                                      
                                        
                                        <button type="submit" class="btn btn-default">Save</button>
                                        <button type="reset" class="btn btn-default">Reset</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->    