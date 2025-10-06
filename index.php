<!DOCTYPE html>
<html lang="en">
<!--HEADER INICIO-->

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/solid.min.css">
    <link rel="stylesheet" href="style1.css">
    <title>myTaller</title>
</head>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal - Gestión de Taller</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="slider-container">
        <div class="slider-item active">
            <img src="assets/slider/slider1.png" alt="Imagen 1">
        </div>
        <div class="slider-item">
            <video src="assets/slider/slide2.mp4" autoplay loop muted alt="Video 3" ></video>
        </div>
        <div class="slider-item">
            <img src="assets/slider/slider3.png" alt="Imagen 4"> 
        </div>

        <button class="slider-arrow prev">&#10094;</button> <button class="slider-arrow next">&#10095;</button> <div class="slider-nav">
            </div>
    </div>

    <div class="container">
        <h1>Sistema de Gestión de Taller</h1>

        <div class="main-menu">
            <a href="clientes.php">Clientes</a>
            <a href="vehiculos.php">Vehículos</a>
            <a href="ordenes_trabajo.php">Órdenes de Trabajo</a>
            <a href="repuestos.php">Repuestos</a>
            <a href="configuracion.php" class="action-button config-button">Configuración Taller</a>
        </div>
    </div>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            const sliderContainer = document.querySelector('.slider-container');
            const sliderItems = document.querySelectorAll('.slider-item');
            const prevArrow = document.querySelector('.slider-arrow.prev');
            const nextArrow = document.querySelector('.slider-arrow.next');
            const sliderNav = document.querySelector('.slider-nav');
            let currentIndex = 0;
            let intervalId; // Para el autoplay

            // Generar puntos de navegación
            sliderItems.forEach((_, index) => {
                const dot = document.createElement('div');
                dot.classList.add('slider-nav-dot');
                if (index === 0) dot.classList.add('active');
                dot.addEventListener('click', () => showSlide(index));
                sliderNav.appendChild(dot);
            });
            const navDots = document.querySelectorAll('.slider-nav-dot');

            function showSlide(index) {
                // Desactivar todos los items y dots
                sliderItems.forEach(item => item.classList.remove('active'));
                navDots.forEach(dot => dot.classList.remove('active'));

                // Activar el item y dot actual
                sliderItems[index].classList.add('active');
                navDots[index].classList.add('active');

                // Pausar y reiniciar videos
                sliderItems.forEach(item => {
                    const video = item.querySelector('video');
                    if (video) {
                        video.pause();
                        video.currentTime = 0; // Reinicia el video
                    }
                });
                const currentVideo = sliderItems[index].querySelector('video');
                if (currentVideo) {
                    currentVideo.play();
                }

                currentIndex = index;
            }

            function nextSlide() {
                currentIndex = (currentIndex + 1) % sliderItems.length;
                showSlide(currentIndex);
            }

            function prevSlide() {
                currentIndex = (currentIndex - 1 + sliderItems.length) % sliderItems.length;
                showSlide(currentIndex);
            }

            // Event listeners para las flechas
            nextArrow.addEventListener('click', () => {
                nextSlide();
                resetAutoplay();
            });
            prevArrow.addEventListener('click', () => {
                prevSlide();
                resetAutoplay();
            });

            // Autoplay
            function startAutoplay() {
                intervalId = setInterval(nextSlide, 5000); // Cambia cada 5 segundos
            }

            function stopAutoplay() {
                clearInterval(intervalId);
            }

            function resetAutoplay() {
                stopAutoplay();
                startAutoplay();
            }

            // Iniciar slider y autoplay
            showSlide(currentIndex);
            startAutoplay();
        });
    </script>
</body>
</html>


    
</body>


 <section class="banner">
        <div class="banner-content">
            <div class="banner-content-text">
                <h3 class="banner-subtitle">Todos los servicios</h3>
                <h1 class="banner-title">Up to 40% off!</h1>

               
            </div>
            <img class="header-img" src="./assets/images/porcentaje.png" width="450" height="230">
        </div>
    </section>

    <footer>

        <div class="footer-content">
            <div class="footer-card">
                <img src="./assets/images/logo1.png">
                <div class="social-icons">
                    <a href="#"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-card">
                <h5>About Us</h5>
                <p>Un servicio de taller mecánico consiste en ofrecer mantenimiento y reparación de vehículos a propietarios, abarcando desde servicios de rutina como cambios 
                    de aceite y filtros hasta reparaciones complejas del motor, transmisión, carrocería y sistemas eléctricos.</p>
            </div>
            <div class="footer-card">
                <h5>Contact Us</h5>
                <p>Puedes contactarno al Whasaap +1 (843) 301-1044.</p>
            </div>
            <div class="footer-card">
                <h5>Newsletter</h5>
                <form method="post" action="#">
                    <input type="email" class="sub-input" placeholder="Enter your email">
                    <input class="sub-btn" type="submit" value="SUBSCRIBE">
                </form>
            </div>
        </div>
        <div class="copyright">
            <p>©2025 All Rights Reserved. Design by Yanis Torres. </p>
        </div>
    </footer>



</html>