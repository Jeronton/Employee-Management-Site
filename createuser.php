<!-- 
    
    Author: Jeremy Grift
    Created: March 17, 2020
    Last Updated: March 17, 2020
 -->

 <?php 
    $title = 'Create User';

    // require('authenticate.php');
 ?>

 <?php include('header.php'); ?>
    <div class="container">
        <form>
            <div class="row">
                <div class="col-md-6">   
                    <div class="form-group row"> 
                        <label for="firstname" class="col-lg-3 col-form-label">First Name:</label>
                        <div class="col-lg-9">
                            <input id="firstname" name="firstname" type="text" class="form-control" required>
                            <div class="invalid-feedback">
                                *Required
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="lastname" class="col-lg-3 col-form-label">Last Name:</label>
                        <div class="col-lg-9">
                            <input id="lastname" name="lastname" type="text" class="form-control" required>
                            <div class="invalid-feedback">
                                *Required
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="username" class="col-lg-3 col-form-label">Username:</label>
                        <div class="col-lg-9">
                            <input id="username" name="username" type="text" class="form-control" required>
                            <div class="invalid-feedback">
                                *Required
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password" class="col-lg-3 col-form-label">Password:</label>
                        <div class="col-lg-9">
                            <input id="password" name="password" type="password" class="form-control" required>
                            <div class="invalid-feedback">
                                *Required
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="confirmpassword" class="col-lg-3 col-form-label">Confirm Password:</label>
                        <div class="col-lg-9">
                            <input id="confirmpassword" name="confirmpassword" type="password" class="form-control" required>
                            <div class="invalid-feedback">
                                *Required
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="usertype" class="col-lg-3 col-form-label">Account Type:</label>
                        <div class="col-lg-9">
                            <select name="usertype" id="usertype" class="custom-select">
                                <option value="employee" selected >Employee</option>
                                <option value="accountant">Accountant</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="jobsite" class="col-lg-3 col-form-label">Jobsite:</label>
                        <div class="col-lg-9">
                            <select name="jobsite" id="jobsite" class="custom-select">
                                <option value="other" selected >Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-3"></div>
                        <div class="col-lg-9">
                            <input class="btn btn-primary w-100" type="submit" name="create" value="Create User">
                        </div>
                    </div>


                </div>
                <div class="form-group col-md-6 row">   

                    <div class="form-group row">
                        <label for="profileimage" class="col-lg-4 col-form-label">Profile Image:</label>
                        <div class="col-lg-8">
                            <input id="profileimage" name="profileimage" type="file" class="form-control">
                        </div>
                    </div>

                    <div class="card w-50">
                        <img src="images/BlankProfile.jpg"" alt="Blank profile picture." class="card-img-top">
                    </div>
                </div>

                
            </div>
        </form>
    </div>
</body>
</html>

<!-- 
                    <div class="form-group row">
                        <label for="" class="col-lg-3 col-form-label">:</label>
                        <div class="col-lg-9">
                            <input id="" name="" type="text" class="form-control" required>
                            <div class="invalid-feedback">
                                *Required
                            </div>
                        </div>
                    </div> -->