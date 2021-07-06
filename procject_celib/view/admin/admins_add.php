<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=base_url()?>admin">Dashboard</a></li>
                <li class="breadcrumb-item">Settings</li>
                <li class="breadcrumb-item"><a href="<?=base_url()?>admins/settings/admins">Admins</a></li>
                <li class="breadcrumb-item active">Add Admin</li>
            </ol>

        <form method="POST" action="/admins/settings/admins">
            <input type="hidden" name="action" value="add">
            <div class="form-group row">
              <label for="example-text-input" class="col-2 col-form-label">Username</label>
              <div class="col-10">
                <input class="form-control" type="text" value="" placeholder="" name="username">
              </div>
            </div>
            <div class="form-group row">
              <label for="example-text-input" class="col-2 col-form-label">Password</label>
              <div class="col-10">
                <input class="form-control" type="password" value="" placeholder="" name="password">
              </div>
            </div>
            <div class="form-group row">
              <label for="example-text-input" class="col-2 col-form-label">Password Verify</label>
              <div class="col-10">
                <input class="form-control" type="password" value="" placeholder="" name="password2">
              </div>
            </div>
            <div class="form-group row">
              <label for="example-text-input" class="col-2 col-form-label">Email</label>
              <div class="col-10">
                <input class="form-control" type="email" value="" placeholder="" name="email">
              </div>
            </div>
            <div class="form-group row">
              <label for="example-text-input" class="col-2 col-form-label">Name</label>
              <div class="col-10">
                <input class="form-control" type="text" value="" placeholder="" name="name">
              </div>
            </div>
            <div class="form-group row">
              <label for="example-text-input" class="col-2 col-form-label">Admin Group</label>
              <div class="col-10">
                <select class="form-control" name="admin_group" required>
                <?php
                foreach ($admin_groups as $v) {
                  echo "<option value='".$v["id"]."'>".$v["name"]."</option>";
                } ?>
                </select>
              </div>
            </div> 
            <div class="form-group row"> 
              <div class="col-10">
                <input type="submit" class="btn btn-primary">
              </div>
            </div> 
            

        </form>
