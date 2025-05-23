<?php 
    require '../includes/db_connect.php';
    include '../includes/header.php';

?>

<div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="../assets/images/banners/animation1.jpg" class="d-block w-100" alt="Image 1">
                <div class="carousel-content">
                    <h1>Pleated Perfection</h1>
                    <p>NEW 52" sweep pleated blades + 12" light kit for TriAire™</p>
                    <a href="#" class="btn">Learn More</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="../assets/images/banners/animation2.jpg" class="d-block w-100" alt="Image 2">
                <div class="carousel-content">
                    <h1>Pleated Perfection</h1>
                    <p>NEW 52" sweep pleated blades + 12" light kit for TriAire™</p>
                    <a href="#" class="btn">Learn More</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="../assets/images/banners/animation3.jpg" class="d-block w-100" alt="Image 3">
                <div class="carousel-content">
                    <h1>Pleated Perfection</h1>
                    <p>NEW 52" sweep pleated blades + 12" light kit for TriAire™</p>
                    <a href="#" class="btn ">Learn More</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

<?php 
    include '../includes/footer.php'
?>