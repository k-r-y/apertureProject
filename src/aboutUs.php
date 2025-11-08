<?php
$activeMember = isset($_GET['member']) ? $_GET['member'] : 'casiano';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="./assets/camera.png" type="image/x-icon">
    <title>Aperture</title>
</head>

<body>
    <?php include './includes/header.php'; ?>

    <section class="w-100 min-vh-100 d-flex px-2 px-lg-5 py-5 justify-content-center align-items-center" id="about">

        <div class="container-fluid d-flex justify-content-center align-items-center" style="margin-top: 3rem;">
            <div class="row gap-3">
                <div class="col-lg-4 border shadow-sm  p-3  firstCol">

                    <!-- Casiano -->
                    <aside class="profileDetails overflow-x-hidden align-items-center justify-content-center active">
                        <div id="casiano" class="<?php echo ($activeMember == 'casiano') ? 'active' : '' ?> profile">
                            <div class="profileInfo gap-2 d-flex flex-lg-column justify-content-center align-items-center">

                                <img src="./assets/pic.png" alt="" class="img-fluid bg-secondary rounded-4 w-25 ">


                                <div class="profileInfoText text-center d-flex flex-column justify-content-center align-content-center">
                                    <p class="m-0 fw-bold fs-6">Prince Andrew Casiano</p>
                                    <small class="mt-1 role"><span class="bg-secondary px-2 py-1 rounded text-light">Web Developer</span></small>
                                </div>

                            </div>

                            <hr class="border border-secondary w-100">


                            <div class="profileMoreInfo gap-3 flex-wrap d-flex justify-content-center align-items-center">



                                <div class="row justify-content-center align-items-center">

                                    <div class="col-md-6 col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/section.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Section</small>
                                                <p class="m-0">BSIS 209</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6   col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/course.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Program</small>
                                                <p class="m-0">Bachelor of Science in Information System</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/school.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">School</small>
                                                <p class="m-0">Kolehiyo ng Lungsod ng Dasmariñas</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-12">
                                        <div class=" p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/email.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Email</small>
                                                <p class="m-0">pawcasiano@kld.edu.ph</p>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <hr class="border border-secondary w-100">



                            <div class="socmed justify-content-center align-items-center d-flex flex-row gap-3">
                                <a href="https://www.facebook.com/prince.andrew.casiano.2024" target="_blank"><img src="./assets/facebook-app-symbol.png" alt="Facebook"></a>
                                <a href="https://github.com/noir-13" target="_blank"><img src="./assets/github.png" alt="GitHub"></a>
                                <a href="https://www.instagram.com/kry_1101/" target="_blank"><img src="./assets/instagram.png" alt="Instagram"></a>
                                <a href="https://x.com/KazuCos13" target="_blank"><img src="./assets/twitter.png" alt="Twitter"></a>
                            </div>


                        </div>




                        <!-- Buban -->


                        <div id="buban" class="profile <?php echo ($activeMember == 'buban') ? 'active' : '' ?>">
                            <div class="profileInfo gap-2 d-flex flex-lg-column justify-content-center align-items-center">

                                <img src="./assets/buban.png" alt="" class="img-fluid bg-secondary rounded-4 w-25 ">

                                <div class="profileInfoText text-center d-flex flex-column justify-content-center align-content-center">
                                    <p class="m-0 fw-bold fs-6">Aljhon Buban</p>
                                    <small class="mt-1 role"><span class="bg-secondary px-2 py-1 rounded text-light">Web Developer</span></small>
                                </div>

                            </div>

                            <hr class="border border-secondary w-100">


                            <div class="profileMoreInfo gap-3 flex-wrap d-flex justify-content-center align-items-center">



                                <div class="row justify-content-center align-items-center">

                                    <div class="col-md-6 col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/section.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Section</small>
                                                <p class="m-0">BSIS 209</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6   col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/course.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Program</small>
                                                <p class="m-0">Bachelor of Science in Information System</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/school.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">School</small>
                                                <p class="m-0">Kolehiyo ng Lungsod ng Dasmariñas</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-12">
                                        <div class=" p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/email.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Email</small>
                                                <p class="m-0">abuban@kld.edu.ph</p>
                                            </div>
                                        </div>
                                    </div>



                                </div>

                            </div>

                            <hr class="border border-secondary w-100">



                            <div class="socmed justify-content-center align-items-center d-flex flex-row gap-3">
                                <a href="#https://www.facebook.com/aljhon.buban.35" target="_blank"><img src="./assets/facebook-app-symbol.png" alt="Facebook"></a>
                                <a href="https://github.com/buban55aljhon-png" target="_blank"><img src="./assets/github.png" alt="GitHub"></a>
                                <a href="https://www.instagram.com/aljhnnnnxz/" target="_blank"><img src="./assets/instagram.png" alt="Instagram"></a>
                                <a href="https://x.com/kremlinrussia_e" target="_blank"><img src="./assets/twitter.png" alt="Twitter"></a>
                            </div>

                        </div>




                        <!-- Centino -->

                        <div id="centino" class="profile <?php echo ($activeMember == 'centino') ? 'active' : '' ?>">
                            <div class="profileInfo gap-2 d-flex flex-lg-column justify-content-center align-items-center">

                                <img src="./assets/centino.png" alt="" class="img-fluid bg-secondary rounded-4 w-25 ">

                                <div class="profileInfoText text-center d-flex flex-column justify-content-center align-content-center">
                                    <p class="m-0 fw-bold fs-6">Mark Anthony Centino</p>
                                    <small class="mt-1 role"><span class="bg-secondary px-2 py-1 rounded text-light">Web Developer</span></small>
                                </div>

                            </div>

                            <hr class="border border-secondary w-100">


                            <div class="profileMoreInfo gap-3 flex-wrap d-flex justify-content-center align-items-center">



                                <div class="row justify-content-center align-items-center ">

                                    <div class="col-md-6 col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/section.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Section</small>
                                                <p class="m-0">BSIS 209</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6   col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/course.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Program</small>
                                                <p class="m-0">Bachelor of Science in Information System</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/school.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">School</small>
                                                <p class="m-0">Kolehiyo ng Lungsod ng Dasmariñas</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-12">
                                        <div class=" p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/email.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Email</small>
                                                <p class="m-0">macentino@kld.edu.ph</p>
                                            </div>
                                        </div>
                                    </div>



                                </div>

                            </div>

                            <hr class="border border-secondary w-100">



                            <div class="socmed justify-content-center align-items-center d-flex flex-row gap-3">
                                <a href="https://www.facebook.com/share/1NgM7yjHLx/" target="_blank"><img src="./assets/facebook-app-symbol.png" alt="Facebook"></a>
                                <a href="https://github.com/markant22" target="_blank"><img src="./assets/github.png" alt="GitHub"></a>
                                <a href="https://www.instagram.com/mark.ant4?igsh=MTgzYTd4ZmhmbHNx" target="_blank"><img src="./assets/instagram.png" alt="Instagram"></a>
                                <a href="https://x.com/macmacdunks" target="_blank"><img src="./assets/twitter.png" alt="Twitter"></a>
                            </div>


                        </div>



                        <!-- Cereno -->

                        <div id="cereno" class="profile <?php echo ($activeMember == 'cereno') ? 'active' : '' ?>">
                            <div class="profileInfo gap-2 d-flex flex-lg-column justify-content-center align-items-center">

                                <img src="./assets/cereno.png" alt="" class="img-fluid bg-secondary rounded-4 w-25 ">

                                <div class="profileInfoText text-center d-flex flex-column justify-content-center align-content-center">
                                    <p class="m-0 fw-bold fs-6">Marianne Joi Cereno</p>
                                    <small class="mt-1 role"><span class="bg-secondary px-2 py-1 rounded text-light">Web Developer</span></small>
                                </div>

                            </div>

                            <hr class="border border-secondary w-100">


                            <div class="profileMoreInfo gap-3 flex-wrap d-flex justify-content-center align-items-center">



                                <div class="row justify-content-center align-items-center">

                                    <div class="col-md-6 col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/section.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Section</small>
                                                <p class="m-0">BSIS 209</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6   col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/course.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Program</small>
                                                <p class="m-0">Bachelor of Science in Information System</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/school.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">School</small>
                                                <p class="m-0">Kolehiyo ng Lungsod ng Dasmariñas</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-12">
                                        <div class=" p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/email.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Email</small>
                                                <p class="m-0">mjcereno@kld.edu.ph</p>
                                            </div>
                                        </div>
                                    </div>



                                </div>

                            </div>

                            <hr class="border border-secondary w-100">



                            <div class="socmed justify-content-center align-items-center d-flex flex-row gap-3">
                                <a href="https://www.facebook.com/mariannejoi.cereno" target="_blank"><img src="./assets/facebook-app-symbol.png" alt="Facebook"></a>
                                <a href="https://github.com/Android18-cyber" target="_blank"><img src="./assets/github.png" alt="GitHub"></a>
                                <a href="https://www.instagram.com/marian.joixzs?igsh=MXQzZ3dydjY0eGxiMQ==" target="_blank"><img src="./assets/instagram.png" alt="Instagram"></a>
                                <a href="https://x.com/mariannemalakas" target="_blank"><img src="./assets/twitter.png" alt="Twitter"></a>
                            </div>

                        </div>



                        <!-- Conosido -->

                        <div id="conosido" class="profile <?php echo ($activeMember == 'conosido') ? 'active' : '' ?>">

                            <div class="profileInfo gap-2 d-flex flex-lg-column justify-content-center align-items-center">

                                <img src="./assets/conosido.png" alt="" class="img-fluid bg-secondary rounded-4 w-25 ">

                                <div class="profileInfoText text-center d-flex flex-column justify-content-center align-content-center">
                                    <p class="m-0 fw-bold fs-6">Dionilo Conosido</p>
                                    <small class="mt-1 role"><span class="bg-secondary px-2 py-1 rounded text-light">Web Developer</span></small>
                                </div>

                            </div>

                            <hr class="border border-secondary w-100">


                            <div class="profileMoreInfo gap-3 flex-wrap d-flex justify-content-center align-items-center">



                                <div class="row justify-content-center align-items-center">

                                    <div class="col-md-6 col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/section.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Section</small>
                                                <p class="m-0">BSIS 209</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6   col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/course.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Program</small>
                                                <p class="m-0">Bachelor of Science in Information System</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-12">
                                        <div class="p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/school.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">School</small>
                                                <p class="m-0">Kolehiyo ng Lungsod ng Dasmariñas</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-12">
                                        <div class=" p-2 d-flex gap-2 justify-content-start align-items-center">

                                            <img src="./assets/email.png" class="socIcon p-2 shadow rounded border" width="40" alt="">

                                            <div class="contacts d-flex flex-column align-items-start justify-content-center">
                                                <small class="text-secondary">Email</small>
                                                <p class="m-0">dconosido@kld.edu.ph</p>
                                            </div>
                                        </div>
                                    </div>



                                </div>

                            </div>

                            <hr class="border border-secondary w-100">



                            <div class="socmed justify-content-center align-items-center d-flex flex-row gap-3">
                                <a href="https://www.facebook.com/dionilo.conosido.71" target="_blank"><img src="./assets/facebook-app-symbol.png" alt="Facebook"></a>
                                <a href="https://github.com/deyuuu-dgaf" target="_blank"><img src="./assets/github.png" alt="GitHub"></a>
                                <a href="https://www.instagram.com/_deeyuuuuuuuu?igsh=MWloenV4dmY2M2phMw==" target="_blank"><img src="./assets/instagram.png" alt="Instagram"></a>
                                <a href="https://x.com/_deyuuu?t=Xsu3QjWZJKC4Q_1xv90Ubg&s=09" target="_blank"><img src="./assets/twitter.png" alt="Twitter"></a>
                            </div>
                            
                        </div>

                </div>
                </aside>


       

            <div class="col border  shadow-sm px-3  py-5 position-relative secondCol">
                <nav class="shadow navbar navbar-expand-md  navbar position-absolute profileNav p-3  d-flex justify-content-end align-items-center">



                    <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarMembers">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="navbar-collapse  collapse max-h-100" id="navbarMembers">
                        <ul class="navbar-nav   d-flex flex-column flex-md-row justify-content-center align-items-center gap-1 gap-md-2 w-100">
                            <li class="nav-item"><a class="nav-link rounded-pill px-2 <?php echo ($activeMember == 'casiano') ? 'active' : '' ?>" href="./aboutUs.php?member=casiano" onclick="showAboutInfo('aboutCasiano', 'casiano')">Casiano</a></li>
                            <li class="nav-item"><a class="nav-link rounded-pill px-2 <?php echo ($activeMember == 'buban') ? 'active' : '' ?>" href="./aboutUs.php?member=buban" onclick="showAboutInfo('aboutBuban', 'buban')">Buban</a></li>
                            <li class="nav-item"><a class="nav-link rounded-pill px-2 <?php echo ($activeMember == 'cereno') ? 'active' : '' ?>" href="./aboutUs.php?member=cereno" onclick="showAboutInfo('aboutCereno','cereno')">Cereno</a></li>
                            <li class="nav-item"><a class="nav-link rounded-pill px-2 <?php echo ($activeMember == 'centino') ? 'active' : '' ?>" href="./aboutUs.php?member=centino" onclick="showAboutInfo('aboutCentino','centino')">Centino</a></li>
                            <li class="nav-item"><a class="nav-link rounded-pill px-2 <?php echo ($activeMember == 'conosido') ? 'active' : '' ?>" href="./aboutUs.php?member=conosido" onclick="showAboutInfo('aboutConosido','conosido')">Conosido</a></li>
                        </ul>
                    </div>


                </nav>

                <div class="infoContainer">
                    <div class="info <?php echo ($activeMember == 'casiano') ? 'active' : '' ?> mt-4" id="aboutCasiano">

                        <section class="p-3">
                            <h1 class="poppins">About me</h1>
                            <p class="m-0">I’m Prince Andrew Casiano, a BSIS 2nd year student at Kolehiyo ng Lungsod ng Dasmariñas, I'm passionate about learning and improving my skills. I enjoy exploring new ideas, working with others, and challenging myself to grow both academically and personally. </p>
                        </section>

                        <section class="p-3">
                            <h1 class="poppins">Skills</h1>
                            <div class="skillSet d-flex flex-wrap gap-2">
                                <small class="p-1 bg-secondary rounded text-light">UI/UX Design</small>
                                <small class="p-1 bg-secondary rounded text-light">Responsive Design</small>
                                <small class="p-1 bg-secondary rounded text-light">Problem Solving</small>
                                <small class="p-1 bg-secondary rounded text-light">Critical Thinking</small>
                                <small class="p-1 bg-secondary rounded text-light">Continuos Learning</small>
                                <small class="p-1 bg-secondary rounded text-light">Frontend Development</small>
                                <small class="p-1 bg-secondary rounded text-light">Web Design</small>
                            </div>
                        </section>

                        <section class="p-3">
                            <h1 class="poppins">Tech Stack</h1>
                            <div class="techStack d-flex flex-wrap gap-2">
                                <abbr title="HTML 5"><img src="./assets/html-5.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="CSS 3"><img src="./assets/css-3.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="Javascript"><img src="./assets/js.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="Java"><img src="./assets/java.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="Tailwind"><img src="./assets/Tailwind_CSS_Logo.svg.png" alt="" class="py-2 px-1 rounded shadow"></abbr>
                                <abbr title="Figma"><img src="./assets/figma.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="React"><img src="./assets/react.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="Bootstrap 5"><img src="./assets/bootstrap-5-logo-icon.webp" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="PHP"><img src="./assets/php.png" alt="" class="p-1 rounded shadow"></abbr>

                            </div>
                        </section>

                        <section class="p-md-3">
                            <div class="card">
                                <div class="card-header poppins">
                                    Motto in life
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <figure>
                                        <blockquote class="blockqoute fs-4">
                                            “I'm not stupid, I'm just too lazy to show how smart I am.”
                                        </blockquote>
                                        <figcaption class="blockquote-footer text-end pe-5">
                                            Houtarou Oreki from <cite>Hyouka</cite>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        </section>

                    </div>

                    <div class="info mt-4 <?php echo ($activeMember == 'buban') ? 'active' : '' ?>" id="aboutBuban">

                        <section class="p-3">
                            <h1 class="poppins">About me</h1>
                            <p class="m-0">Hi, I’m Aljhon Buban, an aspiring Web Developer passionate about building functional and creative systems. I enjoy working with databases, creating engaging content, and exploring different forms of digital media.</p>
                        </section>

                        <section class="p-3">
                            <h1 class="poppins">Skills</h1>
                            <div class="skillSet d-flex flex-wrap gap-2">
                                <small class="p-1 bg-secondary rounded text-light">Web Development</small>
                                <small class="p-1 bg-secondary rounded text-light">Database Management</small>
                                <small class="p-1 bg-secondary rounded text-light">System Design</small>
                                <small class="p-1 bg-secondary rounded text-light">Videographer</small>
                                <small class="p-1 bg-secondary rounded text-light">Director</small>
                                <small class="p-1 bg-secondary rounded text-light">Editor</small>
                                <small class="p-1 bg-secondary rounded text-light">Content Creator</small>

                            </div>
                        </section>

                        <section class="p-3">
                            <h1 class="poppins">Tech Stack</h1>
                            <div class="techStack d-flex flex-wrap gap-2">
                                <abbr title="HTML 5"><img src="./assets/html-5.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="CSS 3"><img src="./assets/css-3.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="Java"><img src="./assets/java.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="Bootstrap 5"><img src="./assets/bootstrap-5-logo-icon.webp" alt="" class="p-2 rounded shadow"></abbr>


                            </div>
                        </section>

                        <section class="p-md-3">
                            <div class="card">
                                <div class="card-header poppins">
                                    Motto in life
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <figure>
                                        <blockquote class="blockqoute fs-4">
                                            “Don’t stop hanggat di ka pa masarap.”
                                        </blockquote>
                                        <figcaption class="blockquote-footer text-end pe-5">
                                            Buban(2025)
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        </section>

                    </div>

                    <div class="info mt-4 <?php echo ($activeMember == 'centino') ? 'active' : '' ?>" id="aboutCentino">

                        <section class="p-3">
                            <h1 class="poppins">About me</h1>
                            <p class="m-0">
                                Mark Anthony
                                I’m Mark Anthony Centino, currently in my second year taking up Bachelor of Science in Information System at Kolehiyo ng Lungsod ng Dasmariñas. I like discovering new things, learning from experiences, and sharing ideas with others. For me, every challenge is a chance to improve and grow.</p>
                        </section>

                        <section class="p-3">
                            <h1 class="poppins">Skills</h1>
                            <div class="skillSet d-flex flex-wrap gap-2">
                                <small class="p-1 bg-secondary rounded text-light">Strong communication and interpersonal skills</small>
                                <small class="p-1 bg-secondary rounded text-light">Teamwork and collaboration</small>
                                <small class="p-1 bg-secondary rounded text-light">Time management and adaptability</small>
                                <small class="p-1 bg-secondary rounded text-light">Basic computer and research skills</small>
                            </div>
                        </section>

                        <section class="p-3">
                            <h1 class="poppins">Tech Stack</h1>
                            <div class="techStack d-flex flex-wrap gap-2">
                                <abbr title="HTML 5"><img src="./assets/html-5.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="CSS 3"><img src="./assets/css-3.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="Java"><img src="./assets/java.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="Bootstrap 5"><img src="./assets/bootstrap-5-logo-icon.webp" alt="" class="p-2 rounded shadow"></abbr>

                            </div>
                        </section>

                        <section class="p-md-3">
                            <div class="card">
                                <div class="card-header poppins">
                                    Motto in life
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <figure>
                                        <blockquote class="blockqoute fs-4">
                                            “Success begins with discipline, dedication, and determination.”
                                        </blockquote>
                                        <figcaption class="blockquote-footer text-end pe-5">
                                            Centino(2025)
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        </section>

                    </div>

                    <div class="info mt-4 <?php echo ($activeMember == 'cereno') ? 'active' : '' ?>" id="aboutCereno">

                        <section class="p-3">
                            <h1 class="poppins">About me</h1>
                            <p class="m-0">Hi i'm Marianne Joi Cereno. I consider myself a talkative, happy-go-lucky, and friendly person, though I can also be a bit shy sometimes.</p>
                        </section>

                        <section class="p-3">
                            <h1 class="poppins">Skills</h1>
                            <div class="skillSet d-flex flex-wrap gap-2">
                                <small class="p-1 bg-secondary rounded text-light">Fast learner</small>
                                <small class="p-1 bg-secondary rounded text-light">Adaptable</small>
                                <small class="p-1 bg-secondary rounded text-light">Approachable</small>
                                <small class="p-1 bg-secondary rounded text-light">Basic computer and research skills</small>
                            </div>
                        </section>

                        <section class="p-3">
                            <h1 class="poppins">Tech Stack</h1>
                            <div class="techStack d-flex flex-wrap gap-2">
                                <abbr title="HTML 5"><img src="./assets/html-5.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="CSS 3"><img src="./assets/css-3.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="Java"><img src="./assets/java.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="Bootstrap 5"><img src="./assets/bootstrap-5-logo-icon.webp" alt="" class="p-2 rounded shadow"></abbr>

                            </div>
                        </section>

                        <section class="p-md-3">
                            <div class="card">
                                <div class="card-header poppins">
                                    Motto in life
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <figure>
                                        <blockquote class="blockqoute fs-4">
                                            "Be kind, stay humble and keep learning."
                                        </blockquote>
                                        <figcaption class="blockquote-footer text-end pe-5">
                                            Cereno(2025)
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="info mt-4 <?php echo ($activeMember == 'conosido') ? 'active' : '' ?>" id="aboutConosido">

                        <section class="p-3">
                            <h1 class="poppins">About me</h1>
                            <p class="m-0">Hi, I'm Conosido, Dionilo P., 20, Second Year Student taking up BS in Information System at Kolehiyo ng Lungsod ng Dasmariñas. I aspire to become a web developer in the future.</p>
                        </section>

                        <section class="p-3">
                            <h1 class="poppins">Skills</h1>
                            <div class="skillSet d-flex flex-wrap gap-2">
                                <small class="p-1 bg-secondary rounded text-light">Programming</small>
                                <small class="p-1 bg-secondary rounded text-light">Time Management</small>
                                <small class="p-1 bg-secondary rounded text-light"> Problem Solving</small>
                            </div>
                        </section>

                        <section class="p-3">
                            <h1 class="poppins">Tech Stack</h1>
                            <div class="techStack d-flex flex-wrap gap-2">
                                <abbr title="HTML 5"><img src="./assets/html-5.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="CSS 3"><img src="./assets/css-3.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="Java"><img src="./assets/java.png" alt="" class="p-2 rounded shadow"></abbr>
                                <abbr title="Bootstrap 5"><img src="./assets/bootstrap-5-logo-icon.webp" alt="" class="p-2 rounded shadow"></abbr>

                            </div>
                        </section>

                        <section class="p-md-3">
                            <div class="card">
                                <div class="card-header poppins">
                                    Motto in life
                                </div>
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <figure>
                                        <blockquote class="blockqoute fs-4">
                                            “Life is like coding — you must debug problems to move forward.”
                                        </blockquote>
                                        <figcaption class="blockquote-footer text-end pe-5">
                                            Conosido(2025)
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        </section>


                    </div>

                </div>

            </div>


        </div>
        </div>
        




    </section>






    <?php include './includes/footer.php'; ?>
    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>

</html>