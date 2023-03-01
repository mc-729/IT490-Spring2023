
<?php session_start(); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>


<nav class="navbar navbar-expand-lg navbar-light bg-light">
    
    <div class="container-fluid">
   
        <a class="navbar-brand" href="/landingPage.php">Home</a>
    
     
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent" aria-controls="navContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <?php if (!isset($_SESSION['DB_ID'])): ?>
            <li class="nav-item"><a class="nav-link" href="/loginForm.php">Login</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['DB_ID'])): ?>
            <li class="nav-item"><a class="nav-link" href="/logout.php">Logout</a></li>
            <?php endif; ?>        
            <li class="nav-item"><a class="nav-link" href="/RegisterForm.php">Register</a></li>
                    <li class="nav-item"><a class="nav-link" href="/validate_test.php">session valid?</a></li>
                    <?php if (isset($_SESSION['DB_ID'])): ?>
            <li class="nav-item"><a class="nav-link" href="/Profile.php">Profile</a></li>
            <?php endif; ?>              
            </ul>
        </div>
    </div>
</nav>
