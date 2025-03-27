<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MediSphere</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@300&display=swap" rel="stylesheet">
    <style>
            body {
                margin: 0;
                font-family: 'Roboto', sans-serif;
                background-color: #f5f7fa;
                color: #333;
                overflow-x: hidden;
                scroll-behavior: smooth;
            }

            /* Navbar */
            nav {
                position: fixed;
                top: 0;
                width: 100%;
                background: rgba(0, 0, 0, 0.85);
                padding: 15px 0;
                z-index: 1000;
                transition: background 0.3s ease;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            }

            nav ul {
                list-style: none;
                display: flex;
                justify-content: center;
                margin: 0;
                padding: 0;
            }

            nav ul li {
                margin: 0 30px;
            }

            nav ul li a {
                color: #f1f1f1;
                text-decoration: none;
                font-size: 1.2rem;
                transition: color 0.3s, transform 0.3s;
                position: relative;
            }

            nav ul li a:hover {
                color: #28b4b4;
                transform: translateY(-3px);
            }

            nav ul li a::after {
                content: '';
                display: block;
                width: 0;
                height: 2px;
                background: #28b4b4;
                transition: width 0.3s;
                position: absolute;
                bottom: -5px;
                left: 50%;
                transform: translateX(-50%);
            }

            nav ul li a:hover::after {
                width: 130%;
            }

            /* Hero Section */
            .hero {
                height: 100vh;
                background-image: url('images/back.jpg');
                background-size: cover;
                background-position: center;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                text-align: center;
                position: relative;
            }

            .hero::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                z-index: 1;
            }

            .hero-content {
                z-index: 2;
                max-width: 80%;
                padding: 0 20px;
            }

            .hero h1 {
                font-size: 80px;
            }

            .hero h2 {
                font-size: 30px;
            }

            .hero p {
                font-size: 16px;
                margin-bottom: 70px;
            }

            .hero h1, .hero h2, .hero p {
                color: #ffffff;
                text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
            }

            .cta {
                background-color: #28b4b4;
                text-align: center;
                padding: 15px 50px;
                font-size: 1.2rem;
                font-weight: bold;
                color: #fff;
                text-decoration: none;
                border-radius: 50px;
                transition: background-color 0.3s, transform 0.3s;
                animation: fadeInUp 1s;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            }

            .cta:hover {
                background-color: #239d9d;
                transform: translateY(-5px);
                box-shadow: 0 10px 15px rgba(0, 0, 0, 0.3);
            }

            /* Services Section */
            .services {
                padding: 80px 20px;
                text-align: center;
                background-color: #e8f1f5;
            }

            .services h2 {
                font-size: 2.5rem;
                margin-bottom: 50px;
                font-family: 'Poppins', sans-serif;
                color: #333;
            }

            .service-container {
                display: flex;
                justify-content: space-around;
                flex-wrap: wrap;
            }

            .service-box {
                background-color: #fff;
                margin: 10px;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                width: 300px;
                transition: transform 0.3s;
            }

            .service-box:hover {
                transform: translateY(-20px);
            }

            .service-box img {
                width: 70%;
                border-radius: 10px;
            }

            .service-box h3 {
                margin-top: 15px;
                font-size: 1.5rem;
                color: #28b4b4;
            }

            /* About Section */
            .about {
                padding: 80px 20px;
                background-color: #fff;
                text-align: center;
            }

            .about h2 {
                font-size: 2.5rem;
                margin-bottom: 20px;
                font-family: 'Poppins', sans-serif;
                color: #333;
            }

            .about p {
                font-size: 1.2rem;
                color: #555;
                max-width: 800px;
                margin: 0 auto;
                line-height: 1.6;
            }

            /* Contact Section */
            .contact {
                padding: 80px 20px;
                text-align: center;
                background-color: #e8f1f5;
            }

            .contact h2 {
                font-size: 2.5rem;
                margin-bottom: 40px;
                font-family: 'Poppins', sans-serif;
                color: #333;
            }

            .contact-form {
                max-width: 600px;
                margin: 0 auto;
            }

            .contact-form input,
            .contact-form textarea {
                width: 100%;
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 5px;
                border: 1px solid #ccc;
                font-size: 1rem;
                transition: border-color 0.3s;
            }

            .contact-form input:focus,
            .contact-form textarea:focus {
                border-color: #28b4b4;
            }

            .contact-form input[type="submit"] {
                background-color: #28b4b4;
                width: 100%;
                color: #fff;
                border: none;
                font-size: 1rem;
                cursor: pointer;
                transition: background-color 0.3s;
                padding: 15px;
                border-radius: 5px;
            }

            .contact-form input[type="submit"]:hover {
                background-color: #239d9d;
            }

            /* Footer */
            footer {
                background-color: #333;
                color: #fff;
                padding: 20px;
                text-align: center;
                font-size: 0.9rem;
            }

            footer a {
                color: #28b4b4;
                text-decoration: none;
            }

            footer a:hover {
                text-decoration: underline;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                nav ul li {
                    margin: 0 15px;
                }

                .hero h1 {
                    font-size: 3rem;
                }

                .hero h2 {
                    font-size: 2rem;
                }

                .hero p {
                    font-size: 1.2rem;
                }

                .service-container {
                    flex-direction: column;
                    align-items: center;
                }

                .services h2, .about h2, .contact h2 {
                    font-size: 2rem;
                }

                .service-box {
                    width: 90%;
                }

                .cta {
                    padding: 10px 30px;
                    font-size: 1rem;
                }
            }

            @media (max-width: 480px) {
                .hero h1 {
                    font-size: 2.5rem;
                }

                .hero h2 {
                    font-size: 1.5rem;
                }

                .hero p {
                    font-size: 1rem;
                }

                nav ul {
                    flex-direction: column;
                }

                nav ul li {
                    margin: 10px 0;
                }
            }
    </style>

    </head>

    <body>
        <nav>
            <ul>
                <li><a href="#hero">Home</a></li>
                <li><a href="#services">Speciality</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="loginpage.php">LOGIN</a></li>
            </ul>
        </nav>

        <section id="hero" class="hero">
            <div class="hero-content">
                <h1>MediSphere </h1> <h2>Empowering Healthcare, Delivering Quality. </h2>
                <p>"Welcome to the future of healthcare, where quality meets innovation. At MediSphere, we don’t just offer equipment — we deliver solutions that empower care, elevate efficiency, and enhance outcomes. Our mission is clear : to provide the best in medical technology, helping you stay ahead in a world where health never stands still. With a commitment to excellence and cutting-edge products, we redefine healthcare so you can focus on what truly matters — delivering care with confidence. Empowering healthcare, delivering quality." </p>
                <a href="home.php" class="cta">Explore Us</a>
            </div>
        </section>

        <section id="services" class="services">
            <h2>OUR SPECIALITY</h2>
            <div class="service-container">
                <div class="service-box">
                    <img src="images/default.jpg" alt="Service 1">
                    <h3>Great Variety of Categories & Products</h3>
                </div>
                <div class="service-box">
                    <img src="images/default.jpg" alt="Service 2">
                    <h3>Customer Centric and Satisfaction</h3>
                </div>
                <div class="service-box">
                    <img src="images/default.jpg" alt="Service 3">
                    <h3>Continuous Improvement and Updates</h3>
                </div>
            </div>
        </section>

        <section id="about" class="about">
            <h2>ABOUT US</h2>
            <p>"At MediSphere, we're not just providers — we're pioneers committed to enhancing healthcare through quality and innovation. Specializing in reliable, cutting-edge medical equipment, we deliver solutions that empower healthcare professionals to provide efficient, top-tier care. Our journey is driven by expertise, collaboration, and an unwavering dedication to improving patient outcomes. But this is only the start. Ready to see how MediSphere can transform your healthcare experience? Empowering healthcare, delivering quality. <br> <a href="loginpage.php" style="color: #28b4b4; text-decoration: underline;">Know More</a></p></p>
        </section>

        <section id="contact" class="contact">
            <h2>CONTACT US</h2>
            <div class="contact-form">
                <input type="text" placeholder="Your Name">
                <input type="email" placeholder="Your Email">
                <textarea placeholder="Your Message"></textarea>
                <input type="submit" value="Send Message">
            </div>
        </section>

        <footer>
            <p>&copy; 2024 MediSphere. All rights reserved.</p>
        </footer>
    </body>
</html>