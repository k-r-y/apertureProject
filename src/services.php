<?php

require_once 'includes/functions/session.php';


?>

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

<!-- @src/services.php  make the cta button on package cards align with other cta button on packages. also make the cards on add ons smaller by reducing the spacing and padding. also add a gallery section. then add border radius to everything. think longer and don't stop until you finish it. make sure that you follow the design system and the whole page is responsive, modern design, and luxury -->

  <?php include './includes/header.php'; ?>

  <!--------------------------------------- HERO SECTION ---------------------------------------->
  <section class="w-100 min-vh-100 d-flex justify-content-center align-items-center position-relative" style="background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);">
    <div class="position-absolute w-100 h-100" style="opacity: 0.03; background-image: url('./assets/wp2815597-black-texture-wallpapers.jpg'); background-size: cover; background-position: center;"></div>

    <div class="container position-relative" style="z-index: 2;">
      <div class="row justify-content-center text-center">
        <div class="col-lg-10">
          <div class="mb-3">
            <span class="d-inline-block px-4 py-2 rounded-pill" style="background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3); font-size: 0.75rem; letter-spacing: 3px; color: var(--gold); font-weight: 600;">APERTURE STUDIOS</span>
          </div>
          <h1 class="serif mb-4 text-light" style="font-size: clamp(2.5rem, 6vw, 5rem); font-weight: 300; line-height: 1.2; letter-spacing: -1px;">Capturing Moments,<br><span style="font-style: italic; color: var(--gold);">Crafting Legacies</span></h1>
          <p class="mb-5 mx-auto text-light" style="max-width: 650px; font-size: 1.05rem; line-height: 1.8; opacity: 0.8; font-weight: 300;">Bespoke photography and videography services for discerning clients who demand excellence. From intimate celebrations to grand corporate events, we transform fleeting moments into timeless art.</p>
          <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
            <a href="#packages" class="btn px-5 py-3" style="background-color: var(--gold); color: #000; font-weight: 500; letter-spacing: 0.5px; border-radius: 6px; font-size: 0.9rem; transition: all 0.4s ease;">View Packages</a>
            <a href="logIn.php" class="btn px-5 py-3" style="background: transparent; color: var(--gold); border: 1px solid var(--gold); font-weight: 500; letter-spacing: 0.5px; border-radius: 6px; font-size: 0.9rem; transition: all 0.4s ease;">Book now</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-4" style="animation: bounce 2s infinite;">
      <div class="d-flex flex-column align-items-center">
        <span class="text-light mb-2" style="font-size: 0.7rem; letter-spacing: 2px; opacity: 0.6;">SCROLL</span>
        <i class="bi bi-arrow-down text-light" style="opacity: 0.6;"></i>
      </div>
    </div>
  </section>
  <!--------------------------------------- HERO SECTION ---------------------------------------->

  <!--------------------------------------- PACKAGES SECTION ---------------------------------------->
  <section id="packages" class="w-100 py-5" style="background: linear-gradient(to bottom, #fafafa 0%, #ffffff 100%);">
    <div class="container py-5">
      <div class="row justify-content-center mb-5">
        <div class="col-md-10 text-center">
          <span class="d-inline-block px-4 py-2 rounded-pill mb-3" style="background: rgba(212, 175, 55, 0.08); border: 1px solid rgba(212, 175, 55, 0.2); font-size: 0.7rem; letter-spacing: 3px; color: var(--gold); font-weight: 600;">OUR COLLECTIONS</span>
          <h2 class="serif mb-3" style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 300; line-height: 1.3; letter-spacing: -0.5px; color: #1a1a1a;">Tailored to Your Vision</h2>
          <p class="mb-0 mx-auto" style="max-width: 600px; font-size: 0.95rem; line-height: 1.7; opacity: 0.7; font-weight: 300; color: #4a4a4a;">Three meticulously crafted tiers designed for discerning clients. Every collection includes professional coverage, expert post-production, and guaranteed delivery within 1-2 weeks.</p>
        </div>
      </div>

      <div class="row g-4 justify-content-center align-items-stretch">

        <!-- Essential Package -->
        <div class="col-lg-4 col-md-6">
          <div class="package-card h-100 position-relative d-flex flex-column" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; padding: 2rem 1.75rem; transition: all 0.4s ease;">
            <span class="position-absolute top-0 start-0 mt-3 ms-3 px-2 py-1" style="background: rgba(212, 175, 55, 0.1); font-size: 0.6rem; letter-spacing: 2px; color: var(--gold); font-weight: 600; border-radius: 4px;">INTIMATE</span>

            <div class="mb-3" style="padding-top: 1.5rem;">
              <h3 class="serif mb-1" style="font-size: 1.5rem; font-weight: 300; letter-spacing: 0.5px; color: #1a1a1a;">Essential</h3>
              <p class="mb-3" style="font-size: 0.8rem; line-height: 1.5; opacity: 0.6; font-weight: 300;">Perfect for intimate celebrations</p>

              <div class="d-flex align-items-baseline mb-1">
                <span style="font-size: 0.7rem; color: #999; margin-right: 0.25rem;">₱</span>
                <span class="serif" style="font-size: 2rem; font-weight: 300; letter-spacing: -1px; color: var(--gold);">7,500</span>
              </div>
              <div class="d-flex align-items-center gap-2 mb-2">
                <span style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; color: #999; font-weight: 500;">2 hours</span>
                <span style="font-size: 0.65rem; color: #ccc;">•</span>
                <span style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; color: #999; font-weight: 500;">1 Photographer</span>
              </div>
              <p style="font-size: 0.7rem; opacity: 0.5; margin: 0;">+ ₱1,000/hr</p>
            </div>

            <div class="mb-3 flex-grow-1" style="border-top: 1px solid rgba(0,0,0,0.05); padding-top: 1.25rem;">
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span style="font-size: 0.8rem; line-height: 1.5; font-weight: 300; color: #4a4a4a;">40+ Edited Photos</span>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span style="font-size: 0.8rem; line-height: 1.5; font-weight: 300; color: #4a4a4a;">1–2 Min Highlight Video</span>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span style="font-size: 0.8rem; line-height: 1.5; font-weight: 300; color: #4a4a4a;">Color Grading & Audio</span>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span style="font-size: 0.8rem; line-height: 1.5; font-weight: 300; color: #4a4a4a;">Gallery Access (1 Month)</span>
              </div>
              <div class="d-flex align-items-start">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span style="font-size: 0.8rem; line-height: 1.5; font-weight: 300; color: #4a4a4a;">Free Consultation</span>
              </div>
            </div>

            <div class="mt-auto" style="padding-top: 1.25rem;">
              <a href="logIn.php" class="btn w-100 py-2 btn-book-package" data-package="Essential" data-price="7500" style="background: transparent; color: #1a1a1a; border: 1px solid #1a1a1a; font-size: 0.75rem; letter-spacing: 1.5px; font-weight: 500; border-radius: 6px; transition: all 0.3s ease;">RESERVE NOW</a>
              <p class="text-center mt-2 mb-0" style="font-size: 0.65rem; opacity: 0.5;">20% deposit required</p>
            </div>
          </div>
        </div>

        <!-- Premium Package (Featured) -->
        <div class="col-lg-4 col-md-6">
          <div class="package-card-featured h-100 position-relative d-flex flex-column" style="background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%); border: 2px solid var(--gold); border-radius: 8px; padding: 2rem 1.75rem; transition: all 0.4s ease; transform: scale(1.05);">
            <span class="position-absolute top-0 end-0 mt-3 me-3 px-2 py-1" style="background: var(--gold); font-size: 0.6rem; letter-spacing: 2px; color: #000; font-weight: 600; border-radius: 4px;">RECOMMENDED</span>

            <div class="mb-3" style="padding-top: 1.5rem;">
              <h3 class="serif mb-1 text-light" style="font-size: 1.5rem; font-weight: 300; letter-spacing: 0.5px;">Premium</h3>
              <p class="mb-3 text-light" style="font-size: 0.8rem; line-height: 1.5; opacity: 0.6; font-weight: 300;">Most comprehensive coverage</p>

              <div class="d-flex align-items-baseline mb-1">
                <span class="text-light" style="font-size: 0.7rem; opacity: 0.6; margin-right: 0.25rem;">₱</span>
                <span class="serif" style="font-size: 2rem; font-weight: 300; letter-spacing: -1px; color: var(--gold);">15,000</span>
              </div>
              <div class="d-flex align-items-center gap-2 mb-2">
                <span class="text-light" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6; font-weight: 500;">4 hours</span>
                <span class="text-light" style="font-size: 0.65rem; opacity: 0.4;">•</span>
                <span class="text-light" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6; font-weight: 500;">Dual Coverage</span>
              </div>
              <p class="text-light" style="font-size: 0.7rem; opacity: 0.5; margin: 0;">+ ₱1,200/hr</p>
            </div>

            <div class="mb-3 flex-grow-1" style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.25rem;">
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span class="text-light" style="font-size: 0.8rem; line-height: 1.5; font-weight: 300;">100+ Edited Photos</span>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span class="text-light" style="font-size: 0.8rem; line-height: 1.5; font-weight: 300;">3–5 Min Highlight Film</span>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span class="text-light" style="font-size: 0.8rem; line-height: 1.5; font-weight: 300;">Full Event (10–15 min)</span>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span class="text-light" style="font-size: 0.8rem; line-height: 1.5; font-weight: 300;">Audio & Speeches</span>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span class="text-light" style="font-size: 0.8rem; line-height: 1.5; font-weight: 300;">Professional Lighting</span>
              </div>
              <div class="d-flex align-items-start">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span class="text-light" style="font-size: 0.8rem; line-height: 1.5; font-weight: 300;">Gallery Access (3 Months)</span>
              </div>
            </div>

            <div class="mt-auto" style="padding-top: 1.25rem;">
              <a href="logIn.php" class="btn w-100 py-2 btn-book-package" data-package="Premium" data-price="15000" style="background: var(--gold); color: #000; font-size: 0.75rem; letter-spacing: 1.5px; font-weight: 600; border-radius: 6px; transition: all 0.3s ease;">RESERVE NOW</a>
              <p class="text-center mt-2 mb-0 text-light" style="font-size: 0.65rem; opacity: 0.5;">20% deposit required</p>
            </div>
          </div>
        </div>

        <!-- Elite Package -->
        <div class="col-lg-4 col-md-6">
          <div class="package-card h-100 position-relative d-flex flex-column" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; padding: 2rem 1.75rem; transition: all 0.4s ease;">
            <span class="position-absolute top-0 start-0 mt-3 ms-3 px-2 py-1" style="background: rgba(0,0,0,0.05); font-size: 0.6rem; letter-spacing: 2px; color: #1a1a1a; font-weight: 600; border-radius: 4px;">SIGNATURE</span>

            <div class="mb-3" style="padding-top: 1.5rem;">
              <h3 class="serif mb-1" style="font-size: 1.5rem; font-weight: 300; letter-spacing: 0.5px; color: #1a1a1a;">Elite</h3>
              <p class="mb-3" style="font-size: 0.8rem; line-height: 1.5; opacity: 0.6; font-weight: 300;">Complete cinematic experience</p>

              <div class="d-flex align-items-baseline mb-1">
                <span style="font-size: 0.7rem; color: #999; margin-right: 0.25rem;">₱</span>
                <span class="serif" style="font-size: 2rem; font-weight: 300; letter-spacing: -1px; color: var(--gold);">25,000</span>
              </div>
              <div class="d-flex align-items-center gap-2 mb-2">
                <span style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.5px; color: #999; font-weight: 500;">8 hrs coverage</span>
                <span style="font-size: 0.65rem; color: #ccc;">•</span>
                <span style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.5px; color: #999; font-weight: 500;">Full Team</span>
              </div>
              <p style="font-size: 0.7rem; opacity: 0.5; margin: 0;">+ ₱1,500 per extra hour</p>
            </div>

            <div class="mb-3 flex-grow-1" style="border-top: 1px solid rgba(0,0,0,0.05); padding-top: 1.25rem;">
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span style="font-size: 0.8rem; line-height: 1.5; font-weight: 300; color: #4a4a4a;">200 Edited + Raw Photos</span>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span style="font-size: 0.8rem; line-height: 1.5; font-weight: 300; color: #4a4a4a;">7–10 Min Cinematic Film (HD)</span>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span style="font-size: 0.8rem; line-height: 1.5; font-weight: 300; color: #4a4a4a;">Full Event Film (20+ min)</span>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span style="font-size: 0.8rem; line-height: 1.5; font-weight: 500; color: #1a1a1a;">Drone Coverage Included</span>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span style="font-size: 0.8rem; line-height: 1.5; font-weight: 500; color: #1a1a1a;">Same-Day Edit (SDE) Included</span>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span style="font-size: 0.8rem; line-height: 1.5; font-weight: 300; color: #4a4a4a;">Premium Color Grading</span>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span style="font-size: 0.8rem; line-height: 1.5; font-weight: 300; color: #4a4a4a;">Gallery Access (1 Year)</span>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span style="font-size: 0.8rem; line-height: 1.5; font-weight: 300; color: #4a4a4a;">Premium USB + Case</span>
              </div>
              <div class="d-flex align-items-start">
                <span style="width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 0.45rem; margin-right: 0.65rem; flex-shrink: 0;"></span>
                <span style="font-size: 0.8rem; line-height: 1.5; font-weight: 300; color: #4a4a4a;">40-Page Photo Album</span>
              </div>
            </div>

            <div class="mt-auto" style="padding-top: 1.25rem;">
              <a href="logIn.php" class="btn w-100 py-2 btn-book-package" data-package="Elite" data-price="25000" style="background: transparent; color: #1a1a1a; border: 1px solid #1a1a1a; font-size: 0.75rem; letter-spacing: 1.5px; font-weight: 500; border-radius: 6px; transition: all 0.3s ease;">RESERVE NOW</a>
              <p class="text-center mt-2 mb-0" style="font-size: 0.65rem; opacity: 0.5;">20% deposit required</p>
            </div>
          </div>
        </div>

      </div>

      <!-- Comparison Button -->
      <div class="row mt-5">
        <div class="col text-center">
          <button id="toggleComparison" class="btn px-5 py-3" style="background: transparent; color: #1a1a1a; border: 1px solid rgba(0,0,0,0.2); font-size: 0.8rem; letter-spacing: 1.5px; font-weight: 500; border-radius: 6px; transition: all 0.3s ease;">DETAILED COMPARISON</button>
        </div>
      </div>

      <!-- Package Comparison Table -->
      <div id="comparisonTable" class="row mt-5" style="display: none;">
        <div class="col-12">
          <div class="table-responsive">
            <table class="table comparison-table bg-white" style="border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; overflow: hidden;">
              <thead style="background: #fafafa; border-bottom: 1px solid rgba(0,0,0,0.08);">
                <tr>
                  <th class="py-4 ps-4" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: #999;">Feature</th>
                  <th class="text-center py-4" style="font-size: 0.8rem; font-weight: 300; color: #1a1a1a;">Essential<br><small style="font-size: 0.7rem; color: var(--gold);">₱7,500</small></th>
                  <th class="text-center py-4" style="font-size: 0.8rem; font-weight: 300; color: #1a1a1a; background-color: rgba(212, 175, 55, 0.05);">Premium<br><small style="font-size: 0.7rem; color: var(--gold);">₱15,000</small></th>
                  <th class="text-center py-4" style="font-size: 0.8rem; font-weight: 300; color: #1a1a1a;">Elite<br><small style="font-size: 0.7rem; color: var(--gold);">₱25,000</small></th>
                </tr>
              </thead>
              <tbody>
                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                  <td class="ps-4 py-3" style="font-size: 0.85rem; font-weight: 500; color: #1a1a1a;">Coverage Hours</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a;">2 hours</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a; background-color: rgba(212, 175, 55, 0.03);">4 hours</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a;">8 hours</td>
                </tr>
                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                  <td class="ps-4 py-3" style="font-size: 0.85rem; font-weight: 500; color: #1a1a1a;">Edited Photos</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a;">40+</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a; background-color: rgba(212, 175, 55, 0.03);">100+</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a;">200+ (+ Raw)</td>
                </tr>
                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                  <td class="ps-4 py-3" style="font-size: 0.85rem; font-weight: 500; color: #1a1a1a;">Highlight Video</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a;">1-2 min</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a; background-color: rgba(212, 175, 55, 0.03);">3-5 min</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a;">7-10 min (HD)</td>
                </tr>
                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                  <td class="ps-4 py-3" style="font-size: 0.85rem; font-weight: 500; color: #1a1a1a;">Full Event Film</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #ccc;">—</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a; background-color: rgba(212, 175, 55, 0.03);">10-15 min</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a;">20+ min</td>
                </tr>
                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                  <td class="ps-4 py-3" style="font-size: 0.85rem; font-weight: 500; color: #1a1a1a;">Drone Coverage</td>
                  <td class="text-center py-3" style="font-size: 0.75rem; font-weight: 300; color: #999;">Add-on</td>
                  <td class="text-center py-3" style="font-size: 0.75rem; font-weight: 300; color: #999; background-color: rgba(212, 175, 55, 0.03);">Add-on</td>
                  <td class="text-center py-3"><span style="width: 6px; height: 6px; background: var(--gold); border-radius: 50%; display: inline-block;"></span></td>
                </tr>
                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                  <td class="ps-4 py-3" style="font-size: 0.85rem; font-weight: 500; color: #1a1a1a;">Same-Day Edit</td>
                  <td class="text-center py-3" style="font-size: 0.75rem; font-weight: 300; color: #999;">Add-on</td>
                  <td class="text-center py-3" style="font-size: 0.75rem; font-weight: 300; color: #999; background-color: rgba(212, 175, 55, 0.03);">Add-on</td>
                  <td class="text-center py-3"><span style="width: 6px; height: 6px; background: var(--gold); border-radius: 50%; display: inline-block;"></span></td>
                </tr>
                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                  <td class="ps-4 py-3" style="font-size: 0.85rem; font-weight: 500; color: #1a1a1a;">Photo Album</td>
                  <td class="text-center py-3" style="font-size: 0.75rem; font-weight: 300; color: #999;">Add-on</td>
                  <td class="text-center py-3" style="font-size: 0.75rem; font-weight: 300; color: #999; background-color: rgba(212, 175, 55, 0.03);">Add-on</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a;"><span style="width: 6px; height: 6px; background: var(--gold); border-radius: 50%; display: inline-block; margin-right: 0.5rem;"></span>40 pages</td>
                </tr>
                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                  <td class="ps-4 py-3" style="font-size: 0.85rem; font-weight: 500; color: #1a1a1a;">Gallery Access</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a;">1 month</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a; background-color: rgba(212, 175, 55, 0.03);">3 months</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a;">1 year</td>
                </tr>
                <tr>
                  <td class="ps-4 py-3" style="font-size: 0.85rem; font-weight: 500; color: #1a1a1a;">Team Size</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a;">1 Pro</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a; background-color: rgba(212, 175, 55, 0.03);">2 Pros</td>
                  <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a;">4 Pros</td>
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
  <section class="w-100 py-5" style="background: #fafafa;">
    <div class="container py-5">
      <div class="row justify-content-center mb-5">
        <div class="col-md-10 text-center">
          <span class="d-inline-block px-4 py-2 rounded-pill mb-3" style="background: rgba(212, 175, 55, 0.08); border: 1px solid rgba(212, 175, 55, 0.2); font-size: 0.7rem; letter-spacing: 3px; color: var(--gold); font-weight: 600;">ENHANCEMENTS</span>
          <h2 class="serif mb-3" style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 300; line-height: 1.3; letter-spacing: -0.5px; color: #1a1a1a;">Curated Add-Ons</h2>
          <p class="mb-0 mx-auto" style="max-width: 600px; font-size: 0.95rem; line-height: 1.7; opacity: 0.7; font-weight: 300; color: #4a4a4a;">Refine your collection with bespoke enhancements. Each add-on is designed to elevate your experience.</p>
        </div>
      </div>

      <div class="row g-4">

        <!-- Add-on 1 -->
        <div class="col-md-6 col-lg-4">
          <div class="addon-card h-100 position-relative" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; padding: 1.75rem 1.5rem; transition: all 0.4s ease;">
            <div class="mb-2">
              <i class="bi bi-camera-video-fill" style="font-size: 1.75rem; color: var(--gold); opacity: 0.8;"></i>
            </div>
            <h4 class="serif mb-2" style="font-size: 1.35rem; font-weight: 300; letter-spacing: 0.3px; color: #1a1a1a;">Drone Aerial Shots</h4>
            <p class="mb-3" style="font-size: 0.8rem; line-height: 1.6; opacity: 0.6; font-weight: 300; color: #4a4a4a;">Cinematic aerial perspectives capturing the grandeur and scale of your event from above.</p>
            <div class="d-flex align-items-baseline">
              <span style="font-size: 0.7rem; color: #999; margin-right: 0.25rem;">₱</span>
              <span class="serif" style="font-size: 1.6rem; font-weight: 300; letter-spacing: -0.5px; color: var(--gold);">2,000</span>
            </div>
          </div>
        </div>

        <!-- Add-on 2 -->
        <div class="col-md-6 col-lg-4">
          <div class="addon-card h-100 position-relative" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; padding: 1.75rem 1.5rem; transition: all 0.4s ease;">
            <div class="mb-2">
              <i class="bi bi-lightning-charge-fill" style="font-size: 1.75rem; color: var(--gold); opacity: 0.8;"></i>
            </div>
            <h4 class="serif mb-2" style="font-size: 1.35rem; font-weight: 300; letter-spacing: 0.3px; color: #1a1a1a;">Same-Day Edit</h4>
            <p class="mb-3" style="font-size: 0.8rem; line-height: 1.6; opacity: 0.6; font-weight: 300; color: #4a4a4a;">Watch your event's highlight reel during your reception—a truly unforgettable experience.</p>
            <div class="d-flex align-items-baseline">
              <span style="font-size: 0.7rem; color: #999; margin-right: 0.25rem;">₱</span>
              <span class="serif" style="font-size: 1.6rem; font-weight: 300; letter-spacing: -0.5px; color: var(--gold);">3,500</span>
            </div>
          </div>
        </div>

        <!-- Add-on 3 -->
        <div class="col-md-6 col-lg-4">
          <div class="addon-card h-100 position-relative" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; padding: 1.75rem 1.5rem; transition: all 0.4s ease;">
            <div class="mb-2">
              <i class="bi bi-book-fill" style="font-size: 1.75rem; color: var(--gold); opacity: 0.8;"></i>
            </div>
            <h4 class="serif mb-2" style="font-size: 1.35rem; font-weight: 300; letter-spacing: 0.3px; color: #1a1a1a;">Premium Album</h4>
            <p class="mb-3" style="font-size: 0.8rem; line-height: 1.6; opacity: 0.6; font-weight: 300; color: #4a4a4a;">Museum-quality printed album featuring your finest moments. Available in 30 or 40-page editions.</p>
            <div class="d-flex align-items-baseline">
              <span style="font-size: 0.7rem; color: #999; margin-right: 0.25rem;">₱</span>
              <span class="serif" style="font-size: 1.6rem; font-weight: 300; letter-spacing: -0.5px; color: var(--gold);">2,000</span>
            </div>
          </div>
        </div>

        <!-- Add-on 4 -->
        <div class="col-md-6 col-lg-4">
          <div class="addon-card h-100 position-relative" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; padding: 1.75rem 1.5rem; transition: all 0.4s ease;">
            <div class="mb-2">
              <i class="bi bi-clock-history" style="font-size: 1.75rem; color: var(--gold); opacity: 0.8;"></i>
            </div>
            <h4 class="serif mb-2" style="font-size: 1.35rem; font-weight: 300; letter-spacing: 0.3px; color: #1a1a1a;">Extended Coverage</h4>
            <p class="mb-3" style="font-size: 0.8rem; line-height: 1.6; opacity: 0.6; font-weight: 300; color: #4a4a4a;">Additional hours to ensure every moment is documented from beginning to end.</p>
            <div class="d-flex align-items-baseline">
              <span style="font-size: 0.7rem; color: #999; margin-right: 0.25rem;">₱</span>
              <span class="serif" style="font-size: 1.6rem; font-weight: 300; letter-spacing: -0.5px; color: var(--gold);">1,000</span>
              <span style="font-size: 0.75rem; color: #999; margin-left: 0.25rem;">/hr</span>
            </div>
          </div>
        </div>

        <!-- Add-on 5 -->
        <div class="col-md-6 col-lg-4">
          <div class="addon-card h-100 position-relative" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; padding: 1.75rem 1.5rem; transition: all 0.4s ease;">
            <div class="mb-2">
              <i class="bi bi-usb-symbol" style="font-size: 1.75rem; color: var(--gold); opacity: 0.8;"></i>
            </div>
            <h4 class="serif mb-2" style="font-size: 1.35rem; font-weight: 300; letter-spacing: 0.3px; color: #1a1a1a;">Premium USB</h4>
            <p class="mb-3" style="font-size: 0.8rem; line-height: 1.6; opacity: 0.6; font-weight: 300; color: #4a4a4a;">All media delivered on a premium USB drive with custom presentation case.</p>
            <div class="d-flex align-items-baseline">
              <span style="font-size: 0.7rem; color: #999; margin-right: 0.25rem;">₱</span>
              <span class="serif" style="font-size: 1.6rem; font-weight: 300; letter-spacing: -0.5px; color: var(--gold);">500</span>
            </div>
          </div>
        </div>

        <!-- Add-on 6 -->
        <div class="col-md-6 col-lg-4">
          <div class="addon-card h-100 position-relative" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; padding: 1.75rem 1.5rem; transition: all 0.4s ease;">
            <div class="mb-2">
              <i class="bi bi-broadcast" style="font-size: 1.75rem; color: var(--gold); opacity: 0.8;"></i>
            </div>
            <h4 class="serif mb-2" style="font-size: 1.35rem; font-weight: 300; letter-spacing: 0.3px; color: #1a1a1a;">Livestream Setup</h4>
            <p class="mb-3" style="font-size: 0.8rem; line-height: 1.6; opacity: 0.6; font-weight: 300; color: #4a4a4a;">Professional streaming service allowing distant loved ones to share in your celebration.</p>
            <div class="d-flex align-items-baseline">
              <span style="font-size: 0.7rem; color: #999; margin-right: 0.25rem;">₱</span>
              <span class="serif" style="font-size: 1.6rem; font-weight: 300; letter-spacing: -0.5px; color: var(--gold);">3,000</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
  <!--------------------------------------- ADD-ONS SECTION ---------------------------------------->

  <!--------------------------------------- BOOKING DETAILS SECTION ---------------------------------------->
  <section class="w-100 py-5" style="background: linear-gradient(to bottom, #ffffff 0%, #fafafa 100%);">
    <div class="container py-5">
      <div class="row justify-content-center mb-5">
        <div class="col-md-10 text-center">
          <span class="d-inline-block px-4 py-2 rounded-pill mb-3" style="background: rgba(212, 175, 55, 0.08); border: 1px solid rgba(212, 175, 55, 0.2); font-size: 0.7rem; letter-spacing: 3px; color: var(--gold); font-weight: 600;">OUR COMMITMENT</span>
          <h2 class="serif mb-3" style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 300; line-height: 1.3; letter-spacing: -0.5px; color: #1a1a1a;">Service Excellence</h2>
          <p class="mb-0 mx-auto" style="max-width: 600px; font-size: 0.95rem; line-height: 1.7; opacity: 0.7; font-weight: 300; color: #4a4a4a;">We prioritize transparency, flexibility, and exceptional service in every engagement.</p>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="text-center" style="padding: 2rem 1.5rem;">
            <div class="mb-3">
              <span style="width: 50px; height: 50px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid rgba(212, 175, 55, 0.3); color: var(--gold);">
                <i class="bi bi-shield-check" style="font-size: 1.5rem;"></i>
              </span>
            </div>
            <h5 class="mb-2" style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px; color: #1a1a1a;">Secure Booking</h5>
            <p style="font-size: 0.8rem; line-height: 1.6; opacity: 0.6; font-weight: 300; color: #4a4a4a;">20% deposit confirms your date. Balance due before the event.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="text-center" style="padding: 2rem 1.5rem;">
            <div class="mb-3">
              <span style="width: 50px; height: 50px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid rgba(212, 175, 55, 0.3); color: var(--gold);">
                <i class="bi bi-arrow-repeat" style="font-size: 1.5rem;"></i>
              </span>
            </div>
            <h5 class="mb-2" style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px; color: #1a1a1a;">Flexible Rescheduling</h5>
            <p style="font-size: 0.8rem; line-height: 1.6; opacity: 0.6; font-weight: 300; color: #4a4a4a;">Free reschedule with 5+ days notice. Full refund 7+ days prior.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="text-center" style="padding: 2rem 1.5rem;">
            <div class="mb-3">
              <span style="width: 50px; height: 50px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid rgba(212, 175, 55, 0.3); color: var(--gold);">
                <i class="bi bi-clock-history" style="font-size: 1.5rem;"></i>
              </span>
            </div>
            <h5 class="mb-2" style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px; color: #1a1a1a;">Timely Delivery</h5>
            <p style="font-size: 0.8rem; line-height: 1.6; opacity: 0.6; font-weight: 300; color: #4a4a4a;">Edited photos and videos delivered within 1-2 weeks guaranteed.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="text-center" style="padding: 2rem 1.5rem;">
            <div class="mb-3">
              <span style="width: 50px; height: 50px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid rgba(212, 175, 55, 0.3); color: var(--gold);">
                <i class="bi bi-credit-card" style="font-size: 1.5rem;"></i>
              </span>
            </div>
            <h5 class="mb-2" style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px; color: #1a1a1a;">Multiple Payment Options</h5>
            <p style="font-size: 0.8rem; line-height: 1.6; opacity: 0.6; font-weight: 300; color: #4a4a4a;">GCash, Maya, Bank Transfer, or PayPal accepted.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--------------------------------------- BOOKING DETAILS SECTION ---------------------------------------->

  <!--------------------------------------- GALLERY SECTION ---------------------------------------->
  <section class="w-100 py-5" style="background: #0a0a0a;" id="gallery">
    <div class="container py-5">
      <div class="row justify-content-center mb-5">
        <div class="col-md-10 text-center">
          <span class="d-inline-block px-4 py-2 rounded-pill mb-3" style="background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3); font-size: 0.7rem; letter-spacing: 3px; color: var(--gold); font-weight: 600;">PORTFOLIO</span>
          <h2 class="serif mb-3 text-light" style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 300; line-height: 1.3; letter-spacing: -0.5px;">Our Recent Work</h2>
          <p class="mb-0 mx-auto text-light" style="max-width: 600px; font-size: 0.95rem; line-height: 1.7; opacity: 0.7; font-weight: 300;">A glimpse into the moments we've captured and the stories we've told for our clients.</p>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-4 col-md-6">
          <div class="gallery-item position-relative overflow-hidden" style="border-radius: 8px; height: 350px;">
            <img src="./assets/pexels-emma-bauso-1183828-2253831.jpg" alt="Wedding Photography" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;">
            <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
              <p class="text-light mb-1" style="font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase; opacity: 0.8;">Weddings</p>
              <h4 class="text-light serif mb-0" style="font-size: 1.25rem; font-weight: 300;">Intimate Garden Ceremony</h4>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="gallery-item position-relative overflow-hidden" style="border-radius: 8px; height: 350px;">
            <img src="./assets/bride-groom-couple-wedding.jpg" alt="Corporate Event" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;">
            <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
              <p class="text-light mb-1" style="font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase; opacity: 0.8;">Corporate</p>
              <h4 class="text-light serif mb-0" style="font-size: 1.25rem; font-weight: 300;">Annual Gala Evening</h4>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="gallery-item position-relative overflow-hidden" style="border-radius: 8px; height: 350px;">
            <img src="./assets/pexels-mikhail-nilov-7534800.jpg" alt="Birthday Celebration" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;">
            <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
              <p class="text-light mb-1" style="font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase; opacity: 0.8;">Celebrations</p>
              <h4 class="text-light serif mb-0" style="font-size: 1.25rem; font-weight: 300;">Milestone Birthday</h4>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="gallery-item position-relative overflow-hidden" style="border-radius: 8px; height: 350px;">
            <img src="./assets/pexels-cottonbro-5077049.jpg" alt="Debut Photography" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;">
            <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
              <p class="text-light mb-1" style="font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase; opacity: 0.8;">Debuts</p>
              <h4 class="text-light serif mb-0" style="font-size: 1.25rem; font-weight: 300;">Elegant Coming of Age</h4>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="gallery-item position-relative overflow-hidden" style="border-radius: 8px; height: 350px;">
            <img src="./assets/close-up-teenage-boy-taking-photography-click-retro-vintage-photo-camera-against-white-background.jpg" alt="Creative Shoot" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;">
            <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
              <p class="text-light mb-1" style="font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase; opacity: 0.8;">Creative</p>
              <h4 class="text-light serif mb-0" style="font-size: 1.25rem; font-weight: 300;">Fashion Editorial</h4>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="gallery-item position-relative overflow-hidden" style="border-radius: 8px; height: 350px;">
            <img src="./assets/pexels-rdne-7648020.jpg" alt="Product Photography" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;">
            <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
              <p class="text-light mb-1" style="font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase; opacity: 0.8;">Commercial</p>
              <h4 class="text-light serif mb-0" style="font-size: 1.25rem; font-weight: 300;">Brand Campaign</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--------------------------------------- GALLERY SECTION ---------------------------------------->

  <!--------------------------------------- CTA SECTION ---------------------------------------->
  <section class="cta-section w-100 d-flex justify-content-center align-items-center position-relative" style="min-height: 60vh; background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%); padding: 6rem 0;">
    <div class="position-absolute w-100 h-100" style="opacity: 0.02; background-image: url('./assets/wp2815597-black-texture-wallpapers.jpg'); background-size: cover; background-position: center;"></div>

    <div class="container text-center position-relative" style="z-index: 2;">
      <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
          <span class="d-inline-block px-4 py-2 rounded-pill mb-4" style="background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3); font-size: 0.7rem; letter-spacing: 3px; color: var(--gold); font-weight: 600;">BEGIN YOUR JOURNEY</span>
          <h2 class="serif mb-4 text-light" style="font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 300; line-height: 1.2; letter-spacing: -0.5px;">Ready to Preserve<br><span style="font-style: italic; color: var(--gold);">Your Story?</span></h2>
          <p class="text-light mb-5 mx-auto" style="max-width: 650px; font-size: 0.95rem; line-height: 1.8; opacity: 0.7; font-weight: 300;">From intimate celebrations to grand occasions, we're here to document your moments with artistry, professionalism, and uncompromising attention to detail.</p>

          <div class="d-flex flex-column flex-md-row gap-3 justify-content-center mb-4">
            <a href="logIn.php" class="btn px-5 py-3" style="background-color: var(--gold); color: #000; font-weight: 500; letter-spacing: 1px; border-radius: 6px; font-size: 0.85rem; transition: all 0.4s ease;">Book now</a>
            <a href="aboutCompany.php" class="btn px-5 py-3" style="background: transparent; color: var(--gold); border: 1px solid var(--gold); font-weight: 500; letter-spacing: 1px; border-radius: 6px; font-size: 0.85rem; transition: all 0.4s ease;">About us</a>
          </div>

          <div class="mt-5 pt-4" style="border-top: 1px solid rgba(255,255,255,0.1);">
            <p class="text-light mb-3" style="font-size: 0.75rem; opacity: 0.5; letter-spacing: 1px; text-transform: uppercase;">Important Details</p>
            <div class="row g-4">
              <div class="col-md-4">
                <p class="text-light mb-1" style="font-size: 0.85rem; font-weight: 300; opacity: 0.8;">20% deposit to secure booking</p>
              </div>
              <div class="col-md-4">
                <p class="text-light mb-1" style="font-size: 0.85rem; font-weight: 300; opacity: 0.8;">Free reschedule with 5+ days notice</p>
              </div>
              <div class="col-md-4">
                <p class="text-light mb-1" style="font-size: 0.85rem; font-weight: 300; opacity: 0.8;">Delivery within 1-2 weeks</p>
              </div>
            </div>
          </div>
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
          this.textContent = 'CLOSE COMPARISON';
          comparisonTable.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
          comparisonTable.style.display = 'none';
          this.textContent = 'DETAILED COMPARISON';
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
