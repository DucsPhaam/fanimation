<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/Fanimation/includes/config.php';
require $db_connect_url;
include $header_url;
?>
<link rel="stylesheet" href="../assets/css/index.css">
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
    <div class="content-1">
        <p>Fanimation fans are the perfect fusion of beauty and functionality. With designs for every style and technology-driven controls for your convenience, Fanimation fans inspire your home. They integrate into any space and allow you to make a statement that is all your own.</p>
        <div class="content1-fan-img">
            <img src="../assets/images/banners/home_image_3.png" alt="">
        </div>
        <div class="intro-content-1">
            <div class="content1-intro">
                <span>CHOOSING A FAN</span>
                <h4>Location is everything.</h4>
                <h6>Installing a fan in your favorite indoor space? Or adding one to your outdoor haven? The location determines the fan rating (dry, damp and wet) you need. From there, the fun begins as you choose a style that fits you!</h6>
                <button>Learn More</button>
            </div>
            <div class="content1-banner">
                <img src="../assets/images/banners/content-1-img.png" alt="">
            </div>
        </div>
    </div>
    <div class="plx-image1">
        <video src="../assets/images/banners/March25_CCT_Select_v06.mp4" autoplay loop muted></video>
    </div>
    <div class="content-2">
        <a href="products.php"><img src="../assets/images/banners/banner-fanimation-studio1_hover.jpg" alt=""></a>
        <a href="products.php"><img src="../assets/images/banners/showroomcollection2018_hover.jpg" alt=""></a>
    </div>
    <div class="plx-image2">
        <div class="box-content">
            <h4>ABOUT US</h4>
            <h2>Air Apparent</h2>
            <p>From the very first fan we created more than 30 years ago to the newest ones in our portfolio, we create fans you can’t wait to show off! The same ingenuity and quality craftsmanship that gave birth to Fanimation continues to guide us today.</p>
        </div>
    </div>
<?php 
    include $footer_url;
?>
