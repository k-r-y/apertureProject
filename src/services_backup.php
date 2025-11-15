<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Discover Aperture's photography and videography services for weddings, corporate events, celebrations, and creative shoots. Transparent pricing, professional quality, guaranteed delivery.">
  <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="./assets/camera.png" type="image/x-icon">
  <link rel="stylesheet" href="../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
  <title>Our Services - Professional Photography & Videography | Aperture</title>
</head>

<body>

  <?php include './includes/header.php'; ?>

  <!--------------------------------------- HERO CAROUSEL SECTION ---------------------------------------->
  <section class="w-100 min-vh-100 bg-light d-flex justify-content-center align-items-center position-relative p-2">
    <div class="list w-100 h-100 position-relative">

      <!-- Slide 1: Weddings & Engagements -->
      <div class="container active">
        <div class="row align-items-center gap-3 gap-md-0">
          <div class="col-md-6 service-hero-text">
            <h1 class="display-6 serif fw-bold">Weddings & Engagements</h1>
            <p class="fs-4 lh-lg">Your love story deserves to be told with elegance. From the first look to the final dance, we capture every emotion, every tear, every smile—preserving the magic of your special day for a lifetime.</p>
            <div class="d-flex flex-column flex-md-row gap-3 mt-4">
              <a href="logIn.php" class="btn bg-dark text-light px-5 py-3 fs-5">Book Your Wedding</a>
              <a href="#packages" class="btn border-dark px-5 py-3 fs-5">View Packages</a>
            </div>
          </div>
          <div class="col-md-6">
            <div class="service-img-wrapper">
              <img src="./assets/pexels-emma-bauso-1183828-2253831.jpg" alt="Wedding Photography" class="img-fluid rounded carouselImg" loading="lazy">
            </div>
          </div>
        </div>
      </div>

      <!-- Slide 2: Corporate Events -->
      <div class="container">
        <div class="row align-items-center gap-3 gap-md-0">
          <div class="col-md-6 service-hero-text">
            <h1 class="display-6 serif fw-bold">Corporate Excellence</h1>
            <p class="fs-4 lh-lg">Professional imagery that commands attention. We deliver corporate photography and videography that reflects your company's vision, capturing conferences, product launches, and executive portraits with precision.</p>
            <div class="d-flex flex-column flex-md-row gap-3 mt-4">
              <a href="logIn.php" class="btn bg-dark text-light px-5 py-3 fs-5">Book Corporate Event</a>
              <a href="#packages" class="btn border-dark px-5 py-3 fs-5">View Packages</a>
            </div>
          </div>
          <div class="col-md-6">
            <div class="service-img-wrapper">
              <img src="./assets/pexels-cottonbro-5077049.jpg" alt="Corporate Events" class="img-fluid rounded carouselImg" loading="lazy">
            </div>
          </div>
        </div>
      </div>

      <!-- Slide 3: Birthdays & Celebrations -->
      <div class="container">
        <div class="row align-items-center gap-3 gap-md-0">
          <div class="col-md-6 service-hero-text">
            <h1 class="display-6 serif fw-bold">Joyful Celebrations</h1>
            <p class="fs-4 lh-lg">Candid smiles, heartfelt laughter, and unforgettable memories. Whether it's a milestone birthday or intimate gathering, we capture the essence of your celebration with creativity and care.</p>
            <div class="d-flex flex-column flex-md-row gap-3 mt-4">
              <a href="logIn.php" class="btn bg-dark text-light px-5 py-3 fs-5">Book Your Celebration</a>
              <a href="#packages" class="btn border-dark px-5 py-3 fs-5">View Packages</a>
            </div>
          </div>
          <div class="col-md-6">
            <div class="service-img-wrapper">
              <img src="./assets/pexels-jessbaileydesign-768472.jpg" alt="Birthday Celebrations" class="img-fluid rounded carouselImg" loading="lazy">
            </div>
          </div>
        </div>
      </div>

      <!-- Slide 4: Creative Shoots -->
      <div class="container">
        <div class="row align-items-center gap-3 gap-md-0">
          <div class="col-md-6 service-hero-text">
            <h1 class="display-6 serif fw-bold">Creative Vision</h1>
            <p class="fs-4 lh-lg">Personal portraits, lifestyle photography, and cinematic projects tailored to your unique vision. From fashion shoots to artistic concepts, we bring your creative ideas to life with professional execution.</p>
            <div class="d-flex flex-column flex-md-row gap-3 mt-4">
              <a href="logIn.php" class="btn bg-dark text-light px-5 py-3 fs-5">Book Creative Session</a>
              <a href="#packages" class="btn border-dark px-5 py-3 fs-5">View Packages</a>
            </div>
          </div>
          <div class="col-md-6">
            <div class="service-img-wrapper">
              <img src="./assets/pexels-rdne-7648020.jpg" alt="Creative Photography" class="img-fluid rounded carouselImg" loading="lazy">
            </div>
          </div>
        </div>
      </div>

      <!-- Slide 5: Behind the Lens (Videography) -->
      <div class="container">
        <div class="row align-items-center gap-3 gap-md-0">
          <div class="col-md-6 service-hero-text">
            <h1 class="display-6 serif fw-bold">Cinematic Storytelling</h1>
            <p class="fs-4 lh-lg">More than recording—it's reliving the moment. Our videography services include 4K recording, color grading, audio mixing, and same-day edits that transform your event into a cinematic masterpiece.</p>
            <div class="d-flex flex-column flex-md-row gap-3 mt-4">
              <a href="logIn.php" class="btn bg-dark text-light px-5 py-3 fs-5">Book Videography</a>
              <a href="#packages" class="btn border-dark px-5 py-3 fs-5">View Packages</a>
            </div>
          </div>
          <div class="col-md-6">
            <div class="service-img-wrapper">
              <img src="./assets/high-angle-photo-camera-indoors-still-life.jpg" alt="Videography Services" class="img-fluid rounded carouselImg" loading="lazy">
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Carousel Navigation Buttons -->
    <div class="carouselBtn d-flex gap-3">
      <button class="btn border-dark" aria-label="Previous slide">
        <i class="bi bi-chevron-left"></i>
      </button>
      <button class="btn border-dark" aria-label="Next slide">
        <i class="bi bi-chevron-right"></i>
      </button>
    </div>
  </section>
  <!--------------------------------------- HERO CAROUSEL SECTION ---------------------------------------->

  <!--------------------------------------- PACKAGES SECTION ---------------------------------------->
  <section id="packages" class="w-100 py-5 bg-white">
    <div class="container py-5">
      <div class="row justify-content-center mb-5">
        <div class="col-md-10 text-center">
          <span class="section-label text-uppercase fw-bold" style="color: var(--gold); font-size: 1rem; letter-spacing: 4px;">Pricing Packages</span>
          <h2 class="display-2 serif fw-bold mt-3 mb-4">Choose Your Perfect Package</h2>
          <p class="fs-4 text-secondary lh-lg">Three tiers designed for every budget and occasion. All packages include professional photography/videography, expert editing, and guaranteed delivery.</p>
        </div>
      </div>

      <div class="row g-4 justify-content-center align-items-stretch">

        <!-- Essential Package -->
        <div class="col-lg-4 col-md-6">
          <div class="package-card rounded-4 p-5 h-100 position-relative shadow-sm border">
            <span class="package-badge position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill bg-dark text-light small fw-bold">POPULAR</span>
            <div class="text-center mb-4">
              <h3 class="serif h1 mb-3 fw-bold">Essential</h3>
              <p class="fs-5 text-secondary mb-4">Perfect for intimate celebrations</p>
              <div class="package-price display-2 fw-bold mb-2" style="color: var(--gold);">
                <small class="">₱</small>7,500
              </div>
              <p class="fs-5 text-uppercase text-secondary" style="letter-spacing: 2px;">3 hours coverage</p>
            </div>

            <div class="package-features mb-4">
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">40+ Professionally Edited Photos</span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">1–2 Minute Highlight Video</span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">Basic Color Grading & Audio Sync</span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">Online Gallery Access (1 Month)</span>
              </div>
              <div class="package-feature py-3 d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">1-2 Week Delivery</span>
              </div>
            </div>

            <div class="text-center mt-auto">
              <a href="logIn.php" class="btn btn-outline-dark w-100 py-3 fs-5 btn-book-package" data-package="Essential" data-price="7500">Book Essential</a>
              <p class="text-secondary mt-3 mb-0 fs-6">Add-ons available from ₱500</p>
            </div>
          </div>
        </div>

        <!-- Elite Package (Featured) -->
        <div class="col-lg-4 col-md-6">
          <div class="package-card-featured rounded-4 p-5 h-100 position-relative shadow border-3" style="border-color: var(--gold) !important;">
            <span class="package-badge position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill text-dark small fw-bold" style="background-color: var(--gold);">BEST VALUE</span>
            <div class="text-center mb-4">
              <h3 class="serif h1 mb-3 fw-bold">Elite</h3>
              <p class="fs-5 text-secondary mb-4">Most comprehensive coverage</p>
              <div class="package-price display-2 fw-bold mb-2" style="color: var(--gold);">
                <small class="">₱</small>15,000
              </div>
              <p class="fs-5 text-uppercase text-secondary" style="letter-spacing: 2px;">4 hours coverage</p>
            </div>

            <div class="package-features mb-4">
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">100+ Professionally Edited Photos</span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">3–5 Minute Highlight Film</span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">Full Event Video (10–15 minutes)</span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">Drone Coverage (Optional Add-on)</span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">Audio Recording for Speeches/Vows</span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">On-Site Lighting Setup</span>
              </div>
              <div class="package-feature py-3 d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">Online Gallery Access (3 Months)</span>
              </div>
            </div>

            <div class="text-center mt-auto">
              <a href="logIn.php" class="btn w-100 py-3 fs-5 text-dark fw-bold btn-book-package" style="background-color: var(--gold);" data-package="Elite" data-price="15000">Book Elite</a>
              <p class="text-secondary mt-3 mb-0 fs-6">Add-ons available from ₱1,500</p>
            </div>
          </div>
        </div>

        <!-- Premium Package -->
        <div class="col-lg-4 col-md-6">
          <div class="package-card rounded-4 p-5 h-100 position-relative shadow-sm border">
            <span class="package-badge position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill bg-dark text-light small fw-bold">LUXURY</span>
            <div class="text-center mb-4">
              <h3 class="serif h1 mb-3 fw-bold">Premium</h3>
              <p class="fs-5 text-secondary mb-4">Complete cinematic experience</p>
              <div class="package-price display-2 fw-bold mb-2" style="color: var(--gold);">
                <small class="">₱</small>25,000
              </div>
              <p class="fs-5 text-uppercase text-secondary" style="letter-spacing: 2px;">8 hours coverage</p>
            </div>

            <div class="package-features mb-4">
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">Unlimited Raw + 200 Edited Photos</span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">7–10 Min Cinematic Highlight Film (HD)</span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">Full Event Film (20+ minutes)</span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5"><strong>Drone Coverage Included</strong></span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5"><strong>Same-Day Edit (SDE) Included</strong></span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">Audio Mixing & Cinematic Grading</span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">Personalized Gallery (1 Year Access)</span>
              </div>
              <div class="package-feature py-3 border-bottom d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">Premium USB Copy with Case</span>
              </div>
              <div class="package-feature py-3 d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-4" style="color: var(--gold);"></i>
                <span class="fs-5">Printed Photo Album (40 Pages)</span>
              </div>
            </div>

            <div class="text-center mt-auto">
              <a href="logIn.php" class="btn btn-outline-dark w-100 py-3 fs-5 btn-book-package" data-package="Premium" data-price="25000">Book Premium</a>
              <p class="text-secondary mt-3 mb-0 fs-6">Add-ons available from ₱1,000</p>
            </div>
          </div>
        </div>

      </div>

      <!-- Comparison Button -->
      <div class="row mt-5">
        <div class="col text-center">
          <button id="toggleComparison" class="btn btn-outline-dark btn-lg px-5 py-3 fs-5">Compare All Packages</button>
        </div>
      </div>

      <!-- Package Comparison Table -->
      <div id="comparisonTable" class="row mt-5" style="display: none;">
        <div class="col-12">
          <div class="table-responsive">
            <table class="table comparison-table bg-white shadow rounded fs-5">
              <thead class="text-light" style="background-color: var(--dark);">
                <tr>
                  <th class="py-4 ps-4">Feature</th>
                  <th class="text-center py-4">Essential<br><small class="fs-6">₱7,500</small></th>
                  <th class="text-center py-4" style="background-color: rgba(212, 175, 55, 0.2);">Elite<br><small class="fs-6">₱15,000</small></th>
                  <th class="text-center py-4">Premium<br><small class="fs-6">₱25,000</small></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="ps-4 py-3"><strong>Coverage Hours</strong></td>
                  <td class="text-center py-3">3 hours</td>
                  <td class="text-center py-3" style="background-color: rgba(212, 175, 55, 0.05);">4 hours</td>
                  <td class="text-center py-3">8 hours</td>
                </tr>
                <tr>
                  <td class="ps-4 py-3"><strong>Edited Photos</strong></td>
                  <td class="text-center py-3">40+</td>
                  <td class="text-center py-3" style="background-color: rgba(212, 175, 55, 0.05);">100+</td>
                  <td class="text-center py-3">200+ (+ Raw)</td>
                </tr>
                <tr>
                  <td class="ps-4 py-3"><strong>Highlight Video</strong></td>
                  <td class="text-center py-3">1-2 min</td>
                  <td class="text-center py-3" style="background-color: rgba(212, 175, 55, 0.05);">3-5 min</td>
                  <td class="text-center py-3">7-10 min (HD)</td>
                </tr>
                <tr>
                  <td class="ps-4 py-3"><strong>Full Event Film</strong></td>
                  <td class="text-center py-3"><i class="bi bi-x-lg text-danger"></i></td>
                  <td class="text-center py-3" style="background-color: rgba(212, 175, 55, 0.05);">10-15 min</td>
                  <td class="text-center py-3">20+ min</td>
                </tr>
                <tr>
                  <td class="ps-4 py-3"><strong>Drone Coverage</strong></td>
                  <td class="text-center py-3"><i class="bi bi-x-lg text-danger"></i></td>
                  <td class="text-center py-3" style="background-color: rgba(212, 175, 55, 0.05);">Add-on</td>
                  <td class="text-center py-3"><i class="bi bi-check-lg" style="color: var(--gold); font-size: 1.5rem;"></i></td>
                </tr>
                <tr>
                  <td class="ps-4 py-3"><strong>Same-Day Edit (SDE)</strong></td>
                  <td class="text-center py-3"><i class="bi bi-x-lg text-danger"></i></td>
                  <td class="text-center py-3" style="background-color: rgba(212, 175, 55, 0.05);">Add-on</td>
                  <td class="text-center py-3"><i class="bi bi-check-lg" style="color: var(--gold); font-size: 1.5rem;"></i></td>
                </tr>
                <tr>
                  <td class="ps-4 py-3"><strong>Photo Album</strong></td>
                  <td class="text-center py-3"><i class="bi bi-x-lg text-danger"></i></td>
                  <td class="text-center py-3" style="background-color: rgba(212, 175, 55, 0.05);">Add-on</td>
                  <td class="text-center py-3"><i class="bi bi-check-lg" style="color: var(--gold); font-size: 1.5rem;"></i> 40 pages</td>
                </tr>
                <tr>
                  <td class="ps-4 py-3"><strong>Gallery Access</strong></td>
                  <td class="text-center py-3">1 month</td>
                  <td class="text-center py-3" style="background-color: rgba(212, 175, 55, 0.05);">3 months</td>
                  <td class="text-center py-3">1 year</td>
                </tr>
                <tr>
                  <td class="ps-4 py-3"><strong>USB Copy</strong></td>
                  <td class="text-center py-3">Add-on</td>
                  <td class="text-center py-3" style="background-color: rgba(212, 175, 55, 0.05);"><i class="bi bi-check-lg" style="color: var(--gold); font-size: 1.5rem;"></i></td>
                  <td class="text-center py-3"><i class="bi bi-check-lg" style="color: var(--gold); font-size: 1.5rem;"></i> Premium</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </section>
  <!--------------------------------------- PACKAGES SECTION ---------------------------------------->

  <!--------------------------------------- ADD-ONS SECTION ---------------------------------------->
  <section class="w-100 py-5 bg-light">
    <div class="container py-5">
      <div class="row justify-content-center mb-5">
        <div class="col-md-10 text-center">
          <span class="section-label text-uppercase fw-bold" style="color: var(--gold); font-size: 1rem; letter-spacing: 4px;">Enhance Your Package</span>
          <h2 class="display-2 serif fw-bold mt-3 mb-4">Premium Add-Ons</h2>
          <p class="fs-4 text-secondary lh-lg">Customize your experience with our professional add-ons. Prices vary by package tier.</p>
        </div>
      </div>

      <div class="row g-4">

        <!-- Add-on 1 -->
        <div class="col-md-6 col-lg-4">
          <div class="addon-card border rounded-4 p-4 bg-white shadow-sm h-100">
            <div class="addon-card-icon mb-3">
              <i class="bi bi-camera-video-fill" style="font-size: 3rem; color: var(--gold);"></i>
            </div>
            <h4 class="serif mb-3 h3">Drone Aerial Shots</h4>
            <p class="fs-5 text-secondary mb-4">Cinematic aerial photography and videography for breathtaking perspectives of your event.</p>
            <div class="addon-price h2 fw-bold" style="color: var(--gold);">₱2,000 - ₱5,000</div>
          </div>
        </div>

        <!-- Add-on 2 -->
        <div class="col-md-6 col-lg-4">
          <div class="addon-card border rounded-4 p-4 bg-white shadow-sm h-100">
            <div class="addon-card-icon mb-3">
              <i class="bi bi-lightning-charge-fill" style="font-size: 3rem; color: var(--gold);"></i>
            </div>
            <h4 class="serif mb-3 h3">Same-Day Edit (SDE)</h4>
            <p class="fs-5 text-secondary mb-4">Watch your highlight reel during your reception. Perfect for weddings and celebrations.</p>
            <div class="addon-price h2 fw-bold" style="color: var(--gold);">₱3,500 - ₱8,000</div>
          </div>
        </div>

        <!-- Add-on 3 -->
        <div class="col-md-6 col-lg-4">
          <div class="addon-card border rounded-4 p-4 bg-white shadow-sm h-100">
            <div class="addon-card-icon mb-3">
              <i class="bi bi-book-fill" style="font-size: 3rem; color: var(--gold);"></i>
            </div>
            <h4 class="serif mb-3 h3">Premium Photo Album</h4>
            <p class="fs-5 text-secondary mb-4">Museum-quality printed album with your best moments. Available in 30 or 40-page editions.</p>
            <div class="addon-price h2 fw-bold" style="color: var(--gold);">₱2,000 - ₱4,000</div>
          </div>
        </div>

        <!-- Add-on 4 -->
        <div class="col-md-6 col-lg-4">
          <div class="addon-card border rounded-4 p-4 bg-white shadow-sm h-100">
            <div class="addon-card-icon mb-3">
              <i class="bi bi-clock-history" style="font-size: 3rem; color: var(--gold);"></i>
            </div>
            <h4 class="serif mb-3 h3">Extended Coverage</h4>
            <p class="fs-5 text-secondary mb-4">Add extra hours to your package for complete event documentation from start to finish.</p>
            <div class="addon-price h2 fw-bold" style="color: var(--gold);">₱1,000 - ₱2,000/hr</div>
          </div>
        </div>

        <!-- Add-on 5 -->
        <div class="col-md-6 col-lg-4">
          <div class="addon-card border rounded-4 p-4 bg-white shadow-sm h-100">
            <div class="addon-card-icon mb-3">
              <i class="bi bi-usb-symbol" style="font-size: 3rem; color: var(--gold);"></i>
            </div>
            <h4 class="serif mb-3 h3">USB Copy with Case</h4>
            <p class="fs-5 text-secondary mb-4">All your photos and videos delivered in a premium USB drive with custom case.</p>
            <div class="addon-price h2 fw-bold" style="color: var(--gold);">₱500 - ₱1,000</div>
          </div>
        </div>

        <!-- Add-on 6 -->
        <div class="col-md-6 col-lg-4">
          <div class="addon-card border rounded-4 p-4 bg-white shadow-sm h-100">
            <div class="addon-card-icon mb-3">
              <i class="bi bi-broadcast" style="font-size: 3rem; color: var(--gold);"></i>
            </div>
            <h4 class="serif mb-3 h3">Livestream Setup</h4>
            <p class="fs-5 text-secondary mb-4">Share your special moments with loved ones who can't attend. Professional streaming service.</p>
            <div class="addon-price h2 fw-bold" style="color: var(--gold);">₱3,000</div>
          </div>
        </div>

      </div>
    </div>
  </section>
  <!--------------------------------------- ADD-ONS SECTION ---------------------------------------->

  <!--------------------------------------- CTA SECTION ---------------------------------------->
  <section class="cta-section w-100 py-5 d-flex justify-content-center align-items-center position-relative" style="min-height: 70vh; background: linear-gradient(135deg, rgba(33, 37, 41, 0.95) 0%, rgba(33, 37, 41, 0.90) 100%), url('./assets/wp2815597-black-texture-wallpapers.jpg'); background-size: cover; background-position: center; background-attachment: fixed;">
    <div class="container text-center position-relative" style="z-index: 2;">
      <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
          <span class="section-label text-uppercase fw-bold d-block mb-3" style="color: var(--gold); font-size: 1rem; letter-spacing: 4px;">Ready to Begin?</span>
          <h2 class="display-6 serif fw-bold mb-4 text-light">Let's Capture Your Story</h2>
          <p class=" text-light mb-5 lh-lg" style="opacity: 0.9;">From weddings to corporate events, celebrations to creative shoots—we're here to preserve your most precious moments with artistry, professionalism, and unwavering dedication to excellence.</p>
          <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
            <a href="logIn.php" class="btn btn-lg px-5 py-4 fs-4 fw-bold text-dark" style="background-color: var(--gold);">Book Your Session Now</a>
            <a href="aboutCompany.php" class="btn btn-outline-light btn-lg px-5 py-4 fs-4">Learn About Us</a>
          </div>
          <p class="text-light mt-4 mb-0 fs-5" style="opacity: 0.7;">Questions? <a href="aboutCompany.php#contact" class="text-light fw-bold" style="text-decoration: underline;">Contact our team</a> for personalized assistance.</p>
        </div>
      </div>
    </div>
  </section>
  <!--------------------------------------- CTA SECTION ---------------------------------------->

  <?php include './includes/footer.php'; ?>

  <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
  <script>
    // Package comparison toggle functionality
    const toggleComparisonBtn = document.getElementById('toggleComparison');
    const comparisonTable = document.getElementById('comparisonTable');

    if (toggleComparisonBtn && comparisonTable) {
      toggleComparisonBtn.addEventListener('click', function() {
        if (comparisonTable.style.display === 'none') {
          comparisonTable.style.display = 'block';
          this.textContent = 'Hide Comparison';
          comparisonTable.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
          comparisonTable.style.display = 'none';
          this.textContent = 'Compare All Packages';
        }
      });
    }

    // Store selected package in sessionStorage for booking page
    const bookingButtons = document.querySelectorAll('.btn-book-package');
    bookingButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        const packageName = this.dataset.package;
        const packagePrice = this.dataset.price;

        if (packageName && packagePrice) {
          sessionStorage.setItem('selectedPackage', packageName);
          sessionStorage.setItem('selectedPrice', packagePrice);
        }
      });
    });
  </script>

</body>

</html>
