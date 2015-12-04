<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        <span class="pull-left col-xs-8">Users</span>
                        <a href="<?php echo base_url('admin/create');?>" class="btn btn-primary pull-right">New</a>
                        <div class="clearfix"></div>
                    </h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row --> 
            <div class="row">
                <table class="table">
                    <thead>
                    <th>Name</th>
                    <th>Email</th>
                    </thead>
                    <tbody>
                        <?php if(count($Users) < 1) { ?>
                        <tr>
                            <td>No users created</td>
                        </tr>
                        <?php } else { ?>
                        <?php foreach ($Users as $user) { ?>
                        <tr>
                            <td><?php echo $user['name']?></td>
                            <td><?php echo $user['email']?></td>
                            <td><a class="btn btn-primary" href="<?php echo base_url('admin/edit/'.$user['id'])?>">Edit</a></td>
                        </tr>
                        <?php }} ?>
                    </tbody>                
                </table>    
            </div>
            
        </div>
        <!-- /#page-wrapper -->

    </div>