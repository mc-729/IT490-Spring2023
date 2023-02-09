<?php

require_once('nav.php');
require_once(__DIR__ . '/../helper%20files')


?>

</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>




  <div class="container-fluid">
    <h1>Login</h1>
    <form  method="POST" onsubmit="return validate(this)" >
        <div class="mb-3">
            <label class="form-label" for="email">Username/Email</label>
            <input class="form-control" type="text" id="email" name="email" required />
        </div>
        <div class="mb-3">
            <label class="form-label" for="pw">Password</label>
            <input class="form-control" type="password" id="pw" name="password" required minlength="4" />
        </div>
        <input type="submit" class="mt-3 btn btn-primary" value="Login" />
    </form>
 
</div>
<script>
     function validate(form) {

        let isValid = true;
        let email = form.email.value;
        let password = form.password.value;

        if(!email.includes("@")){
            flash("Client side - Invalid email, no @ symbol", "warning");
        }

        if (email.includes("@")) {

            if (!isValidEmail(email)) {

                isValid = false;
                
            flash("Client Side - Invalid email", "warning");
            }
            return isValid;
     }
    }
</script>