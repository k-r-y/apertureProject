<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="./assets/camera.png" type="image/x-icon">
  <title>Services - Aperture</title>
</head>

<body>
  <?php include './includes/header.php'; ?>

  <section class="w-100 min-vh-100 bg-white d-flex justify-content-center align-items-center position-relative p-2">
    <div class="list w-100 h-100 position-relative">
      <div class="container active">
        <div class="row align-items-center gap-3 gap-md-0">
          <div class="col-md-6">
            <h1 class="display-2">Weddings & Engagements</h1>
            <p>From intimate ceremonies to grand celebrations, we capture every emotion and detail that tells your love story.</p>
            <div class="d-flex flex-column flex-md-row gap-3 mt-3">
              <a href="login.php" class="btn bg-dark text-light px-4">Book Now</a>
              <a href="register.php" class="btn border-dark px-4">Sign Up</a>
            </div>
          </div>
          <div class="col-md-6">
            <img src="./assets/pexels-emma-bauso-1183828-2253831.jpg" alt="" class="img-fluid rounded carouselImg">
          </div>
        </div>
      </div>

      <div class="container">+
        <div class="row align-items-center gap-5 gap-md-0">
          <div class="col-md-6">
            <h1 class="display-2">Corporate Events</h1>
            <p>Professional coverage for conferences, product launches, and company milestones — because your brand deserves to shine.</p>
            <div class="d-flex flex-column flex-md-row gap-3 mt-3">
              <a href="login.php" class="btn bg-dark text-light px-4">Book Now</a>
              <a href="register.php" class="btn border-dark px-4">Sign Up</a>
            </div>
          </div>
          <div class="col-md-6">
            <img src="./assets/pexels-cottonbro-5077049.jpg" alt="" class="img-fluid rounded carouselImg">
          </div>
        </div>
      </div>

      <div class="container">
        <div class="row align-items-center gap-5 gap-md-0">
          <div class="col-md-6">
            <h1 class="display-2">Birthdays & Celebrations</h1>
            <p>Candid smiles, joyful laughter, and unforgettable memories preserved in every shot.</p>
            <div class="d-flex flex-column flex-md-row gap-3 mt-3">
              <a href="login.php" class="btn bg-dark text-light px-4">Book Now</a>
              <a href="register.php" class="btn border-dark px-4">Sign Up</a>
            </div>
          </div>
          <div class="col-md-6">
            <img src="./assets/pexels-jessbaileydesign-768472.jpg" alt="" class="img-fluid rounded carouselImg">
          </div>
        </div>
      </div>

      <div class="container">
        <div class="row align-items-center gap-5 gap-md-0">
          <div class="col-md-6">
            <h1 class="display-2">Creative Shoots</h1>
            <p>Personal portraits, lifestyle photography, and cinematic video projects tailored for your unique vision.</p>
            <div class="d-flex flex-column flex-md-row gap-3 mt-3">
              <a href="login.php" class="btn bg-dark text-light px-4">Book Now</a>
              <a href="register.php" class="btn border-dark px-4">Sign Up</a>
            </div>
          </div>
          <div class="col-md-6">
            <img src="./assets/pexels-rdne-7648020.jpg" alt="" class="img-fluid rounded carouselImg">
          </div>
        </div>
      </div>

      <div class="container">
        <div class="row align-items-center gap-5 gap-md-0">
          <div class="col-md-6">
            <h1 class="display-2">Behind the Lens (Videography)</h1>
            <p>Dynamic highlight reels and cinematic storytelling — more than just recording, it’s reliving the moment.</p>
            <div class="d-flex flex-column flex-md-row gap-3 mt-3">
              <a href="login.php" class="btn bg-dark text-light px-4">Book Now</a>
              <a href="register.php" class="btn border-dark px-4">Sign Up</a>
            </div>
          </div>
          <div class="col-md-6">
            <img src="./assets/high-angle-photo-camera-indoors-still-life.jpg" alt="" class="img-fluid rounded carouselImg">
          </div>
        </div>
      </div>

    </div>
    <div class="carouselBtn d-flex gap-3">
      <button class="btn border-dark">&lt;</button>
      <button class="btn border-dark">&gt;</button>
    </div>

  </section>

  <section id="gallery" class="w-100 min-vh-100 bg-light d-flex justify-content-center align-items-center position-relative px-2 py-5">

    <div class="container-fluid justify-content-center align-items-center">
      <div class="row justify-content-center align-items-center">
        <div class="col-md-8">
          <h1 class="serif display-1 text-center my-5">Gallery</h1>
        </div>
      </div>
      <div class="row justify-content-center align-items-center">

        <div class="container px-4">

          <div class="d-flex  flex-row gap-3 justify-content-center align-items-start" style="flex-wrap: wrap; ">

            <!-- First Column -->
            <div class="d-flex flex-column gap-3" style="flex-basis: 27rem;">
              <div class="galPhotos rounded">
                <img src="./assets/wp2815535-black-texture-wallpapers.jpg" alt="" class="img-fluid">
                <div class="text-overlay">
                  <h1 class="serif text-light fw-semibold">Weddings</h1>
                  <p class="serif text-light">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Dolore, magni.</p>
                </div>
              </div>
              <div class="galPhotos rounded">
                <img src="./assets/front-view-photographer-with-camera copy.jpg" alt="" class="img-fluid">
                <div class="text-overlay">
                  <h1 class="serif text-light fw-semibold">Weddings</h1>
                  <p class="serif text-light">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Dolore, magni.</p>
                </div>
              </div>
              <div class="galPhotos rounded">
                <img src="./assets/beautiful-bride-groom-having-beach-wedding.jpg" alt="" class="img-fluid">
                <div class="text-overlay">
                  <h1 class="serif text-light fw-semibold">Weddings</h1>
                  <p class="serif text-light">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Dolore, magni.</p>
                </div>
              </div>
              <div class="galPhotos rounded">
                <img src="./assets/bride-groom-couple-wedding.jpg" alt="" class="img-fluid">
                <div class="text-overlay">
                  <h1 class="serif text-light fw-semibold">Weddings</h1>
                  <p class="serif text-light">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Dolore, magni.</p>
                </div>
              </div>
            </div>

            <!-- Second Column -->
            <div class="d-flex flex-column gap-3" style="flex-basis: 27rem;">
              <div class="galPhotos rounded">
                <img src="./assets/beautiful-bride-groom-having-beach-wedding.jpg" alt="" class="img-fluid">
                <div class="text-overlay">
                  <h1 class="serif text-light fw-semibold">Weddings</h1>
                  <p class="serif text-light">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Dolore, magni.</p>
                </div>
              </div>
              <div class="galPhotos rounded">
                <img src="./assets/pexels-emma-bauso-1183828-2253831.jpg" alt="" class="img-fluid">
                <div class="text-overlay">
                  <h1 class="serif text-light fw-semibold">Weddings</h1>
                  <p class="serif text-light">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Dolore, magni.</p>
                </div>
              </div>
              <div class="galPhotos rounded">
                <img src="./assets/bride-groom-couple-wedding.jpg" alt="" class="img-fluid">
                <div class="text-overlay">
                  <h1 class="serif text-light fw-semibold">Weddings</h1>
                  <p class="serif text-light">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Dolore, magni.</p>
                </div>
              </div>
              <div class="galPhotos rounded">
                <img src="./assets/bride-groom-couple-wedding.jpg" alt="" class="img-fluid">
                <div class="text-overlay">
                  <h1 class="serif text-light fw-semibold">Weddings</h1>
                  <p class="serif text-light">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Dolore, magni.</p>
                </div>
              </div>
            </div>

            <!-- Third Column -->
            <div class="d-flex flex-column gap-3" style="flex-basis: 27rem;">
              <div class="galPhotos rounded">
                <img src="./assets/bride-groom-couple-wedding.jpg" alt="" class="img-fluid">
                <div class="text-overlay">
                  <h1 class="serif text-light fw-semibold">Weddings</h1>
                  <p class="serif text-light">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Dolore, magni.</p>
                </div>
              </div>
              <div class="galPhotos rounded">
                <img src="./assets/close-up-teenage-boy-taking-photography-click-retro-vintage-photo-camera-against-white-background.jpg" alt="" class="img-fluid">
                <div class="text-overlay">
                  <h1 class="serif text-light fw-semibold">Weddings</h1>
                  <p class="serif text-light">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Dolore, magni.</p>
                </div>
              </div>
              <div class="galPhotos rounded">
                <img src="./assets/beautiful-bride-groom-having-beach-wedding.jpg" alt="" class="img-fluid">
                <div class="text-overlay">
                  <h1 class="serif text-light fw-semibold">Weddings</h1>
                  <p class="serif text-light">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Dolore, magni.</p>
                </div>
              </div>

            </div>

          </div>

        </div>


  </section>


  <?php include './includes/footer.php'; ?>
  <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
</body>

</html>