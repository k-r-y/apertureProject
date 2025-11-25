<?php

require_once 'includes/functions/session.php';


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="./assets/camera.png" type="image/x-icon">
  <link rel="stylesheet" href="../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
  <title>Aperture</title>
</head>

<body class="">
  <!------------------------------------------- NAV ------------------------------------------------>
  <?php include './includes/header.php'; ?>

  <!------------------------------------------- NAV ------------------------------------------------>


  <!--------------------------------------- HOME SECTION-------------------------------------------->





  <section id="home" class="home w-100 min-vh-100 bg-dark position-relative d-flex justify-content-center align-items-center">

    <div class="overlay"></div>
    <div class="container position-absolute text-center text-light d-flex align-items-center justify-content-center flex-column gap-3" id="homeText">
      <div class="m-0">
            <small class="d-inline-block py-2 px-3 rounded-pill bg-opacity-10 bg-light "  style="border: 1px solid var(--light);">Aperture Studios</small>
        </div>
      <h1 class="display-1 w-75 fw-semibold serif m-0 text-light">Capture Every Moment, Book Every Memory</h1>
      <div class="d-flex flex-column flex-md-row gap-3">
        <a href="login.php" class="btn border-light px-4 text-light rounded">Book Now</a>
        <a href="aboutCompany.php" class="btn bg-light text-dark px-4 rounded">Learn More</a>
      </div>
    </div>

  </section>

  <!--------------------------------------- HOME SECTION-------------------------------------------->

  <!--------------------------------------- Problem Statement SECTION-------------------------------------------->


  <section class=" py-5 min-vh-100 w-100 d-flex flex-column gap-5 position-relative justify-content-center align-items-center" id="problem">

    <div class="container p-md-4 p-2 d  m-0 bg-white rounded-5">
      <div class="row g-lg-5 g-3 p-2 p-md-5 d-flex justify-content-center align-items-center">

        <div class=" col-lg-6 m-0 py-2">

          <h2 class="fw-bolder serif">Finding the Perfect Photographer Shouldn't Be This Hard</h2>

          <p>You have a vision for your special day, but finding the right creative professional who understands your style, is available on your date, and fits your budget feels impossible.</p>


          <ul class="list-unstyled mt-4">
            <li class="mb-2">
              <img src="./assets/x.png" class="me-2" alt="" style="width: 1rem; height: 1rem;">Endless searching through countless portfolios
            </li>

            <li class="mb-2">
              <img src="./assets/x.png" class="me-2" alt="" style="width: 1rem; height: 1rem;">Uncertainty about pricing and hidden fees
            </li>

            <li class="mb-2">
              <img src="./assets/x.png" class="me-2" alt="" style="width: 1rem; height: 1rem;">No real-time availability information
            </li>

            <li class="mb-2">
              <img src="./assets/x.png" class="me-2" alt="" style="width: 1rem; height: 1rem;">Difficulty comparing styles and specialties
            </li>

            <li class="">
              <img src="./assets/x.png" class="me-2" alt="" style="width: 1rem; height: 1rem;">Stress about delivery timelines and quality
            </li>
          </ul>

        </div>

        <div class=" col-lg-6 m-0">
          <img src="./assets/high-angle-photo-camera-indoors-still-life.jpg" alt="" class="img-fluid rounded-5" loading="lazy">
        </div>

      </div>
    </div>


    <div class="container m-0 bg-white p-md-5 p-4 rounded-5">
      <div class="row mb-5">
        <div class="text-center mx-auto col-lg-8">
          <h2 class="fw-bolder serif">Easy Booking for Photographers and Videographers</h2>
          <p class="text-secondary">We help you find, compare, and book trusted professionals with clear prices and real-time availability. Quality work and on-time delivery are guaranteed.</p>
        </div>

      </div>

      <div class="row solutionGroup gap-3 p-md-3">
        <div class="card solutionCard  px-2 py-4 shadow border-0">
          <img src="./assets/pro.png" alt="" class="card-img-top mx-auto" style="width: 4rem;" loading="lazy">
          <div class="card-body text-center">
            <h4 class="card-title serif">Trusted Professionals</h4>
            <small class="card-text text-secondary">We connect you with experienced photographers and videographers you can rely on.</small>
          </div>
        </div>

        <div class="card solutionCard px-2 py-4 shadow border-0">
          <img src="./assets/price.png" alt="" class="card-img-top mx-auto" style="width: 4rem;" loading="lazy">
          <div class="card-body text-center">
            <h4 class="card-title serif">Clear Pricing</h4>
            <small class="card-text text-secondary">See all prices upfront with no hidden fees, so you can plan your budget easily.</small>
          </div>
        </div>

        <div class="card solutionCard  px-2 py-4 shadow border-0">
          <img src="./assets/booking.png" alt="" class="card-img-top mx-auto" style="width: 4rem;" loading="lazy">
          <div class="card-body text-center">
            <h4 class="card-title serif">Real-Time Booking</h4>
            <small class="card-text text-secondary">Check availability and book your preferred professional instantly online.</small>
          </div>
        </div>


        <div class="card solutionCard  px-2 py-4 shadow border-0">
          <img src="./assets/compare.png" alt="" class="card-img-top mx-auto" style="width: 4rem;" loading="lazy">
          <div class="card-body text-center">
            <h4 class="card-title serif">Easy Style Comparison</h4>
            <small class="card-text text-secondary">Filter and compare creatives by style, specialty, and reviews to find your perfect match.</small>
          </div>
        </div>

        <div class="card solutionCard  px-2 py-4 shadow border-0">
          <img src="./assets/quality.png" alt="" class="card-img-top mx-auto" style="width: 4rem;" loading="lazy">
          <div class="card-body text-center">
            <h4 class="card-title serif">Quality & On-Time Delivery</h4>
            <small class="card-text text-secondary">We guarantee high-quality photos and videos delivered on schedule.</small>
          </div>
        </div>

      </div>



    </div>
  </section>

  <!------------------------------------- SERVICES SECTION-------------------------------------------->


  <section class="w-100 py-5  min-vh-100 d-flex flex-column justify-content-center align-items-center " id="services">


    <div class="container py-5 px-4  m-0 bg-light justify-content-center align-items-center rounded-5 shadow">


      <div class="row gap-2 mb-2 justify-content-center">
        <div class="col-lg-5 rounded align-content-end p-3">
          <h1 class="fw-bold serif">Personalized Sessions for Life's Milestones</h1>
          <p>Whether it's a romantic engagement or a family celebration, our services ensure high-quality captures with expert editing and on-time delivery. Start with our 4-step booking process for hassle-free planning.</p>
        </div>

        <div class="col-lg-3 col-md-5 bg-secondary rounded-4 position-relative">
          <img src="./assets/pexels-emma-bauso-1183828-2253831.jpg" alt="" loading="lazy">
          <h6 class="bg-light p-1 rounded">Weddings & Engagements</h6>
        </div>

        <div class="col-lg-3 col-md-5 bg-secondary rounded-4 position-relative">
          <img src="./assets/pexels-rdne-7648020.jpg" alt="" loading="lazy">
          <h6 class="bg-light p-1 rounded">Creative Shoots</h6>
        </div>

      </div>

      <div class="row gap-2 mb-2 justify-content-center">

        <div class="col-md-3 bg-secondary rounded-4 position-relative">
          <img src="./assets/pexels-jessbaileydesign-768472.jpg" alt="" loading="lazy">
          <h6 class="bg-light p-1 rounded">Birthdays & Celebrations</h6>
        </div>

        <div class="col-md-5 bg-secondary rounded-4 position-relative">
          <img src="./assets/pexels-cottonbro-5077049.jpg" alt="" loading="lazy">
          <h6 class="bg-light p-1 rounded">Corporate Events</h6>
        </div>

        <div class="col-md-3 bg-secondary rounded-4 position-relative">
          <img src="./assets/high-angle-photo-camera-indoors-still-life.jpg" alt="" loading="lazy">
          <h6 class="bg-light p-1 rounded">Behind the Lens</h6>
        </div>
      </div>

    </div>

  </section>


  <!------------------------------------- SERVICES SECTION-------------------------------------------->

  <!------------------------------------- FEATURES SECTION-------------------------------------------->

  <section id="features" class="px-2 py-5 position-relative bg-white  w-100 min-vh-100 justify-content-center align-content-center">
    <div class="container">

      <div class="row justify-content-center mb-5">
        <div class="col-md-8 text-center">
          <h1 class="display-4 serif">Powerful Features Designed for Your Needs</h1>
          <p>Our platform includes everything you need to find, book, and work with the perfect photographer or videographer for your event.</p>
        </div>
      </div>

      <div class="row justify-content-center gap-3">
        <div class="card col-md-5 px-3 py-4 shadow border-0 justify-content-start">
          <img src="./assets/cam.png" alt="" class="card-img-top mb-3" style="width: 4rem;">
          <div class="card-body text-start p-0">
            <h4 class="card-title serif">Professional Photography</h4>
            <p class="card-text serif">High-quality photos with expert composition, lighting, and editing to preserve your memories.</p>
          </div>
        </div>

        <div class="card col-md-5 px-3 py-4 shadow border-0">
          <img src="./assets/vid.png" alt="" class="card-img-top mb-3" style="width: 4rem;">
          <div class="card-body text-start p-0">
            <h4 class="card-title serif">Creative Videography</h4>
            <p class="card-text serif">Engaging videos that tell your story with cinematic techniques and smooth editing.</p>
          </div>
        </div>

        <div class="card col-md-5 px-3 py-4 shadow border-0">
          <img src="./assets/edit.png" alt="" class="card-img-top mb-3" style="width: 4rem;">
          <div class="card-body text-start p-0">
            <h4 class="card-title serif">Custom Editing</h4>
            <p class="card-text serif">Tailored photo retouching and video editing to match your style and vision.</p>
          </div>
        </div>

        <div class="card col-md-5 px-3 py-4 shadow border-0">
          <img src="./assets/event.png" alt="" class="card-img-top mb-3" style="width: 4rem;">
          <div class="card-body text-start p-0">
            <h4 class="card-title serif">Event Coverage</h4>
            <p class="card-text serif">Comprehensive coverage for weddings, corporate events, parties, and more.</p>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!------------------------------------- FEATURES SECTION-------------------------------------------->

  <!------------------------------------- How it works SECTION-------------------------------------------->

  <section class="py-5 bg-dark howItWorks" id="howItWorks">
    <div class="container">
      <div class="row justify-content-center mb-5">
        <div class="col-md-8 text-center text-light">
          <h1 class="display-4 serif">How it works</h1>
          <p class="text-light">Booking your perfect photographer or videographer has never been easier. Follow these simple steps to capture every moment and create lasting memories.</p>
        </div>
      </div>
      <div class="row justify-content-center align-items-center gap-2">

        <div class="col-md-3 text-center shadow p-2 rounded">
          <img src="./assets/1.png" alt="" style="width: 3rem;">
          <h4 class="my-3 serif">Submit Your Request</h4>
          <p>Fill out a simple form with your event details and preferred date.</p>
        </div>
        <div class="col-md-3 text-center shadow p-2 rounded">
          <img src="./assets/2.png" alt="" style="width: 3rem;">
          <h4 class="my-3 serif">We Review & Confirm</h4>
          <p>Our team reviews your request and confirms availability before approval.</p>
        </div>
        <div class="col-md-3 text-center shadow p-2 rounded">
          <img src="./assets/3.png" alt="" style="width: 3rem;">
          <h4 class="my-3 serif">Booking Approved</h4>
          <p>Receive confirmation once your appointment is secured.</p>
        </div>

        <div class="col-md-3 text-center shadow p-2 rounded">
          <img src="./assets/4.png" alt="" style="width: 3rem;">
          <h4 class="my-3 serif">Enjoy Your Event</h4>
          <p>Relax and let our professionals capture your special moments perfectly.</p>
        </div>
      </div>

    </div>
  </section>

  <!------------------------------------- How it works SECTION-------------------------------------------->

  

  <!------------------------------------- ABOUT COMPANY SECTION-------------------------------------------->



  <section class="w-100  py-5 px-2 bg-white d-flex justify-content-center align-content-center position-relative">
    <div class="container m-0">
      <div class="row justify-content-center mb-5">
        <div class="col-md-10">
          <h1 class="display-4 text-center serif">What Our Clients Say</h1>
          <p class="text-center">Real stories from users who've trusted Aperture for their moments—proving our commitment to seamless bookings and stunning results.</p>
        </div>
      </div>

      <div class="row justify-content-center g-3" id="testimonialRow">
        <div class="col col-md-4">
          <div class="card">
            <div class="card-header d-flex justify-content-center align-items-center gap-2">
              <img src="./assets/pic.png" alt="" class="img-thumbnail rounded-circle" style="max-width: 6rem;">
              <div>
                <h5 class="serif m-0">Prince Andrew Casiano</h5>
                <small class="text-secondary serif">CEO, Unishare</small>
              </div>

            </div>
            <div class="card-body">
              <small class="text-secondary">"For our company launch, Aperture's style filters helped us pick the perfect videographer quickly. No hidden fees, and the Premium coverage (&#8369;1000/hr) delivered polished videos in a week—our team loved the quality and ease!"</small>
            </div>
          </div>
        </div>
        <div class="col col-md-4">
          <div class="card">
            <div class="card-header d-flex justify-content-center align-items-center gap-2">
              <img src="./assets/conosido.png" alt="" class="img-thumbnail rounded-circle" style="max-width: 6rem;">
              <div>
                <h5 class="serif m-0">Dionilo Conosido</h5>
                <small class="text-secondary serif">Client from Manila</small>
              </div>

            </div>
            <div class="card-body">
              <small class="text-secondary">"Booking for my son's birthday was effortless—Aperture's 4-step process confirmed everything in hours. The Basic package (&#8369;2000/hr) captured all the fun moments with quick edits. Affordable and reliable for family events!"</small>
            </div>
          </div>
        </div>
        <div class="col col-md-4">
          <div class="card">
            <div class="card-header d-flex justify-content-center align-items-center gap-2">
              <img src="./assets/centino.png" alt="" class="img-thumbnail rounded-circle " style="max-width: 6rem;">
              <div>
                <h5 class="serif m-0">Mark Anthony Centino</h5>
                <small class="text-secondary serif">Client from Quezon City</small>
              </div>

            </div>
            <div class="card-body">
              <small class="text-secondary">"Aperture made booking a behind-the-lens session straightforward—no availability hassles or extra costs. The Premium pro (&#8369;1000/hr) captured my portfolio perfectly with advanced edits. Trustworthy platform for aspiring creatives."</small>
            </div>
          </div>
        </div>
      </div>





    </div>

  </section>

  <!------------------------------------- FAQ SECTION-------------------------------------------->

  <section class="w-100 py-5 min-vh-100" id="faq">

    <div class="container">

      <div class="row justify-content-center mb-5">
        <div class="col-md-10">
          <h1 class="m-0 text-center display-4 serif">Frequently Asked Questions</h1>
          <p class="text-center">Find answers to common questions about our photography and videography services, pricing, and booking process. We're here to assist you with any additional inquiries.</p>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="accordion col-md-8" id="faqAccordion">

          <!-- item 1 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#firstQuestion" aria-expanded="true" aria-controls="firstQuestion">
                How does the Aperture booking system work?
              </button>
            </h2>

            <div id="firstQuestion" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>Aperture uses a simple 4-step process: (1) Submit your booking request with event details, (2) Our team reviews and confirms photographer availability, (3) Receive booking approval and confirmation, (4) Enjoy your event while our professionals capture every moment.</p>
              </div>
            </div>
          </div>

          <!-- item 2 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#secondQuestion" aria-expanded="false" aria-controls="secondQuestion">
                Can I only book one appointment per day?
              </button>
            </h2>

            <div id="secondQuestion" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>Yes, to ensure our photographers can dedicate their full attention and deliver the highest quality service, we allow only one booking per client per day. This policy helps us maintain our commitment to excellence.</p>
              </div>
            </div>
          </div>

          <!-- item 3 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#thirdQuestion" aria-expanded="false" aria-controls="thirdQuestion">
                How long does it take to get booking approval?
              </button>
            </h2>

            <div id="thirdQuestion" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>Our admin team typically reviews and approves booking requests within 24-48 hours. You'll receive an email notification once your booking status changes. You can also track your booking status in real-time through your user dashboard.</p>
              </div>
            </div>
          </div>

          <!-- item 4 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fourthQuestion" aria-expanded="false" aria-controls="fourthQuestion">
                When will I receive my photos or videos?
              </button>
            </h2>

            <div id="fourthQuestion" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>Delivery times vary by event type and package. Typically, you'll receive your professionally edited photos within 7-14 days. Videos may take 14-21 days for complete editing. All deliverables are uploaded to your secure online gallery accessible through your dashboard.</p>
              </div>
            </div>
          </div>

          <!-- item 5 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fifthQuestion" aria-expanded="false" aria-controls="fifthQuestion">
                What types of events can I book through Aperture?
              </button>
            </h2>

            <div id="fifthQuestion" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>Aperture supports a wide range of events including Weddings & Engagements, Birthdays & Celebrations, Corporate Events, Creative Shoots, Product Photography, and Behind-the-Lens sessions. Simply select your event type when submitting your booking request.</p>
              </div>
            </div>
          </div>

          <!-- item 6 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sixthQuestion" aria-expanded="false" aria-controls="sixthQuestion">
                How can I track my booking status?
              </button>
            </h2>

            <div id="sixthQuestion" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>After logging into your Aperture account, navigate to "My Appointments" in your dashboard. Here you can view all your bookings with real-time status updates (Pending, Approved, Completed, or Cancelled), booking details, and access your photo gallery once photos are delivered.</p>
              </div>
            </div>
          </div>

          <!-- item 7 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#seventhQuestion" aria-expanded="false" aria-controls="seventhQuestion">
                What payment methods do you accept?
              </button>
            </h2>

            <div id="seventhQuestion" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>We accept various payment methods including bank transfers, GCash, PayMaya, and credit/debit cards. Payment details and instructions will be provided once your booking is approved by our admin team.</p>
              </div>
            </div>
          </div>

          <!-- item 8 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#eighthQuestion" aria-expanded="false" aria-controls="eighthQuestion">
                Can I cancel or reschedule my booking?
              </button>
            </h2>

            <div id="eighthQuestion" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>Yes, you can request cancellations or rescheduling through your dashboard or by contacting our support team. Cancellations made 7+ days before the event receive a full refund. Cancellations within 7 days may incur a fee. Rescheduling is subject to photographer availability.</p>
              </div>
            </div>
          </div>

          <!-- item 9 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ninthQuestion" aria-expanded="false" aria-controls="ninthQuestion">
                How do I access my photos after the event?
              </button>
            </h2>

            <div id="ninthQuestion" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>Once your photos are ready, you'll receive an email notification. Log into your Aperture account and go to "My Photos" to view and download your professionally edited images. All photos are delivered in high-resolution format and stored securely in your online gallery.</p>
              </div>
            </div>
          </div>

          <!-- item 10 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tenthQuestion" aria-expanded="false" aria-controls="tenthQuestion">
                Do I need to create an account to book?
              </button>
            </h2>

            <div id="tenthQuestion" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>Yes, creating a free Aperture account is required to submit booking requests. Your account gives you access to your personalized dashboard where you can manage bookings, track status updates, view appointments, and access your photo gallery—all in one convenient place.</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

  <!------------------------------------- FAQ SECTION-------------------------------------------->


  <?php include './includes/footer.php'; ?>

  <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('user/api/reviews_api.php?action=get_approved_reviews')
                .then(r => r.json())
                .then(data => {
                    const grid = document.getElementById('testimonialsGrid');
                    if(data.success && data.reviews.length > 0) {
                        grid.innerHTML = data.reviews.map(r => `
                            <div class="col-md-4">
                                <div class="card bg-dark border-secondary h-100">
                                    <div class="card-body p-4">
                                        <div class="mb-3 text-warning">
                                            ${Array(5).fill(0).map((_, i) => 
                                                `<i class="bi bi-star${i < r.rating ? '-fill' : ''}"></i>`
                                            ).join('')}
                                        </div>
                                        <p class="card-text text-light fst-italic mb-4">"${r.comment}"</p>
                                        <div class="d-flex align-items-center">
                                            <div class="ms-0">
                                                <h6 class="text-gold mb-0 font-serif">${r.FirstName} ${r.LastName}</h6>
                                                <small class="text-muted">${r.event_type}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        grid.innerHTML = '<div class="col-12 text-center text-muted">No reviews yet. Be the first!</div>';
                    }
                });
        });
    </script>
</body>

</html>